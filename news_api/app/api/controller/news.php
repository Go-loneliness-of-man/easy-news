<?php

// 命名空间
namespace app\api\controller;
use \core\publicController;
use \app\api\service\news as service;           // service

class news extends publicController {

  public function add() {
    return $this->oftenCode(new service(), [], 'add');
  }

  public function del() {
    return $this->oftenCode(new service(), [], 'del');
  }

  public function revise() {
    return $this->oftenCode(new service(), [], 'revise');
  }

  public function read() {
    return $this->oftenCode(new service(), [], 'get');
  }
}

