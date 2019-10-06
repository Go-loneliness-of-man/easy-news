function addLoad (func) {                                 //事件绑定处理函数
  var old = window.onload;
  if (typeof old != 'function') window.onload = func;     //判断是否已添加函数，若否便直接添加当前函数到 window.onload
  else window.onload = function () { old(); func(); }     //已有函数，将当前函数添加到末尾
}

function sbj (a, b) {                                     //字符串比较，相同 1、不同 0
  for (let i = 0; i < a.length || i < b.length; i++)      //遍历两字符串
    if (a[i] !== b[i]) return 0;                          //只要不同，返回 0
  return 1;                                               //完全相同，返回 1
}

function login () {                                       //登录
  var login = document.getElementById("login");
  login.onclick = function () {
    var mes = prompt("请填写邮箱和密码，用空格隔开", "邮箱（空格）密码");         //从用户获取信息
    u = mes.split(' ');                                                    //分割
    var line = new XMLHttpRequest();                                       //创建 ajax 对象
    line.open('post', 'http://localhost/news/index.php', true);            //初始化请求
    line.setRequestHeader("Content-type", "application/x-www-form-urlencoded");//配置 post
    line.send('m=login&mail=' + u[0] + '&pw=' + u[1]);                     //发送登录请求、用户名、密码
    line.onreadystatechange = function () {                                //处理响应数据
      if (this.readyState == 4) {                                          //判断响应数据是否就绪
        var u = line.responseText.split(' ');                              //转换为数组
        if (u[0] == 1) {                                                   //登录成功
          login.style = 'display:none;';                                   //隐藏登录框
          var user = document.getElementById('user');                      //获取用户元素
          user.innerHTML = u[3];                                           //添加用户名
          user.style = 'display:block;';                                   //使用户框显示
          user.onclick = function () {
            alert('邮箱：' + u[2] + '\n\n用户名：' + u[3] + '\n\n性别：' + u[4] + '\n\n年龄：' + u[5]);
          }
        }
        else if (u[0] == 2)                                                //密码错误
          alert(u[1]);
        else                                                               //失败，打开注册页面
          window.open('http://localhost/news/index.php?m=regdanye', '_blank');
      }
    }
  }
}

//获取所有记录的 id 并拼接为字符串
function huoqusuoyoujiluid () {
  var xwjl = document.getElementsByClassName('xwjlid'), xwjlid = [], xwjlid2 = '', i;
  for (i = 0; i < xwjl.length; i++)                                     //获取 id
    xwjlid[i] = xwjl[i].childNodes[0].nodeValue;
  for (i = 1, xwjlid2 = xwjlid[0]; i < xwjlid.length; i++)              //拼接为字符串
    xwjlid2 = xwjlid2 + 'a' + xwjlid[i];
  return xwjlid2;
}

//当 cookie 存在时自动登录
var loginanniu = document.getElementById("login");                     //获取登录框
user = decodeURIComponent(document.cookie).split(';');                 //获取 cookie
user[0] = user[0].split('=')[1];                                       //去掉前缀
var line = new XMLHttpRequest();                                       //创建 ajax 对象
line.open('post', 'http://localhost/news/index.php', true);            //初始化请求
line.setRequestHeader("Content-type", "application/x-www-form-urlencoded");//配置 post
line.send('m=login&mail=' + user[0] + '&pw=' + user[1]);               //发送登录请求、用户名、密码
line.onreadystatechange = function () {                                //处理响应数据
  if (this.readyState == 4) {                                          //判断响应数据是否就绪
    var u = line.responseText.split(' ');                              //转换为数组
    if (u[0] == 1) {                                                   //登录成功
      loginanniu.style = 'display:none;';                              //隐藏登录按钮
      var user = document.getElementById('user');                      //获取用户元素
      user.innerHTML = u[3];                                           //添加用户名
      user.style = 'display:block;';                                   //使用户框显示
      user.onclick = function () {
        alert('邮箱：' + u[2] + '\n\n用户名：' + u[3] + '\n\n性别：' + u[4] + '\n\n年龄：' + u[5]);
      }
    }
  }
}