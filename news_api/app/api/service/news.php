<?php

// 命名空间
namespace app\api\service;
use \core\publicService;
use \app\api\model\news as model;

class news extends publicService {

  public function add($params) {
    $model = new model();
    $time = $params['time'];
    $data = [
      'theme_id' => $params['theme_id'], // 所属专题 id，可以为空
      'title' => $params['title'], // 标题
      'click' => 0, // 点击量
      'good' => 0, // 点赞数
      'file' => '', // txt 文件路径
      'master' => $params['master'], // 主标签 id
      'branch' => $params['branch'], // 副标签 id，json 数组
      'time' => $time, // 创建时间
      'revise' => $time, // 修改时间
    ];
    $model->add($data, $params['content']); // 具体添加文章的处理
    return $this->res(200, '成功', null);
  }

  // 删除新闻
  public function del($params) {
    $model = new model();
  }

  // 修改新闻
  public function revise($params) {
    $model = new model();
  }

  // 获取新闻[{ theme: '测试', title: '测试', click: 123, good: 23, tags: [{ master: 1, name: '测试asdasdad' }, { master: 0, name: '测试' }], time: (new Date()).getTime(), revise: (new Date()).getTime() }];
  public function read($params) {
    extract($params); // 批量生成参数
    $model = new model();
    $all = isset($all) ? $all : false; // 是否不分页
    $search = isset($search) ? $search : false; // 是否模糊查询
    $idList = isset($idList) ? json_decode($idList) : false; // 是否存在 id 列表
    $res = [];
    $sql = "SELECT t.title as theme, n.title as title, n.click, n.good, n.master, n.branch, n.time, n.revise, n.news_id, t.theme_id FROM news_t n LEFT JOIN theme_t t ON n.theme_id = t.theme_id"; // 准备拼接的 sql
    if($idList) { // 存在 id 列表
      $res = $model->get($idList);
      return $this->res(200, '获取新闻成功', $res, count($res));
    }
    if($search) { // 存在模糊查询字符串
      $temp = explode(' ', $search); // 切割关键字
      $len = count($temp); // 计算长度
      for($i = 1, $search = $temp[0]; $i < $len; $i++) // 拼接正则表达式
        $search = $search.'|'.$temp[$i];
      $sql = $sql." WHERE n.title REGEXP '$search'";
    }
    if(!$all) {// 是否分页
      $start = $size * ($number - 1);
      $sql = $sql." LIMIT $start,$size";
    }
    $res = $model->dao->query($sql); // 查数据库
    $res = $model->getTags($res); // 为新闻记录添加 tags 字段
    return $this->res(200, '获取新闻成功', $res, count($res));
  }

  // 处理临时文件上传
  public function uploadFile() {
    $i = 0;
    $res = [];
    $count = count($_FILES);
    if($count === 1) // 单文件
      return $this->res(200, 'success', $this->saveUploadFile($_FILES['file']));
    foreach ($_FILES as $v) { // 多文件
      $res[$i++] = $this->res(200, 'success', $this->saveUploadFile($v));
    }
    return $this->res(200, 'success', $res);
  }

  // 移动到 /pbulic/temp 下并返回路径
  public function saveUploadFile($file) {
    global $config; // 引入全局配置变量 config
    $path = 'temp/'.$file['name']; // 相对路径
    move_uploaded_file($file['tmp_name'], ROOT_PATH.'public/'.$path); // 转存文件
    return $config['URL']['local'].$path; // 返回图片 url
  }
}



