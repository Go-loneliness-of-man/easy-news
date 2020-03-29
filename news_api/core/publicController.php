<?php

// 命名空间
namespace core;

// 抽象类，只能被继承，不能实例化
abstract class publicController {

  // 输出提示并跳转，参数是消息、模块、控制器、操作、时间、参数，只有第一个是必选的
  protected function jump($msg, $m = M, $c = C, $a = A, $time = 5, $params = []) {
    global $config;                                                 // 引入全局配置变量
    $refresh = 'Refresh:'.$time.';url='.$config['URL'][0]."$m/$c/$a?params=".json_encode($params);  // 拼接请求头
    header($refresh);                                               // 跳转
    echo $msg;                                                      // 输出消息
    exit;
  }

  // 参数校验，参数依次是规则、参数
  protected function rule($rule, $params) {
    foreach($rule as $k => $v)                                      // 遍历参数
      if(gettype(@$params[$k]) !== $v) {                            // 判断参数类型是否与规则相同
        echo '参数错误，参数 '.$k.' 应为 '.$v.' 类型';                // 不同，输出错误
        exit;                                                       // 结束
      }
  }

  // 获取请求参数，默认进行类型转换
  protected function get($isJson = 0, $convert = 1) {
    $res = [];                                                      // 准备结果
    if(!$isJson && $convert)                                        // 不是 json 且转换
      foreach($_REQUEST as $k => $v) {                              // 遍历请求参数
        if(preg_match_all('/^[\d]+$/', $v))                         // 检测字符串中是否仅包含数字，若是则转换为 integer
          $res[$k] = intval($v);                                    // 转换为数值并赋值
        else if(preg_match_all('/^[\d]+[\.]?[\d]+$/', $v))          // 检测字符串中是否仅包含数字、一个小数点，若是则转换为 double
        $res[$k] = floatval($v);                                    // 转换为数值并赋值
        else
          $res[$k] = $v;                                            // 字符串，直接赋值
      }
    else if($isJson)                                                // 是 json
      foreach($_REQUEST as $k => $v)                                // 遍历请求参数
        $res[$k] = json_decode($v);                                 // 转换一次
    return $isJson ? $res : ($convert ? $res : $_REQUEST);          // 若是 json 或需要类型转换则返回 $res，否则返回 $_REQUEST
  }

  // 常用控制器代码片段，参数依次是服务实例、校验规则、执行的服务方法名
  public function oftenCode($service, $rule = [], $method, $isJson = 0, $convert = 1) {
    $params = $this->get($isJson, $convert);                        // 获取参数
    $this->rule($rule, $params);                                    // 参数校验
    return $service->$method($params);                              // 调用服务
  }

  // ********************************************************* 以下为模板引擎 ********************************************************************

  // 渲染视图所需的模板变量
  public $templateVar = [];

  // 接收变量到控制器内部
  public function loadVar($key, $value) {
    if(is_array($key))                                              // 判断是否批量传参
      foreach($key as $k => $v)                                     // 批量接收
        $this->templateVar[$k] = $v;                                // 接收一次
    else
      $this->templateVar[$key] = $value;                            // 接收单个
  }

  // 对模版进行替换并输出
  public function show($view) {
    $exists = file_exists(APP_PATH.M.'/view/'.$view.'.view.php');

    // 检测是否已经生成过 .view 文件、.view 文件是否是最新的，若未生成、不是最新的则生成 .view 文件（是否最新是根据 .view.php 生成时间是否大于 .php 来判断的，若大于则最新，否则不是最新）
    if(!$exists || ($exists && (filemtime(APP_PATH.M.'/view/'.$view.'.view.php') < filemtime(APP_PATH.M.'/view/'.$view.'.php')))) {
      $s = file_get_contents(APP_PATH.M.'/view/'.$view.'.php');     // 获取模版内容
      $s = str_replace('{{','<?php echo $this->templateVar[\'', $s);// 替换左边
      $s = str_replace('}}','\']; ?>',$s);                          // 替换右边
      $s = preg_replace('/[\s]{2,}/', ' ', $s);                     // 压缩空格、换行、制表符
      $file = fopen(APP_PATH.M.'/view/'.$view.'.view.php', 'wb+');  // 打开（创建）并清空文件
      fwrite($file, $s);                                            // 写入内容
      fclose($file);                                                // 关闭文件
    }

    // 包含视图文件
    include_once(APP_PATH.M.'/view/'.$view.'.view.php');
  }
}



