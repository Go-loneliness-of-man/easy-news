<?php

// 命名空间
namespace app\api\controller;
use \core\publicController;
use \app\api\service\theme as service;           // service

class theme extends publicController {

  public function add() {
    $rule = ['title' => 'string', 'master' => 'integer'];
    $this->rule($rule, $this->get()); // 参数校验
    $service = new service();
    $params = $this->get(); // 获取参数
    $params['time'] = time() * 1000; // 适应前端 js 以 ms 为单位
    $params['branch'] = $params['branch'] ? $params['branch'] : '[]';
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
}

