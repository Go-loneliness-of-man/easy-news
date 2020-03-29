<?php

// 命名空间
namespace core;

// 负责路由相关操作
class route {

    public static $route;

    // 遍历路由注册表，若存在匹配路由，取出其模块、控制器、操作并定义为常量
    public static function router($path) {
        global $route;                                       // 声明全局变量 route
        include_once(CONFIG_PATH.'route.php');               // 读取路由配置文件
        self::$route = $route;                               // 保存路由配置全局变量到静态属性
        for($i = 0, $c = count($route); $i < $c; $i++)       // 遍历判断是否存在匹配的已注册路由
            if(self::match($path, $route[$i][0])) {          // 若匹配，则定义模块、控制器、操作为常量，并解析 params 参数
                define('M', $route[$i][1]);                  // 定义模块、控制器、操作为常量
                define('C', $route[$i][2]);
                define('A', $route[$i][3]);
                route::params($path, $route[$i][0]);         // 解析 params 参数到 $_REQUEST
                return 0;                                    // 已匹配，不进行默认路由
            }
        return 1;                                            // 无匹配的已注册路由，使用默认路由
    }

    // 解析 params 参数到 $_REQUEST，参数 $path、$route 依次是根据 / 切割后的 url、路由字符串
    public static function params($path, $route) {
        $route = explode('/:', $route);                      // 从注册的路由中取出参数的 key
        $path = explode('/', explode($route[0], $path)[1]);  // 从 url 中取出参数的值
        $c = count($route);
        if($c < count($path)) {                              // 判断参数是否过多
            echo 'error：路由参数过多';
            exit;
        }
        for($i = 1; $i < $c; $i++)                           // 循环将参数赋值到 $_REQUEST
            if($path[$i] !== NULL)                           // 判断是否存在该 key 对应的值
                $_REQUEST[$route[$i]] = $path[$i];           // 赋值一次
            else {                                           // 不存在该 key 对应的值，抛出错误
                echo 'error：缺少路由参数 '.$route[$i];
                exit;
            }
    }

    // 判断路由匹配
    public static function match($path, $route) {
        $baseRoute = explode('/:', $route)[0];               // 去掉已注册路由的参数
        $path = explode('/', $path);                         // 切割 path
        $basePath = '';                                      // 准备 basepath
        for($i = count($path), $c = count(explode('/:', $route)); $c > 0; $i--, $c--)            // 去掉 path 末尾的 params 参数
            unset($path[$i]);
        for($i = 1, $c = count($path); $i < $c; $i++)        // 拼接 basepath
            $basePath = $basePath.'/'.$path[$i];             // 拼接一次
        return $basePath === $baseRoute;                     // 返回判断结果
    }
}

