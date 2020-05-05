<?php

// 命名空间
namespace app\api\controller;
use \core\publicController;
use \app\api\service\news as service;           // service

class news extends publicController {

  public function add() {
    $rule = ['master' => 'integer', 'title' => 'string', 'content' => 'string'];
    $params = $this->get(); // 获取参数
    $this->rule($rule, $params); // 参数校验
    $service = new service();
    $params['time'] = time() * 1000; // 适应前端 js 以 ms 为单位
    $params['theme_id'] = $params['theme_id'] ? $params['theme_id'] : -1; // 默认 -1 代表空
    $params['branch'] = $params['branch'] ? $params['branch'] : '[]'; // 默认空数组
    return $service->add($params);
  }

  public function del() {
    return $this->oftenCode(new service(), [], 'del');
  }

  public function revise() {
    return $this->oftenCode(new service(), [], 'revise');
  }

  public function read() {
    return $this->oftenCode(new service(), [], 'read');
  }

  public function uploadFile() {
    return $this->oftenCode(new service(), [], 'uploadFile');
  }
}

