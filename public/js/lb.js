function lbt (img, l, r, tyc, tyd, w, f) {                 //封装轮播图，参数依次是图片、左右按钮、延迟秒、移动秒、图片宽度、轮播方向（1 代表左，0 代表右）
  function left () {                                       //向左移动一次的动画
    for (let i = 0; i < count; i++)                        //所有图片向左移动
      img[i].style.animation = 'lb' + i + ' ' + tyd + 's linear 1 normal';
  }

  function lmw () {                                        //使动画停在末状态并更新数组位置
    for (let i = 1; i < count; i++)                        //使各图片停在末状态
      img[i].style.right = -(i - 1) * w + 'px';
    img[0].style.right = -(count - 1) * w + 'px';         //在页面中移动第一张图片到末尾
    img[count] = img[0];                                  //在数组中将第一个换到最后
    for (let i = 0; i < count; i++)                       //更新数组位置信息
      img[i] = img[i + 1];
  }

  function right () {                                      //向右移动一次的动画
    img[count - 1].style.right = w + 'px';                //末尾图片移动到左边
    img[count - 1].style.animation = 'lb1 ' + tyd + 's linear 1 normal';  //左边向右移动
    for (let i = 0; i < count - 1; i++)                   //所有图片向右移动
      img[i].style.animation = 'lb' + (i + 2) + ' ' + tyd + 's linear 1 normal';
  }

  function rmw () {                                        //使动画停在末状态并更新数组位置
    for (let i = 0; i < count - 1; i++)                   //使图片停在末状态
      img[i].style.right = -(i + 1) * w + 'px';
    img[count - 1].style.right = '0px';                   //更新最后一张图的末位置
    img[count] = img[count - 1];                          //准备将最后一个换到第一位
    for (let i = count - 1; i > 0; i--)                   //更新数组位置信息
      img[i] = img[i - 1];
    img[0] = img[count];                                  //将最后一个换到第一位
  }

  function anclick () {                                    //绑定按钮点击事件
    l.onclick = function () {
      click.sj[++click.top] = 1;                          //事件入栈
    };
    r.onclick = function () {
      click.sj[++click.top] = 0;                          //事件入栈
    };
  }

  function qx () {                                         //若存在点击事件，取消默认轮播
    e++;
    if (click.top >= 0) {
      clearTimeout(a);                                    //取消默认轮播
      clearTimeout(b);
      clearTimeout(c);
      for (let i = e; i < d.length; i++)                   //取消此后的检测
        clearTimeout(d[i]);
      lb();                                               //立即执行点击事件
    }
  }

  function lb () {                                        //轮播图主进程
    if (click.top < 0) {                                  //没有点击事件
      if (f) {                                            //判断轮播方向
        a = setTimeout(left, tyc * 1000);                 //执行动画，获得取消号
        b = setTimeout(lmw, tz * 1000);                   //更新末状态，获得取消号
      }
      else {
        a = setTimeout(right, tyc * 1000);                //执行动画，获得取消号
        b = setTimeout(rmw, tz * 1000);                   //更新末状态，获得取消号
      }
      e = 0;                                              //用于标记每个检测事件的超时调用的位置
      for (let i = 0; i < tyc * 1000; i += 200)           //每 200ms 检测一次
        d[i] = setTimeout(qx, i);                         //当存在点击事件时，取消默认轮播
      c = setTimeout(lb, tz * 1000);                      //继续下一次轮播
    }
    else if (click.sj[click.top]) {                       //左按钮
      left();                                             //执行动画
      setTimeout(lmw, tyd * 1000);                        //更新末状态
      click.top--;                                        //事件出栈
      setTimeout(lb, tyd * 1000);                         //继续下一次轮播
    }
    else {                                                //右按钮
      right();                                            //执行动画
      setTimeout(rmw, tyd * 1000);                        //更新末状态
      click.top--;                                        //事件出栈
      setTimeout(lb, tyd * 1000);                         //继续下一次轮播
    }
  }

  var tz = tyc + tyd, a, b, c, count = img.length;        //总秒数、默认超时调用号 a 和 b 和 c、图片数 count、循环变量 i
  var d = [], e;                                          //检测点击事件的超时调用号
  var click = Object();                                   //事件栈
  var sheet = document.styleSheets[0];                    //获取样式表

  for (let i = 0; i < count + 2; i++)                     //生成动画样式
    sheet.insertRule("@keyframes lb" + i + "{100%{right:" + ((1 - i) * w) + "px;}}", i);
  for (let i = 0; i < count; i++)                         //生成位置信息
    img[i].style.right = -i * w + 'px';
  img = Array.prototype.slice.call(img, 0);               //转换 nodelist 为 array，（nodelist 反映的是 DOM 节点的实时结构，是只读的，要想像数组一样操作必须先转换为 array）
  click.top = -1; click.sj = Array();                     //初始化事件栈
  addLoad(anclick);                                       //执行事件绑定
  lb();                                                   //执行主进程
}

var img = document.getElementById("lb").getElementsByTagName('figure');  //获取轮播元素
var l = document.getElementById('l');                     //获取两边的按钮元素
var r = document.getElementById('r');
lbt(img, l, r, 2, 0.5, 850, 1);                           //执行轮播图进程

function ckgd () {
  var ckgdanniu = document.querySelectorAll('div[id^="ckgd"]');
  ckgdanniu[0].onclick = function () {
    xwjlid = huoqusuoyoujiluid();                                      //获取记录 id 字符串
    lx = document.getElementsByClassName('clicked')[0].childNodes[0].nodeValue;//获取当前类型
    var line = new XMLHttpRequest();                                   //创建 ajax 对象
    line.open('post', 'http://localhost/news/index.php', true);        //初始化请求
    line.setRequestHeader("Content-type", "application/x-www-form-urlencoded");//配置 post
    line.send('m=ckgd&id=' + xwjlid + '&anniu=0&lx=' + lx);            //发送请求
    line.onreadystatechange = function () {                            //处理响应数据
      if (this.readyState == 4) {                                      //判断响应数据是否就绪
        if (line.responseText != '') {
          var k = document.getElementById("rdjlk");
          k.innerHTML += line.responseText;                            //显示数据
        }
        else alert('没有更多新闻了 >_<');
      }
    }
  }

  for (i = 1; i < 4; i++)
    ckgdanniu[i].onclick = function () {
      xwjlid = huoqusuoyoujiluid();                                    //获取记录 id 字符串
      ulk = this.previousElementSibling;                               //获取 ul
      lx = document.getElementsByClassName('clicked')[0].childNodes[0].nodeValue;//获取当前新闻类型
      var line = new XMLHttpRequest();                                 //创建 ajax 对象
      line.open('post', 'http://localhost/news/index.php', true);      //初始化请求
      line.setRequestHeader("Content-type", "application/x-www-form-urlencoded");//配置 post
      line.send('m=ckgd&id=' + xwjlid + '&anniu=1&lx=' + lx);          //发送请求
      line.onreadystatechange = function () {                          //处理响应数据
        if (this.readyState == 4)                                      //判断响应数据是否就绪
          if (line.responseText != '')
            ulk.innerHTML += line.responseText;                        //显示数据
          else alert('没有更多新闻了 >_<');
      }
    }
}

addLoad(login);
addLoad(ckgd);