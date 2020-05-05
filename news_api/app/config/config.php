<?php

// 全局配置数组
$config = [

  // 数据库配置
  'database' => 
  [ 'type' => 'mysql', 'host' => '47.244.189.67', 'port' => '3306', 'user' => 'news', 'pwd' => '123', 'charset' => 'utf8', 'dbname' => 'news', 'front' => '', 'behind' => '_t'],
  // [ 'type' => 'mysql', 'host' => 'localhost', 'port' => '3306', 'user' => 'root', 'pwd' => '123', 'charset' => 'utf8', 'dbname' => 'news', 'front' => '', 'behind' => '_t'],

  // 系统配置
  'system' => [

    // 判断输出哪些错误处理，E_ALL 代表所有输出所有错误信息
    'error_reporting' => NULL,

    // 是否显示错误信息
    'display_errors' => 1
  ],

  // 配置公共函数文件
  'publicFuncFile' => [

    // 系统函数开始，rw 很多部分会对以下函数存在依赖，请不要轻易删除
    'public.php', // 常用公共函数
    'graph.php', // 图像处理函数
    // 系统函数结束
  ],

  // 项目 URL
  'URL' => [
    'local' => 'http://newsapi.com/', // 测试
    // 'local' => 'http://newsapi.golone.xyz/', // 生产
  ],

  // 配置中间件，执行顺序与数组顺序相同，每个元素依次是类名、方法名
  'middlewareFile' => [
    ['test', 'testFunc'],
  ],
];

