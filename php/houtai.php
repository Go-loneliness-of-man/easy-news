<?php
  header('Refresh:0;url=http://localhost/news/resource/houtai.html');
  header('Access-Control-Allow-Origin:*');                          //允许任何域名访问，便于前端 ajax 交互

  //多关键字查询新闻，执行查询语句，获取所有记录，格式化为 HTML 输出
  function sech(){
    $line = new PDO('mysql:host=localhost;', 'root', '123');        //连接 mysql

    //检测、进入数据库
    $re = $line->prepare('use news');                               //选择 news 数据库
    if(!($re->execute()))                                           //执行，若不存在，返回不存在
    {
      echo '<em>当前新闻记录数据库为空</em>';
      return 0;
    }
    $s = explode(' ',$_REQUEST['sech']);                            //关键字分割
    $b = 'title like \'%'.$s[0].'%\'';                              //只有一个关键字
    $c = count($s);
    for($i = 1; $i < $c; $i++)  $b = $b.' or title like \'%'.$s[$i].'%\'';                                  //拼接多关键字
    $table=['yw','yl','ty','js','nba','gj','kj'];                   //表名
    $table2=array('yw'=>'要闻','yl'=>'娱乐','ty'=>'体育','js'=>'军事','nba'=>'NBA','gj'=>'国际','kj'=>'科技');  //表名对应的类型
    $page = 0;                                                      //分页变量
    if(isset($_REQUEST['page']))  $page = ($_REQUEST['page'] - 1) * 14;   //检查是否带有页码，若有则计算需过滤记录数

    //查询
    if(@$_REQUEST['dakuang'] == 0) {
      //输出全库的新闻
      for($i = $k = 0; $i < 7; $i++)                                  //$k 用来限制输出条数，最大 14 条
      {
        $re=$line->prepare('select * from '.$table[$i].' where '.$b); //查询一张表
        if(!($re->execute()))  continue;                              //执行，若查询失败则跳过
        $result=$re->fetchall();                                      //获取查询到的所有记录
        $c2=count($result);                                           //计算记录数
        if($page > 0){                                                //分页过滤
          $page -= $c2;                                               //计算剩余需过滤数
          if($page >= 0) continue;                                    //剩余过滤数不为 0，跳过本次输出
        }
        if($page < 0)                                                 //当记录未全部被过滤时，计算被过滤的最大下标，之后重置分页变量为 0
        {
          $j = $c2 + $page;
          $page = 0;
        }
        else  $j = 0;                                                 //不存在分页
        for(; $j < $c2 && $k < 14; $j++, $k++)                        //格式化输出记录，限制输出 14 条
          echo '<ul class="jilu"><li>'.$result[$j]['id'].'</li><li>'.$result[$j]['title'].'</li><li>'.$table2[$table[$i]].'</li><li>'.$result[$j]['time'].'</li><li>'.$result[$j]['click'].'</li><li>'.$result[$j]['count'].'</li><li><div id="del"></div><div id="look"></div></li></ul>';
        if($k >= 14)  break;                                          //到达 14 条，跳出
      }
    }
    else {
      //输出所有专题
      if(isset($_REQUEST['page']))  $page = ($_REQUEST['page'] - 1) * 14;   //检查是否带有页码
      $re=$line->prepare('select * from zt where '.$b);               //查询专题总表
      $re->execute();                                                 //执行
      $result=$re->fetchall();                                        //获取查询到的所有记录
      $c2=count($result);                                             //计算记录数
      if($page > 0) {                                                 //分页过滤
        $page -= $c2;                                                 //计算剩余需过滤数
        if($page > 0)   exit;                                         //若过滤数仍大于 0，不输出记录
      }
      if($page < 0)   $j = $c2 + $page;                               //当记录未全部被过滤时，计算被过滤的最大下标
      else  $j = 0;                                                   //不存在分页
      for($k = 0; $j < $c2 && $k < 14; $j++, $k++)                    //格式化输出记录，限制最多输出 14 条
        echo @'<ul class="jilu"><li>'.$result[$j]['id'].'</li><li>'.$result[$j]['title'].'</li><li>'.$result[$j]['time'].'</li><li><div id="del"></div><div id="look"></div></li></ul>';
    }
    exit;
  }

  //为 text 的 img、vd、au 添加 src，接收已排序的路径名
  function text($f){
    $temp = $_POST['text'];
    $temp = preg_split('/(<img>)|(<au>)|(<vd>)/i',$temp,-1,PREG_SPLIT_DELIM_CAPTURE);
    $c = count($temp);
    $result = '';
    for($i = $j = 0; $i < $c - 1; $i++)
    {
      if($temp[$i]=='<img>')
        $temp[$i - 1] = $temp[$i - 1].'<img src="'.$f[$j++].'">';
      else if($temp[$i + 1]=='<au>')
        $temp[$i - 1] = $temp[$i - 1].'<audio controls preload="auto" loop muted src="'.$f[$j++].'">';
      else if($temp[$i + 1]=='<vd>')
        $temp[$i - 1] = $temp[$i - 1].'<video controls preload="auto" loop muted src="'.$f[$j++].'">';
      $result = $result.@$temp[$i - 1];
    }
    $result = $result.@$temp[$i];
    return $result;
  }
  //添加新闻，先判断是否存在数据库，若不存在则创建，再根据 lx 选择存入哪个数据表，存入前依然是先判断是否存在在数据表，不存在则创建，然后再判断是否存在资源，存在则放到 img 下并建表存放其路径，再依次为 text 中的 src 添加路径更新到数据库
  function addxw(){
    $line = new PDO('mysql:host=localhost;', 'root', '123');        //连接 mysql
    //检测、进入数据库
    $re = $line->prepare('use news');                               //选择 news 数据库
    if(!($re->execute()))                                           //执行，若不存在，创建 news
    {
      $re = $line->prepare('create database news');                 //创建 news 数据库
      $re->execute();
      $re = $line->prepare('use news');                             //进入 news 数据库
      $re->execute();
    }

    //处理 text 格式，检测、将数据插入数据表
    $f = fopen('text.txt','wb+');                                   //打开文件
    fwrite($f,$_POST['text']);                                      //写入内容
    `text.exe`;                                                     //调用格式化程序
    $_POST['text'] = file_get_contents('text.txt');                 //读取内容
    fclose($f);                                                     //关闭文件
    unlink('text.txt');                                             //删除文件
    $re = $line->prepare('insert into '.$_POST['lx'].'(title,text,time) values('."'".$_POST['title']."'".','."'".$_POST['text']."'".','.'now())');    //向数据表中插入数据
    if(!($re->execute()))                                           //执行，若失败，创建数据表，存放该类型新闻
    {
      $re = $line->prepare('create table '.$_POST['lx'].'(id int primary key auto_increment, title varchar(50),text text,time datetime,click int default 0, count int default 0) auto_increment=1');//创建数据表
      $re->execute();
      $re = $line->prepare('insert into '.$_POST['lx'].'(title,text,time) values('."'".$_POST['title']."'".','."'".$_POST['text']."'".','.'now())');  //向数据表中插入数据
      $re->execute();
    }

    //处理资源，将资源存入 img 内，重命名为 新闻类型+新闻id_资源名
    if(!$_FILES['f']['error'][0])                                   //检测是否存在文件
    {
      $re = $line->prepare('select id from '.$_POST['lx'].' where title='."'".$_POST['title']."'");   //获取刚刚被插入新闻的主键
      $re->bindColumn('id',$id);                                                                      //绑定变量到字段
      $re->execute();                                                                                 //执行
      $re->fetch();                                                                                   //拉取数据
      $c = count($_FILES['f']['name']);                                                               //计算数量
      var_dump($_FILES['f']['name']);
      for($i = 0, $nums = []; $i < $c; $i++)  $nums[$i] = $_FILES['f']['name'][$i] + 1 - 1;           //将前缀转换为数字，为排序做准备
      var_dump($nums);
      for($i = 0; $i < $c; $i++)                                                                      //将所有资源存储到 img 下
      {
        $_FILES['f']['name'][$i] = 'public/img/'.$_POST['lx'].$id.'_'.$_FILES['f']['name'][$i];       //将文件名扩展为新路径
        move_uploaded_file($_FILES['f']['tmp_name'][$i],'../'.$_FILES['f']['name'][$i]);              //存储到 img 下
      }

      //为 text 内的 <img>、<vd>、<au> 添加 src
      for($i = $temp = $temp2 = $min = 0; $i < $c; $i++)                                              //选择排序
      {
        for($j = $min = $i; $j < $c; $j++)
          if($nums[$j]<$nums[$min]) $min = $j;
        if($min == $i)  continue;
        $temp = $nums[$i];
    		$nums[$i] = $nums[$min];
        $nums[$min] = $temp;

        $temp2 = $_FILES['f']['name'][$i];                                                             //将排序同步到路径
        $_FILES['f']['name'][$i] = $_FILES['f']['name'][$min];
        $_FILES['f']['name'][$min] = $temp2;
      }
      var_dump($_FILES['f']['name']);
      $_POST['text'] = text($_FILES['f']['name']);                                                     //将资源路径按顺序添加到 img、au、vd 中
      $re = $line->prepare('update '.$_POST['lx'].' set text=\''.$_POST['text'].'\' where id='.$id);   //更新 text 字段
      var_dump($re->execute());
    }
  }

  //向专题内添加新闻，先根据专题 id 找到专题 st，再根据专题 id 删掉原视图（如果有的话），然后根据专题 id 和专题 st 创建对应的视图（st 代表的是视图的 select 语句）
  function addztxw(){
    $line = new PDO('mysql:host=localhost;', 'root', '123');        //连接 mysql

    //检测、进入数据库
    $re = $line->prepare('use news');                               //选择 news 数据库
    if(!($re->execute()))                                           //执行，若不存在，创建 news
    {
      $re = $line->prepare('create database news');                 //创建 news 数据库
      $re->execute();
      $re = $line->prepare('use news');                             //进入 news 数据库
      $re->execute();
    }

    //根据专题 id 找出专题 st 并对其进行拼接
    $re = $line->prepare('select st from zt where id='.$_POST['ztid']);   //查找专题 st
    $re->bindColumn('st',$st);                                      //绑定变量到字段
    $re->execute();
    $re->fetch();                                                   //获取一条数据
    if(!$st)  $st = '(select * from '.$_POST['lx'].' where id='.$_POST['xwid'].')';           //视图不存在，创建 select 语句
    else  $st .= ' union all (select * from '.$_POST['lx'].' where id='.$_POST['xwid'].')';   //视图存在，拼接 select 语句
    echo $_POST['xwid'];
    //删除原视图
    $re = $line->prepare('drop view '.$_POST['ztid'].'zt');
    $re->execute();

    //创建新视图
    $re = $line->prepare('create view '.$_POST['ztid'].'zt as ('.$st.')');
    $re->execute();

    //将新的 select 语句存储到专题总表对应的专题记录的 st 字段中
    $st = str_replace('\'','\\\'',$st);                                   //转义字符串中的单引号 '，使其能被存储到数据库中
    $re = $line->prepare('update zt set st=\''.$st.'\' where id='.$_POST['ztid']);
    $re->execute();
  }

  //添加专题
  function addzt(){
    $line = new PDO('mysql:host=localhost;', 'root', '123');        //连接 mysql

    //检测、进入数据库
    $re = $line->prepare('use news');                               //选择 news 数据库
    if(!($re->execute()))                                           //执行，若不存在，创建 news
    {
      $re = $line->prepare('create database news');                 //创建 news 数据库
      $re->execute();
      $re = $line->prepare('use news');                             //进入 news 数据库
      $re->execute();
    }

    //检测、进入数据表
    $re = $line->prepare('insert into zt(title,time) values(\''.$_POST['zttitle'].'\',now())');                //向数据表中插入数据
    if(!($re->execute()))                                           //执行，若失败，创建数据表，存放该类型新闻
    {
      $re = $line->prepare('create table zt(id int primary key auto_increment, title varchar(50), time datetime,st text) auto_increment=1');//创建数据表
      $re->execute();
      $re = $line->prepare('insert into zt(title,time) values(\''.$_POST['zttitle'].'\',now())');                //向数据表中插入数据
      $re->execute();
    }
  }

  //删除记录
  function del(){
    $line = new PDO('mysql:host=localhost;', 'root', '123');        //连接 mysql
    $table = array('要闻'=>'yw','娱乐'=>'yl','体育'=>'ty','军事'=>'js','NBA'=>'nba','国际'=>'gj','科技'=>'kj');  //表名对应的类型
    $re = $line->prepare('use news');                               //选择 news 数据库
    if(!($re->execute())) echo '数据库不存在';                        //执行，若不存在，返回错误
    if($_REQUEST['dakuang'])                                        //专题
      $re = $line->prepare('delete from zt where id='.$_REQUEST['id']);
    else                                                            //新闻
    {
      $re = $line->prepare('select text from '.$table[$_REQUEST['lx']].' where id='.$_REQUEST['id']);
      $re->execute();
      $text=$re->fetchall();
      preg_match_all('/src.{0,20}public\/img\/(.{2,50})">/i',$text[0]['text'],$src,PREG_PATTERN_ORDER);  //匹配所有路径
      for($i = 0, $c = count($src[1]); $i < $c; $i++) unlink('../public/img/'.$src[1][$i]);              //删除记录对应的所有资源
      $re = $line->prepare('delete from '.$table[$_REQUEST['lx']].' where id='.$_REQUEST['id']);
    }
    $re->execute();
  }

  //查看记录
  function lok(){
    $line = new PDO('mysql:host=localhost;', 'root', '123');        //连接 mysql
    $table = array('要闻'=>'yw','娱乐'=>'yl','体育'=>'ty','军事'=>'js','NBA'=>'nba','国际'=>'gj','科技'=>'kj');  //表名对应的类型
    $re = $line->prepare('use news');                               //选择 news 数据库
    if(!($re->execute())) echo '数据库不存在';                        //执行，若不存在，返回错误
    if($_REQUEST['dakuang'])                                        //专题
      $re = $line->prepare('select * from zt where id='.$_REQUEST['id']);
    else                                                            //新闻
      $re = $line->prepare('select * from '.$table[$_REQUEST['lx']].' where id='.$_REQUEST['id']);
    $re->execute();
    $result = $re->fetchall();                                      //获取查询到的所有记录
    if($_REQUEST['dakuang'])                                        //专题
    {
      echo '<h2>专题基本信息</h2>';
      for($i = 0; $i < 6; $i++) echo @$result[0][$i]."<br>";        //输出专题基本信息
      $re = $line->prepare('select * from '.$_REQUEST['id'].'zt');  //查询专题包含的所有记录
      $re->execute();
      $result = $re->fetchall();                                    //获取该专题下的所有新闻记录
      $c = count($result);
      echo '<h2>专题所收录新闻记录基本信息</h2>';
      for($i = 0; $i < $c; $i++)
        echo '<br>'.$result[$i]['id'].'<br>'.$result[$i]['title'].'<br>'.$result[$i]['time'].'<br>'.$result[$i]['click'].'<br>'.$result[$i]['count'].'<br>'.'<br>';
    }
    else                                                            //新闻
      for($i = 0; $i < 6; $i++) echo @$result[0][$i]."<br>";        //全部输出
  }

  switch($_REQUEST['m']){
    case 'sech':  sech(); break;                                    //搜索
    case 'addxw':  addxw(); break;                                  //添加新闻
    case 'addztxw':  addztxw(); break;                              //为专题添加新闻
    case 'addzt':  addzt(); break;                                  //添加专题
    case 'del':  del(); break;                                      //删除新闻
    case 'lok':  lok(); break;                                      //查看新闻
  }
?>