<?php

// 命名空间
namespace core;
use core\dao;

// 抽象类，只能被继承，不能实例化
abstract class publicService {

  // 保存 dao 实例
  protected $dao;

  // 构造函数
  public function __construct() {

  }

  // 标准返回格式
  public function res($code, $message, $result, $total = 0) {
    return json_encode([
      'code' => $code,
      'message' => $message,
      'result' => $result,
      'total' => $total === 0 ? null : $total
    ]);
  }

  // 常用代码片段，增，参数依次是模型、成功消息、失败消息、要插入的数据（关联数组）
  public function oftenC($model, $success = 'success', $fault = 'fault', $body) {
    $code = 200;
    $res = $model->create($body);                                           // 插入记录
    if($res < 1)  $code = 400;                                              // 判断状态码
    return $this->res($code, $res > 0 ? $success : $fault, $res);           // 返回固定格式
  }

  // 常用代码片段，删，参数依次是模型、成功消息、失败消息、删除条件
  public function oftenD($model, $success = 'success', $fault = 'fault', $where) {
    $code = 200;
    $res = $model->delete($where);                                          // 删除记录
    if($res < 1)  $code = 400;                                              // 判断状态码
    return $this->res($code, $res > 0 ? $success : $fault, $res);           // 返回固定格式
  }

  // 常用代码片段，改，参数依次是模型、成功消息、失败消息、修改对象、修改条件
  public function oftenU($model, $success = 'success', $fault = 'fault', $obj, $where) {
    $code = 200;
    $res = 0;
    foreach($obj as $k => $v)                                               // 遍历修改所有属性
      $res = $model->update($k, $v, $where);
    if($res < 1)  $code = 400;                                              // 判断状态码
    return $this->res($code, $res > 0 ? $success : $fault, $res);           // 返回固定格式
  }

  // 常用代码片段，查，参数依次是模型、成功消息、id 列表
  public function oftenR($model, $message = 'success', $list = []) {
    if(count($list) > 0)
      $res = $model->get($list);
    else
      $res = $model->dao->query("SELECT * FROM $model->dbname.$model->table");
    return $this->res(200, $message, $res, count($res));                    // 返回固定格式
  }
}



