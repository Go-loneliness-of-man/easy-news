function qiehuan () {                                                          //绑定侧栏按钮事件，当点击左侧选项时，隐藏当前主栏元素，显现被点击元素对应的主栏
  var xx = document.getElementsByClassName("xuanxiang");                       //获取所有选项元素
  for (let j = 0; j < xx.length; j++)                                          //绑定所有点击事件
    xx[j].onclick = function () {                                              //进行一次绑定
      var daprace = document.getElementsByClassName("dakuang"), i;             //获取所有主栏元素
      for (i = 0; i < daprace.length; i++)                                     //找出当前处于可见状态的元素
        if (sbj(window.getComputedStyle(daprace[i], null).display, "block")) { //找到，令其消失，跳出
          daprace[i].style.display = "none";                                   //消失
          break;
        }
      for (i = 0; i < daprace.length; i++)                                     //找出当前选项所对应的主栏元素
        if (sbj(daprace[i].childNodes[1].childNodes[0].nodeValue, this.childNodes[0].nodeValue)) {   //找到，令其可见，跳出
          daprace[i].style.display = "block";                                  //可见
          break;
        }
    }
}

function select () {                                                           //绑定查询按钮事件，利用 ajax 发送请求
  var button = document.getElementById('sech');                                //获取元素
  button.onclick = function () {                                               //进行一次绑定

    var daprace = document.getElementsByClassName("dakuang"), i;               //获取所有主栏元素
    for (i = 0; i < daprace.length; i++)                                       //找出当前处于可见状态的元素
      if (sbj(window.getComputedStyle(daprace[i], null).display, "block"))     //找到，跳出
        break;

    var line = new XMLHttpRequest();                                           //创建 ajax 对象
    line.open('post', 'http://localhost/news/php/houtai.php', true);           //初始化请求
    line.setRequestHeader("Content-type", "application/x-www-form-urlencoded");//配置 post
    line.send('m=sech&sech=' + document.getElementById('sechinput').value + '&dakuang=' + i); //发送 post 请求

    //处理响应数据
    line.onreadystatechange = function () {
      if (this.readyState == 4) {                                              //判断响应数据是否就绪
        if (i == 0)                                                            //新闻列表
          var jiluk = document.getElementById("jiluk");                        //获取新闻列表的记录框外层节点
        else                                                                   //专题列表
          var jiluk = document.getElementById("ztjiluk");                      //获取新闻列表的记录框外层节点
        jiluk.innerHTML = line.responseText;                                   //插入 HTML 代码
      }
    }
  }
}

function pageclick () {                                                        //绑定新闻列表处的分页按钮事件
  var page = document.getElementById('page').getElementsByTagName('li');       //获取列表元素
  page = Array.prototype.slice.call(page, 0);                                  //转换 nodelist 为 array
  for (let i = 0; i < 6; i++)
    page[i].onclick = function () {                                            //一次绑定
      var line = new XMLHttpRequest();                                         //创建 ajax 对象
      line.open('post', 'http://localhost/news/php/houtai.php', true);         //初始化请求
      line.setRequestHeader("Content-type", "application/x-www-form-urlencoded");//配置 post
      line.send('m=sech&sech=' + document.getElementById('sechinput').value + '&page=' + this.childNodes[0].nodeValue);    //发送 post 请求
      line.onreadystatechange = function () {                                  //处理响应数据
        if (this.readyState == 4) {                                            //判断响应数据是否就绪
          var jiluk = document.getElementById("jiluk");                        //获取新闻列表的记录框外层节点
          jiluk.innerHTML = line.responseText;                                 //插入 HTML 代码
        }
      }
    }
  page[6].onclick = function () {                                              //给最后一个按钮绑定事件
    for (let j = 1; j < 6; j++)                                                //第 2 至 5 个按钮数字加 1
      page[j].childNodes[0].nodeValue = Number(page[j].childNodes[0].nodeValue) + 1;
    page[0].childNodes[0].nodeValue = '《';                                    //第一个按钮变为向左 《
    page[0].onclick = function () {                                            //第一个按钮重新绑定事件
      for (let j = 1; j < 6; j++)                                              //第 2 至 5 个按钮数字减 1
        page[j].childNodes[0].nodeValue = Number(page[j].childNodes[0].nodeValue) - 1;
      if (page[1].childNodes[0].nodeValue == 2) {                               //必要时，第一个按钮变回 1
        this.childNodes[0].nodeValue = 1;
        this.onclick = function () {                                           //对第一个按钮的事件进行重新绑定
          var line = new XMLHttpRequest();                                     //创建 ajax 对象
          line.onreadystatechange = function () {                              //处理响应数据
            if (this.readyState == 4) {                                        //判断响应数据是否就绪
              var jiluk = document.getElementById("jiluk");                    //获取新闻列表的记录框外层节点
              jiluk.innerHTML = line.responseText;                             //插入 HTML 代码
            }
          }
          line.open('post', 'http://localhost/news/php/houtai.php', true);     //初始化请求
          line.setRequestHeader("Content-type", "application/x-www-form-urlencoded");//配置 post
          line.send('m=sech&sech=' + document.getElementById('sechinput').value + '&page=' + this.childNodes[0].nodeValue);    //发送 post 请求
        }
      }
    }
  }
}

