<?php

// 命名空间
namespace core;

// 安全判定，若请求不是来自入口文件，跳转回入口文件，通过 defined() 判断一个常量是否存在
if(!defined('ASINDEX')) {
  header('location:../public/index.php');                 // 跳转至首页
  exit;                                                   // 退出
}

// 创建初始化类
class app {

  // 入口方法
  public static function start() {
    self::setPath();                                      // 定义路径常量
    self::setUrl();                                       // 解析 URL
    self::setAutoLoad();                                  // 开启自动加载
    self::setConfig();                                    // 加载配置文件
    self::setError();                                     // 配置错误控制
    self::setPublicFunc();                                // 加载公共函数
    self::middleware();                                   // 执行中间件
    return self::setDispath();                            // 分发控制器并返回结果
  }

  // 定义路经常量，包括框架基本目录路径，各个模块的控制器模型视图目录路径。
  private static function setPath() {
    define('APP_PATH',    ROOT_PATH.'app/');              // 应用目录
    define('CORE_PATH',   ROOT_PATH.'core/');             // 框架核心目录
    define('PLUGIN_PATH', ROOT_PATH.'plugIn/');           // 插件目录
    define('CONFIG_PATH', APP_PATH.'config/');            // 全局配置文件目录
    define('PUBLICFUNC_PATH', APP_PATH.'publicFunc/');    // 公共函数目录
    define('MIDDLEWARE_PATH', APP_PATH.'middleware/');    // 中间件目录
  }

  // 解析 URL 分配路由，默认路由 M、C、A 依次代表模块（module）、控制器（controller）、操作（action）
  private static function setUrl() {
    include_once(CORE_PATH.'route.php');                  // 加载路由类
    $flag = isset($_SERVER['PATH_INFO']) ? route::router($_SERVER['PATH_INFO']) : 1;   // 判断是否是已注册的路由

    // 不是已注册路由，解析为默认路由
    if($flag) {
      $path = isset($_SERVER['PATH_INFO']) ? explode('/', $_SERVER['PATH_INFO']) : []; // 切割路由
      define('M', isset($path[1]) ? $path[1] : 'index');  // 默认 index 模块
      define('C', isset($path[2]) ? $path[2] : 'index');  // 默认 index 控制器
      define('A', isset($path[3]) ? $path[3] : 'index');  // 默认 index 操作

      // 若模块不存在，抛出错误
      if(!file_exists(APP_PATH.@$path[1])) {
        echo 'error：不存在模块目录 '.APP_PATH."$path[1]，字符串“".$_SERVER['PATH_INFO'].'” 也不是已注册的路由';
        exit;
      }
    }
  }

  // 自动加载类的方法，包括 core、模块的控制器和模型类、vendor 内的类文件，每次接收一个类名
  private static function setAutoLoadFunction($class) {
    $class = rwBaseName($class);                          // 去掉命名空间前缀，只保留类名
    $path = [                                             // 拼接路径
      CORE_PATH.$class.'.php',                            // 加载框架核心类，core
      APP_PATH.M.'/controller/'.$class.'.php',            // 加载模块控制器
      APP_PATH.M.'/model/'.$class.'.php',                 // 加载模块模型
      APP_PATH.M.'/service/'.$class.'.php',               // 加载模块服务
      MIDDLEWARE_PATH.$class.'.php',                      // 加载中间件类，middleware
      PLUGIN_PATH.$class.'.php'                           // 加载插件类，vendor
    ];

    // 依次判断加载核心类、控制器类、模型类、服务类、中间件类、插件类
    $non = true;
    foreach($path as $v)
      if(file_exists($v)) {                               // 找到则加载并跳出
        $non = false;
        include_once($v);
      }

    // 未找到类，设置响应头为 404 并结束
    if($non) {
      http_response_code(404);
      echo "找不到类 $class ，请检查路由/路径是否正确";
      exit;
    }
  }

  // 将自动加载方法注册到 php 内置的自动加载栈
  private static function setAutoLoad() {
    spl_autoload_register(array(__CLASS__, 'setAutoLoadFunction'));
  }

  // 加载配置文件
  private static function setConfig() {
    global $config;                                       // 声明全局配置变量 config
    $m = M;                                               // 准备模块配置变量名
    include(CONFIG_PATH.'config.php');                    // 加载全局配置文件
    include(APP_PATH.M.'/config.php');                    // 加载模块配置文件

    // 用模块配置覆盖全局配置的部分参数
    foreach($$m as $k => $v)  $config[$k] = $$m[$k];
  }

  // 配置错误控制
  private static function setError() {
    global $config;                                       // 引入全局变量 config 到当前函数作用域
    @ini_set('error_reporting', $config['system']['error_reporting']);                    // 判断输出哪些错误信息，E_ALL 代表所有输出所有错误信息
    @ini_set('display_errors', $config['system']['display_errors']);                      // 是否显示错误信息
  }

  // 加载公共函数
  private static function setPublicFunc() {
    global $config;                                       // 引入全局变量 config 到当前函数作用域
    for($i = 0, $c = count($config['publicFuncFile']); $i < $c; $i++)                     // 加载所有公共函数文件
      file_exists(APP_PATH.M.'/'.$config['publicFuncFile'][$i]) ?                         // 判断是否存在模块同名的模块公共函数
      include_once(APP_PATH.M.'/'.$config['publicFuncFile'][$i]) :
      include_once(PUBLICFUNC_PATH.$config['publicFuncFile'][$i]);
  }

  // 执行中间件
  private static function middleware() {
    global $config;                                       // 引入全局变量 config 到当前函数作用域
    for($i = 0, $c = count($config['middlewareFile']); $i < $c; $i++) {                    // 执行所有已配置的中间件
      $class = 'app\middleware\\'.$config['middlewareFile'][$i][0];                        // 拼接类名
      $method = $config['middlewareFile'][$i][1];                                          // 拼接方法名
      $class::$method($_REQUEST);                                                          // 执行中间件
    }
  }

  // 分发控制器
  private static function setDispath() {
    $m = M;                                               // 取出便于拼接
    $c = C;
    $a = A;
    $controller = "\\app\\$m\\controller\\$c";            // 组成控制器名
    $c = new $controller();                               // 实例化控制器
    return $c->$a();                                      // 调用操作并返回结果
  }
}




