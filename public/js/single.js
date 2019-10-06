function gxtime () {                                                 //更新页面时间
  var dqtime = new Date();                                           //获取当前时间
  var time = dqtime.toLocaleDateString().split('/');                 //获取日期
  if (time[1] <= '9') time[1] = '0' + time[1];                       //调整格式
  if (time[2] <= '9') time[2] = '0' + time[2];
  time[3] = dqtime.toString().split(' ')[4].split(':')[0] + ':' + dqtime.toString().split(' ')[4].split(':')[1];   //获取 24 小时制本地时间
  var date = document.getElementById('date');                        //获取左侧栏日期元素
  date.childNodes[0].childNodes[0].nodeValue = time[0];              //更新值
  date.childNodes[1].childNodes[0].nodeValue = time[1] + '/' + time[2];
  date.childNodes[2].childNodes[0].nodeValue = time[3];
  setTimeout(gxtime, 10000);                                         //每 10 秒检测更新一次
}

function getSearch () {                                             //获取 url 参数
  var s = location.search.length ? location.search.substring(1) : "";
  if (!s) return null;                                              //如果没有查询字符串，返回 null
  var x = s.split("&");                                             //以“&”切割出每一项
  return x;                                                         //返回参数
}

//查看更多按钮
function ckgd () {
  var ckgdanniu = document.querySelectorAll('div[id^="ckgd"]');
  ckgdanniu[0].onclick = function () {                               //评论懒加载
    plcount = document.getElementsByClassName('singlepl').length;    //计算评论条数
    var lx = getSearch();                                            //获取当前新闻类型
    var line = new XMLHttpRequest();                                 //创建 ajax 对象
    line.open('post', 'http://localhost/news/index.php', true);      //初始化请求
    line.setRequestHeader("Content-type", "application/x-www-form-urlencoded");//配置 post
    line.send('m=ckgd' + '&anniu=2&' + lx[1] + '&' + lx[2] + '&plcount=' + plcount);  //发送请求
    line.onreadystatechange = function () {                          //处理响应数据
      if (this.readyState == 4) {                                    //判断响应数据是否就绪
        if (line.responseText != '') {
          var allpl = document.getElementById('allpl');              //获取外层评论框
          if (line.responseText != '')
            allpl.innerHTML += line.responseText;                    //显示评论
        }
        else alert('没有更多评论了 >_<');
      }
    }
  }

  ckgdanniu[1].onclick = function () {                               //主栏懒加载
    xwjlid = huoqusuoyoujiluid();                                    //获取记录 id 字符串
    var tuijianjiluk = document.getElementById('tuijianjiluk');      //获取外部框
    var line = new XMLHttpRequest();                                 //创建 ajax 对象
    line.open('post', 'http://localhost/news/index.php', true);      //初始化请求
    line.setRequestHeader("Content-type", "application/x-www-form-urlencoded");//配置 post
    line.send('m=ckgd&id=' + xwjlid + '&anniu=3');                   //发送请求
    line.onreadystatechange = function () {                          //处理响应数据
      if (this.readyState == 4)                                      //判断响应数据是否就绪
        if (line.responseText != '')
          tuijianjiluk.innerHTML += line.responseText;               //显示数据
        else alert('没有更多新闻了 >_<');
    }
  }

  ckgdanniu[2].onclick = function () {                               //侧栏懒加载
    xwjlid = huoqusuoyoujiluid();                                    //获取记录 id 字符串
    console.log(xwjlid);
    var asidejiluk = document.getElementById('asidejiluk');          //获取外部框
    var line = new XMLHttpRequest();                                 //创建 ajax 对象
    line.open('post', 'http://localhost/news/index.php', true);      //初始化请求
    line.setRequestHeader("Content-type", "application/x-www-form-urlencoded");//配置 post
    line.send('m=ckgd&id=' + xwjlid + '&anniu=4');                   //发送请求
    line.onreadystatechange = function () {                          //处理响应数据
      if (this.readyState == 4)                                      //判断响应数据是否就绪
        if (line.responseText != '')
          asidejiluk.innerHTML += line.responseText;                 //显示数据
        else alert('没有更多新闻了 >_<');
    }
  }
}