function ztpageclick () {                                                      //绑定专题列表处的分页按钮事件
  var page = document.getElementById('ztpage').getElementsByTagName('li');     //获取列表元素
  page = Array.prototype.slice.call(page, 0);                                  //转换 nodelist 为 array
  for (let i = 0; i < 6; i++)
    page[i].onclick = function () {                                            //一次绑定
      var line = new XMLHttpRequest();                                         //创建 ajax 对象
      line.onreadystatechange = function () {                                  //处理响应数据
        if (this.readyState == 4) {                                            //判断响应数据是否就绪
          var jiluk = document.getElementById("ztjiluk");                      //获取新闻列表的记录框外层节点
          jiluk.innerHTML = line.responseText;                                 //插入 HTML 代码
        }
      }
      line.open('post', 'http://localhost/news/php/houtai.php', true);         //初始化请求
      line.setRequestHeader("Content-type", "application/x-www-form-urlencoded");//配置 post
      line.send('m=sech&sech=' + document.getElementById('sechinput').value + '&page=' + this.childNodes[0].nodeValue + '&dakuang=2'); //发送 post 请求
    }
  page[6].onclick = function () {                                              //给最后一个按钮绑定事件
    for (let j = 1; j < 6; j++)                                                //第 2 至 5 个按钮数字加 1
      page[j].childNodes[0].nodeValue = Number(page[j].childNodes[0].nodeValue) + 1;
    page[0].childNodes[0].nodeValue = '《';                                    //第一个按钮变为向左 《
    page[0].onclick = function () {                                            //第一个按钮重新绑定事件
      for (let j = 1; j < 6; j++)                                              //第 2 至 5 个按钮数字减 1
        page[j].childNodes[0].nodeValue = Number(page[j].childNodes[0].nodeValue) - 1;
      if (page[1].childNodes[0].nodeValue == 2) {                              //必要时，第一个按钮变回 1
        this.childNodes[0].nodeValue = 1;
        this.onclick = function () {                                           //对第一个按钮的事件进行重新绑定
          var line = new XMLHttpRequest();                                     //创建 ajax 对象
          line.onreadystatechange = function () {                              //处理响应数据
            if (this.readyState == 4) {                                        //判断响应数据是否就绪
              var jiluk = document.getElementById("ztjiluk");                  //获取新闻列表的记录框外层节点
              jiluk.innerHTML += line.responseText;                            //插入 HTML 代码
            }
          }
          line.open('post', 'http://localhost/news/php/houtai.php', true);     //初始化请求
          line.setRequestHeader("Content-type", "application/x-www-form-urlencoded");//配置 post
          line.send('m=sech&sech=' + document.getElementById('sechinput').value + '&page=' + this.childNodes[0].nodeValue + '&dakuang=2');    //发送 post 请求
        }
      }
    }
  }
}

