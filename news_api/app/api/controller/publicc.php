<?php

// 命名空间
namespace app\api\controller;
use \core\publicController;
use \app\api\service\publicc as service;           // service

class publicc extends publicController {

  public function good() {
    return $this->oftenCode(new service(), [], 'good');
  }

  public function discuss() {
    return $this->oftenCode(new service(), [], 'discuss');
  }
}

