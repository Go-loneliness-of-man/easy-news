<?php

// 递归解析变量为字符串，参数 $s、$var、$h 依次是待输出字符串、待解析变量、当前递归深度
function recursiveOutputVariable($s, $var, $h) {
    $type = gettype($var);                                                  // 获取变量类型
    $space = '';                                                            // 生成空白符
    for($i = 0; $i < $h; $space = $space.'    ', $i++);
    if($type === 'object' || $type === 'array') {                           // 解析对象、数组
        $s = $h === 1 ? "$s$space"."$type(<br>" : $s."$type(<br><br>";      // 对象、数组开头换行
        $i = 0;                                                             // 存储循环次数
        foreach($var as $k => $v) {                                         // 遍历对象属性
            if(gettype($v) === 'object' || gettype($v) === 'array') {       // 判断属性值是否是对象、数组
                $s = "$s<br>    $space$k => ";                              // 这里注意，不能将该句与下句合并，因为下句中计算了 $s，在表达式内改变已引用变量会导致错误
                $s = recursiveOutputVariable($s, $v, $h + 1);               // 是对象、数组，进行递归
            }
            else                                                            // 简单类型
                $s = $i === 0 ? $s."    $space$k => ".gettype($v)." ($v)" : $s."<br>    $space$k => ".gettype($v)." ($v)";
            $i++;
        }
        $s = $s."<br>$space)<br>";
    }
    else                                                                    // 简单类型
        $s = "$s    $space$type ($var)";
    return $s;
}

// 格式化输出变量，便于调试查看，参数依次是变量、变量名、是否直接返回字符串而不输出
function dump($var, $name = '', $string = false, $isJson = 0) {
    if($isJson) return json_encode($var);
    $name = $name === '' ? '' : "    $$name :<br>";                         // 处理开头
    $s = "<pre>$name";                                                      // 准备开头
    $s = recursiveOutputVariable($s, $var, 1);                              // 递归解析变量为字符串
    if($string) return $s;
    echo $s.'</pre>';
}

// 参数 $start、$end、$separator、$format 依次是开始日期、结束日期、日期分隔符、返回日期的格式，后两个是可选的
function generateDate($start, $end) {
    $time = [];                                                             // 准备返回数据
    for($date = $start; $date !== $end; $date = addOneDate($date))          // 生成日期
      $time[count($time)] = $date;                                          // 生成一次
    $time[count($time)] = $end;                                             // 加上结尾日期
    return $time;                                                           // 返回结果
}

// 日期加 1，参数 $data、$separator、$format 依次是日期、日期分隔符、返回日期的格式，后两个是可选的
function addOneDate($date, $separator = '-', $format = 'y-m-d') {
    $mon_day = [31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 ];           // 各月份的天数
    $mark = ['y', 'm', 'd'];                                                // 年、月、日标记
    $date = explode($separator, $date);                                     // 切割日期
    for($i = 0; $i < 3; $i++)   $date[$i] = intval($date[$i]);              // 转换日期为数值
    if($date[0] % 4 === 0)  $mon_day[1] = 28;                               // 闰年情况
    if($date[1] === 12 && $date[2] === 31) {                                // 判断是否是本年最后一天
        $date[0]++;                                                         // 年份加 1
        $date[1] = 1;                                                       // 月、日置为 1
        $date[2] = 1;
    }
    else if($date[2] >= $mon_day[$date[1] - 1]){                            // 判断是否是月底
        $date[1]++;                                                         // 月份加 1
        $date[2] = 1;                                                       // 日置为 1
    }
    else                                                                    // 非年底、月底，仅日期加 1
        $date[2]++;
    $date[1] = $date[1] < 10 ? "0$date[1]" : "$date[1]";                    // 补全月、日十位的 0
    $date[2] = $date[2] < 10 ? "0$date[2]" : "$date[2]";
    for($i = 0; $i < 3; $i++)                                               // 以指定格式返回日期
        $format = str_replace($mark[$i], $date[$i], $format);
    return $format;
}

// 切换英文大小写
function toggleCase($s) {
  for($i = 0, $c = strlen($s); $i < $c; $i++)                               // 遍历字符串
    if((ord($s[$i]) > 64) && (ord($s[$i]) < 91))                            // 检测大写
      $s[$i] = chr(ord($s[$i]) + 32);                                       // 转换大写为小写
    else if((ord($s[$i]) > 96) && (ord($s[$i]) < 123))                      // 检测小写
      $s[$i] = chr(ord($s[$i]) - 32);                                       // 转换小写为大写
  return $s;
}

// 将小驼峰转换为小写下划线
function convertNaming($name) {
  $newName = '';                                                          // 准备新字符串
  for($i = 0, $j = 0, $c = strlen($name); $i < $c; $i++)                  // 遍历原字符串
    if((ord($name[$i]) > 64) && (ord($name[$i]) < 91))                    // 检测大写
      $newName = $newName.'_'.toggleCase($name[$i]);                      // 转换小写并加 _
    else
      $newName = $newName.$name[$i];                                      // 不是大写，直接拼接
  return $newName;
}

// 代替 basename
function rwBaseName($s) {
  $name = explode('\\', $s);
  return $name[count($name) - 1];
}

// 生成随机字符串
function randString(&$s, $len) {
  $c = 'abcdefghijklmnopqistuvwxyzABCDEFGHJKLMNOPQISTUVWXYZ23456789';       //字符表
  for($i = 0; $i < $len; $i++)   $s = $s.$c[mt_rand(0, 59)];                //拼接随机字符串
  return $s;
}