function delok () {                                                            //对每条记录的删除、查看操作进行绑定
  setTimeout(function () {                                                     //2 秒后执行绑定
    var daprace = document.getElementsByClassName("dakuang"), i, dl = [], d = [];//声明变量
    for (i = 0; i < daprace.length; i++)                                       //找出当前处于可见状态的元素
      if (sbj(window.getComputedStyle(daprace[i], null).display, "block"))     //找到，跳出
        break;

    if (i == 0)                                                                //判断获取哪个 dakuang 的记录列表
      d = document.getElementById("jiluk").getElementsByClassName("jilu");
    else
      d = document.getElementById("ztjiluk").getElementsByClassName("jilu");
    for (let j = 0; j < d.length && i == 0; j++)                               //找到 del、look 的父元素，新闻列表
      dl[j] = d[j].getElementsByTagName('li')[6];
    for (let j = 0; j < d.length && i == 2; j++)                               //找到 del、look 的父元素，专题列表
      dl[j] = d[j].getElementsByTagName('li')[3];

    for (var k = 0; k < dl.length; k++) {                                      //绑定删除、查看事件
      dl[k].childNodes[0].onclick = function () {                              //删除
        var line = new XMLHttpRequest();                                       //创建 ajax 对象
        line.open('post', 'http://localhost/news/php/houtai.php', true);       //初始化请求
        line.setRequestHeader("Content-type", "application/x-www-form-urlencoded");//配置 post
        if (i) {                                                               //专题，包含 m、id、dakuang 这几个字段
          var temp = this.parentNode.previousElementSibling.previousElementSibling.previousElementSibling;
          line.send('m=del&id=' + temp.childNodes[0].nodeValue + '&dakuang=' + i);
          document.getElementById("ztjiluk").removeChild(this.parentNode.parentNode);
        }
        else {                                                                 //新闻，包含 m、lx、id、dakuang 这几个字段
          var temp = this.parentNode.previousElementSibling.previousElementSibling.previousElementSibling.previousElementSibling;
          line.send('m=del&lx=' + temp.childNodes[0].nodeValue + '&id=' + temp.previousElementSibling.previousElementSibling.childNodes[0].nodeValue + '&dakuang=' + i);
          document.getElementById("jiluk").removeChild(this.parentNode.parentNode);
        }
      }

      dl[k].childNodes[1].onclick = function () {                              //查看
        var line = new XMLHttpRequest();                                       //创建 ajax 对象
        line.open('post', 'http://localhost/news/php/houtai.php', true);       //初始化请求
        line.setRequestHeader("Content-type", "application/x-www-form-urlencoded");//配置 post
        line.onreadystatechange = function () {                                //处理响应数据
          if (this.readyState == 4) {                                          //判断响应数据是否就绪
            w = window.open();                                                 //新建窗口
            w.document.open('text/html');                                      //在窗口内创建一个输出流
            w.document.write(line.responseText);                               //写入内容
            w.document.close();                                                //输出并关闭输出流
          }
        }
        if (i) {                                                               //专题，包含 m、id、dakuang 这几个字段
          var temp = this.parentNode.previousElementSibling.previousElementSibling.previousElementSibling;
          line.send('m=lok&id=' + temp.childNodes[0].nodeValue + '&dakuang=' + i);
        }
        else {                                                                 //新闻，包含 m、lx、id、dakuang 这几个字段
          var temp = this.parentNode.previousElementSibling.previousElementSibling.previousElementSibling.previousElementSibling;
          line.send('m=lok&lx=' + temp.childNodes[0].nodeValue + '&id=' + temp.previousElementSibling.previousElementSibling.childNodes[0].nodeValue + '&dakuang=' + i);
        }
      }
    }
  }, 1200);
}

function delokjt () {                                                          //监听新闻列表、专题列表、搜索按钮的点击事件
  function jt (obj) { obj.addEventListener('click', delok); }                  //单个监听
  var page = document.getElementById('page').getElementsByTagName('li');       //获取新闻列表的翻页元素
  var ztpage = document.getElementById('ztpage').getElementsByTagName('li');   //获取专题列表的翻页元素
  var sech = document.getElementById('sech');                                  //获取搜索按钮元素
  for (let i = 0; i < 6; i++) {                                                //监听所有翻页按钮
    jt(page[i]);                                                               //新闻翻页按钮
    jt(ztpage[i]);                                                             //专题翻页按钮
  }
  jt(sech);                                                                    //监听搜索按钮
}

//监听列表的 onclick 事件，在发生 onclick 三秒后对其进行事件绑定

addLoad(qiehuan);                                                              //切换主栏
addLoad(select);                                                               //查询按钮
addLoad(pageclick);                                                            //页码列表
addLoad(ztpageclick);                                                          //专题页码列表
addLoad(delokjt);                                                              //绑定删除、查询事件