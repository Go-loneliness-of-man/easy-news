<?php

// 命名空间
namespace core;
use \PDO;
use \PDOStatement;
use \PDOException;

// 单例模式，静态方法为 sql 构造器，普通方法为原生查询
final class dao {

  private static $pdo;                                                                             // 保存 pdo 对象
  private static $dao = NULL;                                                                      // 保存 dao 对象
  protected static $prepareSql = [];                                                               // 保存预定义 sql
  public $type;                                                                                    // DBMS
  protected $host;                                                                                 // 主机 ip
  protected $port;                                                                                 // 数据库监听的端口号
  protected $user;                                                                                 // 账号名
  protected $pwd;                                                                                  // 账号密码
  protected $charset;                                                                              // 通信编码
  public $dbname;                                                                                  // 数据库名
  public $front;                                                                                   // 表前缀
  public $behind;                                                                                  // 表后缀

  // 私有 clone 方法，阻止 clone
  private function __clone() {}

  // 私有构造函数
  private function __construct($db = []) {
    global $config;                                                                                 // 引入全局配置变量

    // 配置数据库基本信息
    $this->type    =  empty($config['database']['type'])      ?    @$db['type']       :    $config['database']['type'];        // DBMS
    $this->host    =  empty($config['database']['host'])      ?    @$db['host']       :    $config['database']['host'];        // 主机名
    $this->port    =  empty($config['database']['port'])      ?    @$db['port']       :    $config['database']['port'];        // 端口号
    $this->user    =  empty($config['database']['user'])      ?    @$db['user']       :    $config['database']['user'];        // 数据库管理员帐号名
    $this->pwd     =  empty($config['database']['pwd'])       ?    @$db['pwd']        :    $config['database']['pwd'];         // 数据库管理员帐号密码
    $this->charset =  empty($config['database']['charset'])   ?    @$db['charset']    :    $config['database']['charset'];     // 编码类型
    $this->dbname  =  empty($config['database']['dbname'])    ?    @$db['dbname']     :    $config['database']['dbname'];      // 数据库名
    $this->front   =  empty($config['database']['front'])     ?    @$db['front']      :    $config['database']['front'];       // 表前缀
    $this->behind  =  empty($config['database']['behind'])    ?    @$db['behind']     :    $config['database']['behind'];      // 表后缀

    // 创建 PDO 对象
    try {
      $s = $this->type.':host='.$this->host;                                                        // 用于连接数据库
      $s = empty($this->port) ? $s : $s.';port='.$this->port;                                       // 端口
      $s = empty($this->dbname) ? $s : $s.';dbname='.$this->dbname;                                 // 数据库
      self::$pdo = new PDO($s, $this->user, $this->pwd);                                            // 建立连接
      if(empty($charset))   self::$pdo->exec('SET NAMES '.$this->charset);                          // 设置字符集
      self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);                          // 配置异常处理
    }
    catch(PDOException $err) {
      echo '数据库连接失败<br>错误文件为：'.@$err->getFile().'<br>错误行为：'.@$err->getLine.'<br>错误描述：'.@$err->getMessage();
      exit;
    }
  }

  //获取对象引用
  public static function get($db = []) {
    if(!((self::$dao instanceof self) && $db == []))                                                // 若尚未实例化，则 new
      self::$dao = new dao($db);
    return self::$dao;                                                                              // 返回引用
  }

  // 去掉返回记录的数字下标
  public static function unset($data) {
    for($i = 0, $c = count($data); $i < $c; $i++)
      for($j = 0, $c2 = count($data[$i]) / 2; $j < $c2; $j++)
        unset($data[$i][$j]);
    return $data;
  }

  // 执行 sql 或查询多条记录
  public function query($sql, $unset = 1) {
    $res = self::$pdo->prepare($sql);                                                               // 准备结果集
    $res->execute();                                                                                // 执行
    return $unset ? self::unset($res->fetchAll()) : $res->fetchAll();                               // 返回所有记录
  }

  // 执行 sql 或查询单条记录
  public function one($sql, $unset = 1) {
    return ($this->query($sql, $unset))[0];                                                         // 查询并只返回第一条记录
  }

  // 执行 sql 并返回受影响记录数
  public function exec($sql) {
    return self::$pdo->exec($sql);
  }

  /*  *************************************************************************** sql 构造器 *************************************************************************************
  **  支持大多数 sql 关键字，采用 php 关联数组传参，基于 sql 关键字设计参数，可选择直接返回 sql 语句（用于实现子查询、调试）。目前支持 6 个方法：
  **  select()、select_one()、define()、insert()、update()、delete()，作用依次是查询、查询一条、预定义查询（是在函数内配置好的数组，使用时可直接调用）、
  **  增、改、删

  **  作者 bilibili id: --刃舞--

  **  select(): 用于构造 SELECT 语句，参数如下。
  **  field:      string                                                                            必须，要获取的字段
  **  from:       string 或 [[ string, string, string, string ], [ string, string, string ]]        必须，数据源，当传入数组时会自动进行关联查询，关联查询时，第一个元素为表 1、表2、条件、关联类型（可选，默认 left join），之后为表名、条件、关联类型（可选）
  **  where:      string 或 [[ string, string, string ], [ string, string, string]]                 可选，where 子句，每个子数组依次是左值、关系运算符和右值、逻辑运算符（可选，默认 AND）
  **  group:      string                                                                            可选，分组字段名
  **  distinct:   bool                                                                              可选，是否去重
  **  order:      string                                                                            可选，用于排序的字段名
  **  limit:      [ number, number ]                                                                可选，获取的记录区间，可以只传第一个
  **  onlySql:    bool                                                                              可选，是否直接返回 sql 字符串

  **  select_one():  专用于查询单条记录，参数与 select() 相同。

  **  define(): 用于定义、执行预定义的查询。
  **  key:        string                                                                             必须，预定义 sql 在 prepareSql 中的 key
  **  params:     []                                                                                 可选，其属性是预定义 sql 需要的参数，其内多了个元素 one，用于判断调用 select_one() 还是 select()
  **  cover:      []                                                                                 可选，用于覆盖预定义 sql 的属性，使其更加灵活

  **  insert(): 用于构造 INSERT 语句，参数如下：
  **  table:      string                                                                             必须，要插入的表名
  **  data:       [ key => val ]                                                                     必须，要插入的 key/val 对
  **  onlySql:    bool                                                                               可选，是否直接返回 sql 字符串

  **  update(): 用于构造 UPDATE 语句，参数如下：
  **  table:      string                                                                             必须，要修改的表名
  **  key:        string                                                                             必须，要修改的字段名
  **  value:      string 或 number                                                                   必须，要修改的值
  **  where:      string 或 [[ string, string, string ], [ string, string, string]]                  可选，where 子句，与 select 相同
  **  onlySql:    bool                                                                               可选，是否直接返回 sql 字符串

  **  delete(): 用于构造 DELETE 语句，参数如下：
  **  table:      string                                                                             必须，要删除的表名
  **  where:      string 或 [[ string, string, string ], [ string, string, string]]                  可选，where 子句，与 select 相同
  **  onlySql:    bool                                                                               可选，是否直接返回 sql 字符串
  */

  // sql 构造器，构造 SELECT
  public static function select($params) {
    $dao = self::get();                                // 获取 dao 对象
    $sql = '';                                         // 存储 sql
    extract($params);                                  // 批量生成参数

    // 拼接数据源 from
    if(gettype($from) === 'array') {                   // 判断是否进行表关联
      $temp = count($from) > 1                         // 若大于 2 张表，加括号
      ? '( '.$from[0][0].' '.(isset($from[0][3]) ? $from[0][3] : 'LEFT JOIN').' '.$from[0][1].' ON '.$from[0][2].' )'    // 大于 2 张表
      : $from[0][0].' '.(isset($from[0][3]) ? $from[0][3] : 'LEFT JOIN').' '.$from[0][1].' ON '.$from[0][2];             // 关联两张表
      for($i = 1, $c = count($from); $i < $c; $i++) {                                                                    // 循环关联
        $temp = $i === $c - 1                                                                                            // 判断是否是最后一张表，若是则不加括号
        ? $temp.'
  '.(isset($from[$i][2]) ? $from[$i][2] : 'LEFT JOIN').' '.$from[$i][0].' ON '.$from[$i][1]
        : '(
  '.$temp.'
  '.(isset($from[$i][2]) ? $from[$i][2] : 'LEFT JOIN').' '.$from[$i][0].' ON '.$from[$i][1].'
  )';
      }
      $from = $temp;
    }

    // 拼接 where
    if(isset($where) && gettype($where) === 'array') { // 判断是否需要进行多条件拼接
      $temp = 'WHERE
      ';
      for($i = 0, $c = count($where); $i < $c; $i++) { // 循环拼接条件
        $temp = $temp.$where[$i][0].$where[$i][1];     // 第一个条件
    if($i < $c- 1) {                                   // 若不是最后一个条件，加逻辑运算符
  $temp = isset($where[$i][2])
  ? $temp.'
      '.$where[$i][2].' '
  : $temp.'
      AND ';                                           // 默认 AND
        }
      }
      $where = $temp;
    }
    else if(isset($where) && gettype($where) === 'string' ) {
      $where = 'WHERE
        '.$where;
    }
    else
      $where = '';

    isset($group)                                      // 分组
    ? $group = 'GROUP BY
  '.$group
    : '';
    isset($order)                                      // 排序
    ? $order = 'ORDER BY
  '.$order
    : '';
    isset($distinct) ? $distinct = 'DISTINCT' : '';    // 是否去重

    // 记录区间
    isset($limit) ? $limit = count($limit) < 2 ? 'LIMIT '.$limit[0] : 'LIMIT '.$limit[0].', '.$limit[1] : '';

    // 拼接 sql
    $sql = '
    SELECT
     '.(isset($distinct) ? $distinct : '').' '.$field.'
    FROM
      '.$from.'
    '.$where.'
    '.(isset($group) ? $group : '').'
    '.(isset($order) ? $order : '').'
    '.(isset($limit) ? $limit : '');

    // 返回结果
    if(isset($onlySql)) return $sql;
    else  return $dao->query($sql);
  }

  // 查询一条
  public static function select_one($params) {
    extract($params);                                  // 批量生成参数
    return isset($onlySql) && $onlySql ? self::select($params) : (self::select($params))[0];
  }

  // 执行预定义 sql
  public static function define($key, $params = [], $cover = []) {
    extract($params);                                  // 批量生成参数

    // 从 dao 配置文件中读取预定义 sql，优先读取模块配置文件
    file_exists(APP_PATH.M.'/dao.php')                 // 判断是否存在模块 dao 配置文件
    ? include_once(APP_PATH.M.'/dao.php')              // 加载模块 dao 配置文件
    : include_once(CONFIG_PATH.'dao.php');             // 加载全局 dao 配置文件
    self::$prepareSql = $prepareSql;                   // 保存预定义 sql 到类静态属性
    foreach($cover as $k => $v)                        // 用 $cover 覆盖预定义 sql 的部分参数
      $prepareSql[$key][$k] = $v;

    // 返回结果
    return (isset($one) ? $one : 0)
    ? self::select_one($prepareSql[$key])
    : self::select($prepareSql[$key]);
  }

  // 生成 insert
  public static function insert($params) {
    $dao = self::get();                                // 获取 dao 对象
    extract($params);                                  // 批量生成参数
    $sql = '
    INSERT INTO
      '.$table;
    $key = '(';
    $values = '
    (';
    $i = 0;
    foreach($data as $k => $v) {                       // 拼接字段、值列表
      $key = $i === 0 ? $key.'
      '.$k : $key.',
      '.$k;
      $values = $i === 0 ? (gettype($v) === 'string' ? $values."
      '$v'" : $values.'
      '.$v) : (gettype($v) === 'string' ? $values.",
      '$v'" : $values.',
      '.$v);
      $i++;
    }

    // 拼接 sql
    $sql = $sql.'
    '.$key.'
    )
    VALUES'.$values.'
    )
  ';

    // 返回结果
    if(isset($onlySql)) return $sql;
    else  return $dao->exec($sql);
  }

  // 生成 update
  public static function update($params) {
    $dao = self::get();                                // 获取 dao 对象
    extract($params);                                  // 批量生成参数
    $sql = '
    UPDATE
      '.$table.'
    SET
      '.$key.' = '.(gettype($value) === 'string' ? "'$value'" : $value);

    // 拼接 where
    if(isset($where) && gettype($where) === 'array') { // 判断是否需要进行多条件拼接
      $temp = '
    WHERE
      ';
      for($i = 0, $c = count($where); $i < $c; $i++) { // 循环拼接条件
        $temp = $temp.$where[$i][0].$where[$i][1];     // 第一个条件
    if($i < $c- 1) {                                   // 若不是最后一个条件，加逻辑运算符
  $temp = isset($where[$i][2])
  ? $temp.'
      '.$where[$i][2].' '
  : $temp.'
      AND ';                                           // 默认 AND
        }
      }
      $where = $temp;
    }
    else if(isset($where) && gettype($where) === 'string' ){
      $where = '
    WHERE
      '.$where;
    }
    else
      $where = '';

    $sql = isset($where) ? $sql.$where.'
  ' : $sql.'
  ';
    // 返回结果
    if(isset($onlySql)) return $sql;
    else  return $dao->exec($sql);
  }

  // 生成 delete
  public static function delete($params) {
    $dao = self::get();                                // 获取 dao 对象
    extract($params);                                  // 批量生成参数
    $sql = '
    DELETE FROM
      '.$table;

    // 拼接 where
    if(isset($where) && gettype($where) === 'array') { // 判断是否需要进行多条件拼接
      $temp = '
    WHERE
      ';
      for($i = 0, $c = count($where); $i < $c; $i++) { // 循环拼接条件
        $temp = $temp.$where[$i][0].$where[$i][1];     // 第一个条件
    if($i < $c- 1) {                                   // 若不是最后一个条件，加逻辑运算符
  $temp = isset($where[$i][2])
  ? $temp.'
      '.$where[$i][2].' '
  : $temp.'
      AND ';                                           // 默认 AND
        }
      }
      $where = $temp;
    }
    else if(isset($where) && gettype($where) === 'string' ){
      $where = '
    WHERE
      '.$where;
    }
    else
      $where = '';

    $sql = isset($where) ? $sql.$where.'
  ' : $sql.'
  ';
    // 返回结果
    if(isset($onlySql)) return $sql;
    else  return $dao->exec($sql);
  }
}




