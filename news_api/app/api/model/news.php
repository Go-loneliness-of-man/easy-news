<?php

// 命名空间
namespace app\api\model;
use \core\publicModel;
use \app\api\model\tag as tagModel;

class news extends publicModel {

  public function add($data, $content) {
    $time = $data['time'];
    $this->create($data); // 插入记录到数据库
    $id = $this->dao->one('SELECT * FROM '.$this->table.' WHERE title = \''.$data['title'].'\' AND time = '.$time)['news_id']; // 获取刚插入的记录 id
    $path = ROOT_PATH.'public/news/'.$id.'_'.$time.'.txt'; // 拼接 txt 文件路径，格式：id_时间戳
    $params['content'] = str_replace('<img src="http://newsapi.com/temp/', '<img src="http://newsapi.com/file/'.$id.'_', $content); // 替换图片文件路径，格式：$id_原文件名
    $f = fopen($path, 'wb+'); // 创建并打开文件
    fwrite($f, $content); // 将新闻内容写入文件
    fclose($f); // 关闭
    $this->update('file', $path, 'news_id = '.$id); // 将 txt 文件路径更新到记录
    $match = '<img src=".*file/(.+)" alt=.*/>'; // 接下来解析 content，将图片从暂存区 /public/temp/ 移动到 /public/file/ 下
    $res = [];
    preg_match_all('#'.$match.'#i', $params['content'], $res, PREG_PATTERN_ORDER); // 查出所有路径
    $res = $res[1];
    foreach($res as $v) { // 转移所有图片到 file 下
      $oldName = explode($id.'_', $v)[1]; // 计算实际名称
      copy(ROOT_PATH.'public/temp/'.$oldName, ROOT_PATH.'public/file/'.$v); // 拷贝到新目录
    }
  }

  // 给新闻记录添加标签
  public function getTags($res) {
    $len = count($res);
    if(!$len)  return $res; // 数组为空，直接返回
    $tagModel = new tagModel();
    $idList = ''; // 准备拼接所有 tag_id
    foreach($res as $k => $v) // 解析副标签数组
      $res[$k]['branch'] = json_decode($res[$k]['branch']);
    $branchFirst = $this->convert($res[0]['branch']);
    $branchFirst = $branchFirst === '' ? '' : ','.$branchFirst;
    $idList = $idList.$res[0]['master'].$branchFirst;
    for($i = 1; $i < $len; $i++) { // 拼接所有 tag_id
      $branch = $this->convert($res[$i]['branch']);
      $branch = $branch === '' ? '' : ','.$branch;
      $idList = $idList.','.$res[$i]['master'].$branch;
    }
    $temp = explode(',', $idList); // 切割
    $idList = []; // 准备去重
    foreach($temp as $v) // 以 tag_id 为 key 构建 tag_id 列表的 hashTable
      isset($idList[$v]) ? $idList[$v]++ : $idList[$v] = 1;
    $idList = array_keys($idList); // 直接获取 key 达到去重目的
    $tags = $tagModel->get($idList); // 根据 tag_id 获取这些 tag
    $temp = [];
    foreach($tags as $v) // 以 tag_id 为 key 构建 tags 的 hashTable
      $temp[$v['tag_id']] = $v;
    foreach($res as $k => $v) { // 给新闻记录添加 tags 字段
      $master = $temp[$res[$k]['master']]; // 主标签
      $master['master'] = 1;
      $res[$k]['tags'] = [ $master ];
      $i = 1;
      foreach($res[$k]['branch'] as $tagId) {
        $tag = $temp[$tagId]; // 获取一个副标签
        $tag['master'] = 0;
        $res[$k]['tags'][$i++] = $tag;
      }
    }
    return $res;
  }

  // 将 array 元素连接为字符串并以 , 分隔
  public function convert($arr) {
    if(!count($arr))  return '';
    $s = $arr[0];
    $len = count($arr);
    for($i = 1; $i < $len; $i++)
      $s = $s.','.$arr[$i];
    return $s;
  }

  public function del() {
        
  }

  public function revise() {

  }

  public function read() {

  }
}



