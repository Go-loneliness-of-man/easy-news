<?php
  function index(){
    echo '<!DOCTYPE html><html lang="zh-CN"><head><title>简易新闻站</title><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"><style></style><link rel="stylesheet" href="public/css/index.css"></head><body><div id="backtoptarget"></div><header><ul>';
    $nav=array('yw'=>'要闻','yl'=>'娱乐','ty'=>'体育','js'=>'军事','nba'=>'NBA','gj'=>'国际','kj'=>'科技');
    if(!(isset($_REQUEST['lx'])))                                   //不存在 lx 参数，默认要闻
      echo '<li><a href="http://localhost/news/index.php?lx=yw" class="clicked">要闻</a></li><li><a href="http://localhost/news/index.php?lx=yl">娱乐</a></li><li><a href="http://localhost/news/index.php?lx=ty">体育</a></li><li><a href="http://localhost/news/index.php?lx=js">军事</a></li><li><a href="http://localhost/news/index.php?lx=nba">NBA</a></li><li><a href="http://localhost/news/index.php?lx=gj">国际</a></li><li><a href="http://localhost/news/index.php?lx=kj">科技</a></li>';
    else
      foreach($nav as $li)                                          //生成列表项
      {
        if($_REQUEST['lx']==array_keys($nav,$li)[0])                //若键名与类型名对应
          echo '<li><a href="http://localhost/news/index.php?lx='.$_REQUEST['lx'].'" class="clicked">'.$li.'</a></li>';
        else echo '<li><a href="http://localhost/news/index.php?lx='.array_keys($nav,$li)[0].'">'.$li.'</a></li>';
      }
    echo '</ul><div id="login">登录</div><div id="user"></div></header><div id="body"><a href="#backtoptarget" id="backtop"></a><section id="main"><h2>今日热点</h2><hr><div id="lb">';
    
    //获取数据
    $line = new PDO('mysql:host=localhost;', 'root', '123');        //连接 mysql
    $re = $line->prepare('use news');                               //选择 news 数据库
    $re->execute();
    if(isset($_REQUEST['lx']))                                      //若未指定新闻类别，默认为要闻
      $sechfigure = 'select *,unix_timestamp()-unix_timestamp(time) from '.$_REQUEST['lx'].' order by time desc';
    else
    {
      $sechfigure = 'select *,unix_timestamp()-unix_timestamp(time) from yw order by time desc';
      $_REQUEST['lx']='yw';
    }
    $re = $line->prepare($sechfigure);
    $re->execute();                                                 //执行
    $news = $re->fetchall();                                        //获取所有记录
    $re = $line->prepare('select id,st from news.zt');              //查询专题
    $re->execute();
    $zt = $re->fetchall();                                          //获取所有专题的视图查询语句
    $id = array('lx'=>$_REQUEST['lx']);                             //对应类型的专题数组
    for($i = $j = 0, $c = count($zt); $i < $c; $i++)                //将对应类型的专题新闻 id 整理到同一个数组
    {
      preg_match_all('/select \* from (.{1,3}) where id=(.{1,5})\)/i', $zt[$i]['st'], $ztid, PREG_PATTERN_ORDER);
      for($k = 0, $c2 = count($ztid[1]); $k < $c2; $k++)
        if($ztid[1][$k] == $id['lx'])
        {
          $id[$j]['ztid'] = $zt[$i]['id'];
          $id[$j++]['id'] = $ztid[2][$k];
        }
    }
    $c10 = count($id) - 1;
    $news2=[];

    //生成轮播图 figure，要求为日期最近的 3 ~ 5 条记录，最少取 3 条，满足 3 条后超出 3 天的不取
    for($i = $k = $j = 0, $c = count($news); $i < $c && $k < 5; $i++)//生成 figure，每条记录必须含图片，$k 用于计算符合条件的记录数
      if(preg_match('/<img.{2,20}(public\/img\/.{2,40})">/i',$news[$i]['text'],$src))             //查找到第一张图片的路径
      {
        $k++;
        preg_match('/<p>([^<,^>]{80,200})<\/p>/i',$news[$i]['text'],$news[$i]['text']);           //取出 text 中连续 40 ~ 100 个的汉字作为详情
        if($k > 3 && $news[$i]['unix_timestamp()-unix_timestamp(time)']/3600/24 > 3)   break;     //获取到 3 条，时间已超过 7 天，跳出
        echo '<figure><a href="'.'http://localhost/news/index.php?m=danye&lx='.$_REQUEST['lx'].'&id='.$news[$i]['id'].'" target="_blank"><img src="'.$src[1].'"></a><figcaption><a href="'.'http://localhost/news/index.php?m=danye&lx='.$_REQUEST['lx'].'&id='.$news[$i]['id'].'" class="title">'.$news[$i]['title'].'</a><a href="'.'http://localhost/news/index.php?m=danye&lx='.$_REQUEST['lx'].'&id='.$news[$i]['id'].'" class="text" target="_blank">'.@$news[$i]['text'][1].'</a></figcaption></figure>'.'<div style="display:none;" class="xwjlid">'.$news[$i]['id'].'</div>';
      }
      else                                                            //不含图片的记录，存储到另一个数组
      {
        $news2[$j] = $news[$i];
        $j++;
      }

    //去掉已经被轮播图采用的记录，打乱顺序，并分类为有图、无图两种
    for($news3 = [], $k = 0; $i < $c; $i++, $k++)  $news3[$k] = $news[$i];                        //拷贝剩余部分
    $c = count($news2);
    for($news = [],$i = 0; $i < $c; $i++, $k++)  $news3[$k] = $news2[$i];                         //拼接剩余无图部分
    shuffle($news3);                                                                              //打乱顺序
    for($i = $j = $k = 0, $c = count($news3); $i < $c; $i++)                                      //分为有图、无图两类
      if(@preg_match('/<img.{2,20}(public\/img\/.{2,40})">/i',$news3[$i]['text']))                //查找是否含有图片路径
        $news[$j++] = $news3[$i];                                     //有图
      else  $news2[$k++] = $news3[$i];                                //无图，分类到另一组

    //生成主栏热点精选 figure
    echo '<div id="l"></div><div id="r"></div></div><div id="hot"><h2>近期精选</h2><hr><div id ="rdjlk">';
    for($i = 0, $c = count($news); $i < $c && $i < 4; $i++)           //生成 figure
    {
      preg_match('/<img.{2,20}(public\/img\/.{2,40})">/i',$news[$i]['text'],$src);                //查找到第一张图片的路径
      for($bj = $m = 0; $m < $c10; $m++)                              //检测该新闻是否位于专题内
        if($news[$i]['id'] == $id[$m]['id'])                          //检测到，保存 ztid，跳出
        {
          $bj = $id[$m]['ztid'] + 1;
          break;
        }
      if($bj)                                                         //文章分类
        echo '<figure class="zt"><img src="';
      else if($news[$i]['click'] > 5)
        echo '<figure class="rd"><img src="';
      else
        echo '<figure class="pt"><img src="';
      echo $src[1].'"><figcaption><div id="bt">';
      if($bj)                                                         //专题
        echo '专题</div><a href="'.'http://localhost/news/index.php?m=zt&ztid='.($bj-1).'" target="_blank">';
      else                                                            //不是专题
        echo '</div><a href="http://localhost/news/index.php?m=danye&lx='.$_REQUEST['lx'].'&id='.$news[$i]['id'].'" target="_blank">';
      echo $news[$i]['title'].'</a><span class="time">';
      if(($news[$i]['unix_timestamp()-unix_timestamp(time)']/3600/24) > 1)
        echo ((int)($news[$i]['unix_timestamp()-unix_timestamp(time)']/3600/24)).' 天前';
      else if(($news[$i]['unix_timestamp()-unix_timestamp(time)']/3600) > 1)
        echo ((int)($news[$i]['unix_timestamp()-unix_timestamp(time)']/3600)).' 小时前';
      else  echo ((int)($news[$i]['unix_timestamp()-unix_timestamp(time)']/60)).' 分钟前';
      echo '</span><a href="" class="share">分享</a><a href="'.'http://localhost/news/index.php?m=danye&lx='.$_REQUEST['lx'].'&id='.$news[$i]['id'].'#theend" target="_blank" class="pl">'.$news[$i]['count'].'</a></figcaption></figure>'.'<div style="display:none;" class="xwjlid">'.$news[$i]['id'].'</div>';
    }

    //生成侧栏话题 figure 和 ul
    echo '</div><div id="ckgdnew">查看更多</div></div></section><section id="aside"><div id="ht"><h2>话题</h2>';
    if(preg_match('/<img.{2,20}(public\/img\/.{2,40})">/i',$news[$i]['text'],$src))               //查找到第一张图片的路径
      echo '<figure><img src="'.$src[1].'"><figcaption><a href="'.'http://localhost/news/index.php?m=danye&lx='.$_REQUEST['lx'].'&id='.$news[$i]['id'].'" target="_blank">'.$news[$i]['title'].'</a></figcaption></figure>'.'<div style="display:none;" class="xwjlid">'.$news[$i]['id'].'</div>';
    echo '<ul>';
    for($j = $k = 0,$c=count($news2); $j < $c && $k < 5; $j++,$k++)   //生成无图
      echo '<li><a href="'.'http://localhost/news/index.php?m=danye&lx='.$_REQUEST['lx'].'&id='.$news2[$j]['id'].'" target="_blank">'.$news2[$j]['title'].'</a></li>'.'<div style="display:none;" class="xwjlid">'.$news2[$j]['id'].'</div>';
    //生成侧栏较真 figure 和 ul
    echo '</ul><div id="ckgdht">查看更多</div></div><div id="jz"><h2>较真</h2>';
    if(preg_match('/<img.{2,20}(public\/img\/.{2,40})">/i',$news[++$i]['text'],$src))             //查找到第一张图片的路径
      echo '<figure><img src="'.$src[1].'"><figcaption><a href="'.'http://localhost/news/index.php?m=danye&lx='.$_REQUEST['lx'].'&id='.$news[$i]['id'].'" target="_blank">'.$news[$i]['title'].'</a></figcaption></figure>'.'<div style="display:none;" class="xwjlid">'.$news[$i]['id'].'</div>';
    echo '<ul>';
    for($k = 0; $j < $c && $k < 5; $j++,$k++)                         //生成无图
      echo '<li><a href="'.'http://localhost/news/index.php?m=danye&lx='.$_REQUEST['lx'].'&id='.$news2[$j]['id'].'" target="_blank">'.$news2[$j]['title'].'</a></li>'.'<div style="display:none;" class="xwjlid">'.$news2[$j]['id'].'</div>';
    //生成侧栏资讯 figure 和 ul
    echo '</ul><div id="ckgdjz">查看更多</div></div><div id="zx"><h2>资讯</h2>';
    if(preg_match('/<img.{2,20}(public\/img\/.{2,40})">/i',$news[++$i]['text'],$src))             //查找到第一张图片的路径
      echo '<figure><img src="'.$src[1].'"><figcaption><a href="'.'http://localhost/news/index.php?m=danye&lx='.$_REQUEST['lx'].'&id='.$news[$i]['id'].'" target="_blank">'.$news[$i]['title'].'</a></figcaption></figure>'.'<div style="display:none;" class="xwjlid">'.$news[$i]['id'].'</div>';
    echo '<ul>';
    for($k = 0; $j < $c && $k < 5; $j++,$k++)                         //生成无图
      echo '<li><a href="'.'http://localhost/news/index.php?m=danye&lx='.$_REQUEST['lx'].'&id='.$news2[$j]['id'].'" target="_blank">'.$news2[$j]['title'].'</a></li>'.'<div style="display:none;" class="xwjlid">'.$news2[$j]['id'].'</div>';
    echo '</ul><div id="ckgdzx">查看更多</div></div></section><script src="public/js/public.js"></script><script src="public/js/lb.js"></script></script></body></html>';
  }

  //单页
  function danye(){
    //获取记录
    $line = new PDO('mysql:host=localhost;', 'root', '123');          //连接 mysql
    $re = $line->prepare('select * from news.'.$_REQUEST['lx'].' where id='.$_REQUEST['id']);
    $re->execute();                                                   //执行
    $new = $re->fetchall();                                           //获取记录
    $re = $line->prepare('update news.'.$_REQUEST['lx'].' set click=click+1 where id='.$_REQUEST['id']);
    $re->execute();                                                   //执行

    //生成头部和左侧栏
    $table = ['yw','yl','ty','js','nba','gj','kj'];
    $nav=array('yw'=>'要闻','yl'=>'娱乐','ty'=>'体育','js'=>'军事','nba'=>'NBA','gj'=>'国际','kj'=>'科技');
    $time = explode(' ',date('Y-m-d H:i'));                           //获取时间
    $time[0] = explode('-', $time[0]);
    echo '<!DOCTYPE html><html lang="zh-CN"><head><title>'.$new[0]['title'].'</title><meta charset="UTF-8"><meta name="viewport"content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"><style></style><link rel="stylesheet" href="public/css/single.css"></head><body><header><div id="caidan"></div><div id="caidank"><ul>';
    for($i = 0; $i < 7; $i++)   echo '<li><a href="http://localhost/news/index.php?m=index&lx='.$table[$i].'">'.$nav[$table[$i]].'</a></li>';
    echo '</ul></div><div id="tbbt">正在阅读：'.$new[0]['title'].'</div><div id="login">登录</div><div id="user"></div></header><div id="left"><div id="date"><div id="year">'.$time[0][0].'</div><div id="day">'.$time[0][1].'/'.$time[0][2].'</div><div id="time">'.$time[1].'</div></div><div id="fenxiang"><div id="fenxiangbt">分享</div><div id="wx"></div><div id="qq"></div><div id="qqkj"></div><div id="xlwb"></div></div><div id="goplbt">评论</div><a href="#theend" id="gopl">'.$new[0]['count'].'</a></div>';
    
    //生成主栏
    echo '<h2>'.$new[0]['title'].'</h2><div id="body"><div id="main"><div id="text">'.$new[0]['text'].'</div><div id="theend">THE END</div>'.'<div style="display:none;" class="xwjlid">'.$_REQUEST['lx'].' '.$new[0]['id'].'</div>'.'<div id="plk"><div id="plkbt">网友评论</div><form action="" id="fabiao"><textarea placeholder="说点啥吧" name="pl"></textarea><button type="button" id="fbbutton">发表</button></form><div id="allplbt">全部评论</div><div id="allpl">';

    //生成评论
    $re = $line->prepare('select *,unix_timestamp()-unix_timestamp(time) from news.'.$_REQUEST['lx'].$_REQUEST['id'].'pl limit 4');
    $re->execute();                                                   //执行
    $pls = $re->fetchall();                                           //获取记录
    for($i = 0, $c = count($pls); $i < $c; $i++)
    {
      echo '<div class="singlepl"><div class="u">'.$pls[$i]['user'].'</div><div class="nr">'.$pls[$i]['text'].'</div><div class="other"><div class="good">'.$pls[$i]['good'].'</div><div class="pltime">';
      if(($pls[$i]['unix_timestamp()-unix_timestamp(time)']/3600/24) > 1)
        echo ((int)($pls[$i]['unix_timestamp()-unix_timestamp(time)']/3600/24)).' 天前';
      else if(($pls[$i]['unix_timestamp()-unix_timestamp(time)']/3600) > 1)
        echo ((int)($pls[$i]['unix_timestamp()-unix_timestamp(time)']/3600)).' 小时前';
      else  echo ((int)($pls[$i]['unix_timestamp()-unix_timestamp(time)']/60)).' 分钟前';
      echo '</div></div></div>';
    }

    echo '</div><div id="ckgdpl">查看更多评论</div></div><div id="tuijian"><div id="tuijianbt">为你推荐</div><div id="tuijianjiluk">';

    //生成主栏评论下方的推荐和侧栏推荐
    for($i = $k = $k2 = 0; $i < 7; $i++)                              //获取全库数据进行生成
    {
      $re = $line->prepare('select *,unix_timestamp()-unix_timestamp(time) from news.'.$table[$i].' where id!='.$_REQUEST['id']);
      $re->execute();                                                 //执行
      $news = $re->fetchall();                                        //获取记录
      shuffle($news);                                                 //打乱顺序
      if($k < 4)
      {
        for($j = 0, $c = count($news); $j < $c && $k < 4; $j++)       //生成主栏推荐
          if(preg_match('/<img.{2,20}(public\/img\/.{2,40})">/i',$news[$j]['text'],$src))
          {
            $k++;
            echo '<a href="http://localhost/news/index.php?m=danye&lx='.$table[$i].'&id='.$news[$j]['id'].'" target="_blank"><figure><img src="'.$src[1].'"><figcaption><div class="title">'.$news[$j]['title'].'</div><div class="lx">'.$nav[$table[$i]].'</div><div class="tjtime">';
            if(($news[$j]['unix_timestamp()-unix_timestamp(time)']/3600/24) > 1)
              echo ((int)($news[$j]['unix_timestamp()-unix_timestamp(time)']/3600/24)).' 天前';
            else if(($news[$j]['unix_timestamp()-unix_timestamp(time)']/3600) > 1)
              echo ((int)($news[$j]['unix_timestamp()-unix_timestamp(time)']/3600)).' 小时前';
            else  echo ((int)($news[$j]['unix_timestamp()-unix_timestamp(time)']/60)).' 分钟前';
            echo '</div></figcaption></figure></a>'.'<div style="display:none;" class="xwjlid">'.$table[$i].' '.$news[$j]['id'].'</div>';
          }
      }
      else
        for($j = 0, $c = count($news); $j < $c && $k2 < 8; $j++)      //生成侧栏推荐
          if(preg_match('/<img.{2,20}(public\/img\/.{2,40})">/i',$news[$j]['text'],$src))
          {
            if($k2 == 0)  echo '</div><div id="ckgdtj">查看更多</div></div></div><aside><div id="asidejiluk">';
            $k2++;
            echo '<a href="http://localhost/news/index.php?m=danye&lx='.$table[$i].'&id='.$news[$j]['id'].'" target="_blank"><figure><img src="'.$src[1].'"><figcaption>'.$news[$j]['title'].'</figcaption></figure></a>'.'<div style="display:none;" class="xwjlid">'.$table[$i].' '.$news[$j]['id'].'</div>';
          }
    }

    echo '</div><div id="ckgdaside">查看更多</div></aside></div><script src="public/js/public.js"></script><script src="public/js/single.js"></script></body></html>';
  }

  //专题页
  function zt(){
    $line = new PDO('mysql:host=localhost;','root','123');
    
    //获取数据
    $re = $line->prepare('select *,unix_timestamp()-unix_timestamp(time) from news.'.$_REQUEST['ztid'].'zt');
    $re->execute();
    $news = $re->fetchall();                                            //获取该专题内的新闻记录
    shuffle($news);                                                     //打乱顺序
    $re = $line->prepare('select title from news.zt where id='.$_REQUEST['ztid']);
    $re->execute();
    $title = $re->fetchall();                                            //获取该专题内的新闻记录

    //生成头部
    $table = ['yw','yl','ty','js','nba','gj','kj'];
    $nav=array('yw'=>'要闻','yl'=>'娱乐','ty'=>'体育','js'=>'军事','nba'=>'NBA','gj'=>'国际','kj'=>'科技');
    echo '<!DOCTYPE html><html lang="zh-CN"><head><title>'.$title[0]['title'].'</title><meta charset="UTF-8"><meta name="viewport"content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"><style></style><link rel="stylesheet" href="public/css/zt.css"></head><body><header><div id="caidan"></div><div id="caidank"><ul>';
    for($i = 0; $i < 7; $i++)   echo '<li><a href="http://localhost/news/index.php?m=index&lx='.$table[$i].'">'.$nav[$table[$i]].'</a></li>';
    echo '</ul></div><div id="login">登录</div><div id="user"></div></header><div id="body"><h2>'.$title[0]['title'].'</h2><div id="list">';

    //生成列表
    for($i = 0, $c = count($news); $i < $c; $i++)
    {
      for($j = 0; $j < 7; $j++)                                         //查找当前记录所位于的表
      {
        $re = $line->prepare('select id from news.'.$table[$j].' where id='.$news[$i]['id'].' and title=\''.$news[$i]['title'].'\'');
        $re->execute();
        if($re->rowcount())  break;                                     //查询到，跳出
      }
      echo '<a href="http://localhost/news/index.php?m=danye&lx='.$table[$j].'&id='.$news[$i]['id'].'" target="_blank"><figure>';
      if(preg_match('/<img.{2,20}(public\/img\/.{2,40})">/i',$news[$i]['text'],$src))  //如果有图片，输出 img
        echo '<img src="'.$src[1].'">';
      else
        echo '<img src="public/img/none.png">';
      echo '<figcaption><div class="title">'.$news[$i]['title'].'</div><div class="lx">'.$nav[$table[$j]].'</div><div class="time">';
      if(($news[$i]['unix_timestamp()-unix_timestamp(time)']/3600/24) > 1)
        echo ((int)($news[$i]['unix_timestamp()-unix_timestamp(time)']/3600/24)).' 天前';
      else if(($news[$i]['unix_timestamp()-unix_timestamp(time)']/3600) > 1)
        echo ((int)($news[$i]['unix_timestamp()-unix_timestamp(time)']/3600)).' 小时前';
      else  echo ((int)($news[$i]['unix_timestamp()-unix_timestamp(time)']/60)).' 分钟前';
      echo '</div></figcaption></figure></a>';
    }
    echo '</div></div><script src="public/js/public.js"></script><script src="public/js/zt.js"></script></body></html>';
  }
  
  //负责给“查看更多”按钮响应数据
  function ckgd(){
    $nav=array('要闻'=>'yw','娱乐'=>'yl','体育'=>'ty','军事'=>'js','NBA'=>'nba','国际'=>'gj','科技'=>'kj');
    $nav2=array('yw'=>'要闻','yl'=>'娱乐','ty'=>'体育','js'=>'军事','nba'=>'NBA','gj'=>'国际','kj'=>'科技');
    $table=['yw','yl','ty','js','nba','gj','kj'];                     //表名
    $line = new PDO('mysql:host=localhost;', 'root', '123');          //连接 mysql
    $re = $line->prepare('use news');                                 //选择 news 数据库
    $re->execute();

    if($_REQUEST['anniu'] < 2)                                        //首页
    {
      //获取数据
      $re = $line->prepare('select id,st from news.zt');              //查询专题
      $re->execute();
      $zt = $re->fetchall();                                          //获取所有专题的视图查询语句
      $id = array('lx'=>$nav[$_REQUEST['lx']]);                       //对应类型的专题数组
      for($i = $j = 0, $c = count($zt); $i < $c; $i++)                //将对应类型的专题新闻 id 整理到同一个数组
      {
        preg_match_all('/select \* from (.{1,3}) where id=(.{1,5})\)/i', $zt[$i]['st'], $ztid, PREG_PATTERN_ORDER);
        for($k = 0, $c2 = count($ztid[1]); $k < $c2; $k++)
          if($ztid[1][$k] == $id['lx'])
          {
            $id[$j]['ztid'] = $zt[$i]['id'];
            $id[$j++]['id'] = $ztid[2][$k];
          }
      }
      $c10 = count($id) - 1;
      $sechfigure = 'select *,unix_timestamp()-unix_timestamp(time) from '.$nav[$_REQUEST['lx']].' where '; //查询语句
      $xwjlid = explode('a',$_REQUEST['id']);                         //获取所有已存在记录的 id
      $c = count($xwjlid);                                            //计算长度
      $sechfigure = $sechfigure.'id!='.$xwjlid[0];                    //初始化
      for($i = 1; $i < $c; $i++)                                      //拼接，去掉所有已存在的新闻记录
        $sechfigure = $sechfigure.' and id!='.$xwjlid[$i];            //拼接一次
      $re = $line->prepare($sechfigure);
      $re->execute();                                                 //执行查询
      $news = $re->fetchall();                                        //获取所有记录
      shuffle($news);                                                 //打乱顺序
      $c = count($news);                                              //计算条数
      //输出数据
      if($_REQUEST['anniu']==0)                                       //首页主栏懒加载
      {
        for($i = $k = 0; $i < $c && $k < 3; $i++)
          if(preg_match('/<img.{2,20}(public\/img\/.{2,40})">/i',$news[$i]['text'],$src))   //查找第一张图片的路径
          {
            $k++;
            for($bj = $m = 0; $m < $c10; $m++)                        //检测该新闻是否位于专题内
              if($news[$i]['id'] == $id[$m]['id'])                    //检测到，保存 ztid，跳出
              {
                $bj = $id[$m]['ztid'] + 1;
                break;
              }
            if($bj)                                                   //文章分类
              echo '<figure class="zt"><img src="';
            else if($news[$i]['click'] > 5)
              echo '<figure class="rd"><img src="';
            else
              echo '<figure class="pt"><img src="';
            echo $src[1].'"><figcaption><div id="bt">';
            if($bj)                                                   //专题
              echo '专题</div><a href="'.'http://localhost/news/index.php?m=zt&ztid='.($bj-1).'" target="_blank">';
            else                                                      //不是专题
              echo '</div><a href="http://localhost/news/index.php?m=danye&lx='.$nav[$_REQUEST['lx']].'&id='.$news[$i]['id'].'" target="_blank">';
            echo $news[$i]['title'].'</a><span class="time">';
            if(($news[$i]['unix_timestamp()-unix_timestamp(time)']/3600/24) > 1)
              echo ((int)($news[$i]['unix_timestamp()-unix_timestamp(time)']/3600/24)).' 天前';
            else if(($news[$i]['unix_timestamp()-unix_timestamp(time)']/3600) > 1)
              echo ((int)($news[$i]['unix_timestamp()-unix_timestamp(time)']/3600)).' 小时前';
            else  echo ((int)($news[$i]['unix_timestamp()-unix_timestamp(time)']/60)).' 分钟前';
            echo '</span><a href="" class="share">分享</a><a href="'.'http://localhost/news/index.php?m=danye&lx='.$nav[$_REQUEST['lx']].'&id='.$news[$i]['id'].'#theend" class="pl" target="_blank">'.$news[$i]['count'].'</a></figcaption></figure>'.'<div style="display:none;" class="xwjlid">'.$news[$i]['id'].'</div>';
          }
      }
      else if($_REQUEST['anniu']==1)                                  //首页侧栏懒加载
        for($i = 0; $i < $c && $i < 3; $i++)
            echo '<li><a href="'.'http://localhost/news/index.php?m=danye&lx='.$nav[$_REQUEST['lx']].'&id='.$news[$i]['id'].'" target="_blank">'.$news[$i]['title'].'</a></li>'.'<div style="display:none;" class="xwjlid">'.$news[$i]['id'].'</div>';
    }
    else
    {
      if($_REQUEST['anniu'] == 2)                                     //单页评论懒加载
      {
        $re = $line->prepare('select *,unix_timestamp()-unix_timestamp(time) from news.'.$_REQUEST['lx'].$_REQUEST['id'].'pl where id>'.$_REQUEST['plcount'].' limit 4');
        $re->execute();                                               //执行
        $pls = $re->fetchall();                                       //获取记录
        for($i = 0, $c = count($pls); $i < $c; $i++)
        {
          echo '<div class="singlepl"><div class="u">'.$pls[$i]['user'].'</div><div class="nr">'.$pls[$i]['text'].'</div><div class="other"><div class="good">'.$pls[$i]['good'].'</div><div class="pltime">';
          if(($pls[$i]['unix_timestamp()-unix_timestamp(time)']/3600/24) > 1)
            echo ((int)($pls[$i]['unix_timestamp()-unix_timestamp(time)']/3600/24)).' 天前';
          else if(($pls[$i]['unix_timestamp()-unix_timestamp(time)']/3600) > 1)
            echo ((int)($pls[$i]['unix_timestamp()-unix_timestamp(time)']/3600)).' 小时前';
          else  echo ((int)($pls[$i]['unix_timestamp()-unix_timestamp(time)']/60)).' 分钟前';
          echo '</div></div></div>';
        }
        exit;
      }
      else                                                            //单页推荐懒加载
      {
        $neweds = explode('a',$_REQUEST['id']);                       //切割记录 id
        for($i = 0, $c = count($neweds); $i < $c; $i++)               //处理每对值
          $neweds[$i] = explode(' ',$neweds[$i]);                     //将每对值分为 lx 和 id
        $newedid = array('yw'=>array(0=>1),'yl'=>array(0=>1),'ty'=>array(0=>1),'js'=>array(0=>1),'nba'=>array(0=>1),'gj'=>array(0=>1),'kj'=>array(0=>1));//保存整理已存在记录 id 后的结果
        for($i = 0; $i < $c; $newedid[$neweds[$i][0]][0] += 1, $i++)  //将各类新闻 id 进行归类
          $newedid[$neweds[$i][0]][$newedid[$neweds[$i][0]][0]] = $neweds[$i][1];
        for($i = $k = 0; $i < 7; $i++)                                //全库查询并生成
        {
          $sechjl = 'select *,unix_timestamp()-unix_timestamp(time) from '.$table[$i]; //查询语句
          if($newedid[$table[$i]][0] > 1)                             //判断该表中是否有页面中已存在的记录，若有则对查询字符串进行拼接
          {
            $sechjl = $sechjl.' where id!='.$newedid[$table[$i]][1];  //初始化
            for($j = 2; $j < $newedid[$table[$i]][0]; $j++)           //拼接全部
              $sechjl = $sechjl.' and id!='.$newedid[$table[$i]][$j]; //拼接一次
          }
          $re = $line->prepare($sechjl);
          $re->execute();                                             //执行查询
          $news = $re->fetchall();                                    //获取所有记录
          shuffle($news);                                             //打乱顺序
          for($m = 0, $c = count($news); $m < $c && $k < 2; $m++)     //生成响应数据
            if(preg_match('/<img.{2,20}(public\/img\/.{2,40})">/i',$news[$m]['text'],$src))
            {
              $k++;
              if($_REQUEST['anniu'] == 3)                             //单页主栏推荐懒加载
              {
                echo '<a href="'.'http://localhost/news/index.php?m=danye&lx='.$table[$i].'&id='.$news[$m]['id'].'" target="_blank"><figure><img src="'.$src[1].'"><figcaption><div class="title">'.$news[$m]['title'].'</div><div class="lx">'.$nav2[$table[$i]].'</div><div class="tjtime">';
                if(($news[$m]['unix_timestamp()-unix_timestamp(time)']/3600/24) > 1)
                  echo ((int)($news[$m]['unix_timestamp()-unix_timestamp(time)']/3600/24)).' 天前';
                else if(($news[$m]['unix_timestamp()-unix_timestamp(time)']/3600) > 1)
                  echo ((int)($news[$m]['unix_timestamp()-unix_timestamp(time)']/3600)).' 小时前';
                else  echo ((int)($news[$m]['unix_timestamp()-unix_timestamp(time)']/60)).' 分钟前';
                echo '</div></figcaption></figure></a>'.'<div style="display:none;" class="xwjlid">'.$table[$i].' '.$news[$m]['id'].'</div>';
              }
              else                                                    //单页侧栏懒加载
                echo '<a href="'.'http://localhost/news/index.php?m=danye&lx='.$table[$i].'&id='.$news[$m]['id'].'" target="_blank"><figure><img src="'.$src[1].'"><figcaption>'.$news[$m]['title'].'</figcaption></figure></a>'.'<div style="display:none;" class="xwjlid">'.$table[$i].' '.$news[$m]['id'].'</div>';
            }
        }
      }
    }
  }

  function regdanye(){
    echo '<!DOCTYPE html><html lang="zh-CN"><head><title>新闻站——用户注册</title><meta charset="UTF-8"><meta name="viewport"content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"><style>*{margin:0px;padding:0px;}body{width:100%;background:rgb(60,60,60);}form{width:100%;margin:7% 0px;}form label{display:block;margin:50px auto;width:500px;}form input[type^="text"],form input[type^="password"],form input[type^="numb"]{width:400px;height:35px;font-size:18px;background:black;color:rgb(230,230,230);border-radius:20px;padding-left:15px;}form input[type^="radio"]{width:25px;height:25px;margin:0px 15px;}button{width:100%;height:40px;background:black;color:rgb(230,230,230);font-size:18px;border-radius:20px;}</style></head><body><form action="http://localhost/news/index.php?m=reg" method="post" enctype="multipart/form-data"><label>邮箱：<input type="text" name="mail" placeholder="请输入邮箱"></label><label>网名：<input type="text" name="user" placeholder="只能由字母、数字、下划线、汉字组成"></label><label>密码：<input class="pw" type="password" name="pw" placeholder="请输入密码"></label><label>确认：<input class="pw" type="password" name="pw2" placeholder="再次输入确认密码"></label><label>性别：男<input type="radio" name="sex" value="男">女<input type="radio" name="sex" value="女"></label><label>年龄：<input type="number" name="age" placeholder="请输入年龄"></label><label><button id="b" type="button">确认</button></label></form><script>var b = document.getElementById("b");b.onclick=function(){var pw = document.getElementsByClassName("pw");var user = document.getElementById("user");var test = /^[\d,_,a-z,A-Z,\u2E80-\u9FFF]{1,30}$/i;var text = document.querySelectorAll("form input[type^=\"text\"]");if(text[0].value==""){alert("邮箱不能为空！");return;}if (text[1].value == ""){alert("网名不能为空！");return;}else if(!(test.test(text[1].value))){alert("网名只能由字母、数字、下划线、汉字组成");return;}if (pw[0].value == "" || pw[1].value==""){alert("密码不能为空");return;}if(pw[0].value != pw[1].value){alert("两次输入的密码不一致！");return;}this.type = "submit";alert("注册成功");}</script></body></html>';
  }

  //注册
  function reg(){
    $line = new PDO('mysql:host=localhost;', 'root', '123');          //连接 mysql

    //检测、进入数据库
    $re = $line->prepare('use u');                                    //选择 u 数据库
    if(!($re->execute()))                                             //执行，若失败，创建 u
    {
      $re = $line->prepare('create database u');                      //创建
      $re->execute();
      $re = $line->prepare('use u');                                  //进入
      $re->execute();
      $re = $line->prepare('create table user(mail char(24) primary key,user char(30),pw char(30),sex char(8),age smallint)'); //创建数据表
      $re->execute();
    }

    //插入数据
    $re = $line->prepare('insert into user values(\''.$_REQUEST['mail'].'\',\''.$_REQUEST['user'].'\',\''.$_REQUEST['pw'].'\',\''.$_REQUEST['sex'].'\','.$_REQUEST['age'].')');
    $re->execute();
  }

  //登录
  function login(){
    $line = new PDO('mysql:host=localhost;', 'root', '123');          //连接 mysql

    //检测、进入数据库
    $re = $line->prepare('use u');                                    //选择 u 数据库
    if(!($re->execute()))                                             //执行，若失败，创建 u
    {
      $re = $line->prepare('create database u');                      //创建
      $re->execute();
      $re = $line->prepare('use u');                                  //进入
      $re->execute();
      $line->prepare('create table user(mail char(24) primary key,user char(30),pw char(30),sex char(8),age smallint)'); //创建数据表
      $re->execute();
    }

    //检测、查询数据表
    $re = $line->prepare('select * from user where mail=\''.$_REQUEST['mail'].'\'');    //查询
    if(!($re->execute()))                                             //查询失败，提示注册
    {
      echo '0 未查找到当前用户，请注册';
      exit;
    }
    $u = $re->fetchall();                                             //获取数据
    if($re->rowcount() < 1)
    {
      echo '0 未查找到当前用户，请注册';
      exit;
    }
    if($_REQUEST['pw']===$u[0]['pw'])                                 //验证密码
    {
      echo '1 登录成功 '.$u[0]['mail'].' '.$u[0]['user'].' '.$u[0]['sex'].' '.$u[0]['age'];
      setcookie('user',$u[0]['mail'].';'.$u[0]['pw'],time()+60);      //发送 cookie
    }
    else
      echo '2 密码错误';
  }

  //评论，存放到新闻评论表，再生成 HTML 返回给前端
  function pl(){
    $line = new PDO('mysql:host=localhost;', 'root', '123');          //连接 mysql

    //插入到新闻评论表
    $re = $line->prepare('use news');
    $re->execute();
    $re = $line->prepare('insert into '.$_REQUEST['lx'].$_REQUEST['id'].'pl values(null,\''.$_REQUEST['user'].'\',\''.$_REQUEST['text'].'\',0,now())');
    if(!($re->execute()))                                             //执行
    {
      $re = $line->prepare('create table '.$_REQUEST['lx'].$_REQUEST['id'].'pl(id int primary key auto_increment,user varchar(40),text varchar(1000),good int default 0,time datetime)');
      $re->execute();
      $re = $line->prepare('insert into '.$_REQUEST['lx'].$_REQUEST['id'].'pl values(null,\''.$_REQUEST['user'].'\',\''.$_REQUEST['text'].'\',0,now())');
      $re->execute();
    }

    //输出 HTML
    echo '<div class="singlepl"><div class="u">'.$_REQUEST['user'].'</div><div class="nr">'.$_REQUEST['text'].'</div><div class="other"><div class="good">0</div><div class="pltime">刚刚</div></div></div>';

    //更新表的评论数
    $re = $line->prepare('select count(*) from news.'.$_REQUEST['lx'].$_REQUEST['id'].'pl');
    $re->execute();                                                   //执行
    $plcount = $re->fetchall();                                       //获取计算结果
    $re = $line->prepare('update '.$_REQUEST['lx'].' set count='.$plcount[0]['count(*)'].' where id='.$_REQUEST['id']);
    $re->execute();
  }
  
  //点赞数加 1
  function good(){
    $line = new PDO('mysql:host=localhost;', 'root', '123');          //连接 mysql
    $re = $line->prepare('update news.'.$_REQUEST['lx'].$_REQUEST['id'].'pl set good=good+1 where id='.$_REQUEST['plid']);
    var_dump($re->execute());
  }

  if(!(isset($_REQUEST['m'])))  index();
  else
    switch(@$_REQUEST['m']){
      case 'index':  index(); break;                                  //首页
      case 'danye':  danye(); break;                                  //单页新闻
      case 'zt':  zt(); break;                                        //单页专题
      case 'regdanye':  regdanye(); break;                            //生成注册页面
      case 'ckgd':  ckgd(); break;                                    //支持各页面的懒加载
      case 'reg':  reg(); break;                                      //处理注册操作
      case 'login':  login(); break;                                  //处理登录操作
      case 'pl': pl(); break;                                         //新增评论
      case 'good': good(); break;                                     //新增点赞
    }
?>