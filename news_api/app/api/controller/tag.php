<?php

// 命名空间
namespace app\api\controller;
use \core\publicController;
use \app\api\service\tag as service;           // service

class tag extends publicController {

  public function add() {
    return $this->oftenCode(new service(), ['name' => 'string', 'level' => 'integer'], 'add');
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

