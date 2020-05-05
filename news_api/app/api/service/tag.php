<?php

// 命名空间
namespace app\api\service;
use \core\publicService;
use \app\api\model\tag as model;

class tag extends publicService {

  public function add($params) {
    return $this->oftenC(new model(), '成功', $fault = '添加失败', $params);
  }

  public function del($params) {
    $model = new model();
  }

  public function revise($params) {
    $model = new model();
  }

  public function read($params) {
    extract($params); // 批量生成参数
    $model = new model();
    $all = isset($all) ? $all : false; // 是否不分页
    $search = isset($search) ? $search : false; // 是否模糊查询
    $idList = isset($idList) ? json_decode($idList) : false; // 是否存在 id 列表
    $res = [];
    $sql = "SELECT * FROM $model->dbname.$model->table"; // 准备拼接的 sql
    if($idList) { // 存在 id 列表
      $res = $model->get($idList);
      return $this->res(200, '获取标签成功', $res, count($res));
    }
    if($search) { // 存在模糊查询字符串
      $temp = explode(' ', $search); // 切割关键字
      $len = count($temp); // 计算长度
      for($i = 1, $search = $temp[0]; $i < $len; $i++) // 拼接正则表达式
        $search = $search.'|'.$temp[$i];
      $sql = $sql." WHERE name REGEXP '$search'";
    }
    if(!$all) {// 是否分页
      $start = $size * ($number - 1);
      $sql = $sql." LIMIT $start,$size";
    }
    $res = $model->dao->query($sql);
    return $this->res(200, '获取标签成功', $res, count($res));
  }
}