function pl () {                                                    //评论
  var fbbutton = document.getElementById('fbbutton');               //获取发表按钮
  fbbutton.onclick = function () {                                  //绑定点击事件
    var text = document.getElementById('fabiao').childNodes[0].value;//获取值
    document.getElementById('fabiao').childNodes[0].value = '';     //清空
    user = decodeURIComponent(document.cookie).split(';')[0].split('=')[1];//获取用户邮箱
    if (user.search(/@/i) < 0) {                                    //检测用户是否已登录
      alert('请先登录');
      return;
    }
    else if (text == '') {                                          //检测用户是否已登录
      alert('评论内容不能为空');
      return;
    }
    var lx = getSearch();                                           //获取当前新闻类型及 id
    var name = document.getElementById('user').childNodes[0].nodeValue; //获取用户名
    var line = new XMLHttpRequest();                                //创建 ajax 对象
    line.open('post', 'http://localhost/news/index.php', true);     //初始化请求
    line.setRequestHeader("Content-type", "application/x-www-form-urlencoded");                         //配置 post
    line.send('m=pl&mail=' + user + '&text=' + text + '&' + lx[1] + '&' + lx[2] + '&user=' + name);     //发送请求
    line.onreadystatechange = function () {                         //处理响应数据
      if (this.readyState == 4) {                                   //判断响应数据是否就绪
        var allpl = document.getElementById('allpl');               //获取外层评论框
        if (line.responseText != '')
          allpl.innerHTML += line.responseText;                     //显示评论
        else alert('评论失败 >_<');
      }
    }
  }
}

function good () {                                                  //绑定点赞事件
  var zan = document.getElementsByClassName('good');                //获取所有点赞按钮
  for (let i = 0; i < zan.length; i++)                              //循环绑定
    zan[i].onclick = function () {                                  //一次绑定
      var lx = getSearch();                                         //获取当前新闻类型及 id
      var mail = decodeURIComponent(document.cookie).split(';')[0].split('=')[1];//获取用户邮箱
      this.childNodes[0].nodeValue = Number(this.childNodes[0].nodeValue) + 1;   //赞数加 1
      if (mail.search(/@/i) < 0) {                                  //检测用户是否已登录
        alert('请先登录');
        return;
      }
      var line = new XMLHttpRequest();                              //创建 ajax 对象
      line.open('post', 'http://localhost/news/index.php', true);   //初始化请求
      line.setRequestHeader("Content-type", "application/x-www-form-urlencoded");   //配置 post
      line.send('m=good&' + lx[1] + '&' + lx[2] + '&plid=' + (i + 1));              //发送请求
    }
}

function good2 () {                                                   //绑定点赞事件，用于监听事件
  setTimeout(function(){
    var zan = document.getElementsByClassName('good');                //获取所有点赞按钮
    for (let i = 0; i < zan.length; i++)                              //循环绑定
      zan[i].onclick = function () {                                  //一次绑定
        var lx = getSearch();                                         //获取当前新闻类型及 id
        var mail = decodeURIComponent(document.cookie).split(';')[0].split('=')[1];//获取用户邮箱
        if (mail.search(/@/i) < 0) {                                  //检测用户是否已登录
          alert('请先登录');
          return;
        }
        this.childNodes[0].nodeValue = Number(this.childNodes[0].nodeValue) + 1;   //赞数加 1
        var line = new XMLHttpRequest();                              //创建 ajax 对象
        line.open('post', 'http://localhost/news/index.php', true);   //初始化请求
        line.setRequestHeader("Content-type", "application/x-www-form-urlencoded");   //配置 post
        line.send('m=good&' + lx[1] + '&' + lx[2] + '&plid=' + (i + 1));              //发送请求
      }
  },1000);
}

function bgood () {                                                 //监听发表评论、查看更多评论按钮的 click 事件，对新增元素进行事件绑定
  var fbb = document.getElementById('fbbutton');                    //发表按钮
  var plb = document.getElementById('ckgdpl');                      //查看更多评论按钮
  fbb.addEventListener('click', good2);                             //监听发表按钮点击事件
  plb.addEventListener('click', good2);                             //监听发表按钮点击事件
}

addLoad(login);           //登录
addLoad(ckgd);            //查看更多按钮
addLoad(gxtime);          //更新页面时间
addLoad(pl);              //发表评论
addLoad(good);            //绑定点赞事件
addLoad(bgood);           //监听更多按钮的 click，绑定点赞事件