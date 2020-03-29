<?php

// 生成验证码图片，返回图片资源，参数依次是画布宽高、字符串长度、字体文件路径、图片格式
function  testCode($w, $h, $len, $path, $format = 'png') {
    header('content-type:image/'.$format);        //设置响应头
    $s = randString($s, $len);                    // 获取随机字符串
    $bg = imagecreatetruecolor($w,$h);            // 创建画布
    imagefill($bg,10,10,imagecolorallocate($bg,mt_rand(200,255),mt_rand(200,255),mt_rand(200,255))); //填充背景色
    for($i = 0, $w2 = $w/7; $i < $len; $i++, $w2 += mt_rand($w/$len * 0.8,$w / $len + 5))            //绘制字符，w2 用于确定每个字符的 x 坐标
        imagettftext($bg, mt_rand($h/3,$h/2), mt_rand(0,180), $w2, $h/2, imagecolorallocate($bg, mt_rand(50,200), mt_rand(50,200), mt_rand(30,200)), $path,$s[$i]);
    for($i = 0, $c = mt_rand(4, 7); $i < $c; $i++)// 随机数量干扰线
        imageline($bg, mt_rand($w/30, $w/20), mt_rand(0, $h), mt_rand($w*0.8, $w), mt_rand(0, $h), imagecolorallocate($bg,mt_rand(50,200),mt_rand(50,200),mt_rand(30,200)));
    return $bg;
}

// 加盖水印，返回图片资源，参数依次是图片路径、水印路径、截取水印宽度、截取水印高度、加盖位置（1、2、3、4、5 依次代表左上、右上、左下、右下、正中）、图片格式
function waterMark($bg, $logo, $w, $h, $pos, $format = 'png') {
    header('content-type:image/'.$format);        //设置响应头
    $x = getimagesize($bg)[0];                    // 获取图片宽、高
    $y = getimagesize($bg)[1];
    $bg = imagecreatefrompng($bg);                // 读取图片
    $logo = imagecreatefromjpeg($logo);           // 读取水印
    switch($pos) {                                // 确定水印位置
        case 1:
            $x = $y = 0;
            break;
        case 2:
            $x = $x - $w;
            $y = 0;
            break;
        case 3:
            $x = 0;
            $y = $y - $h;
            break;
        case 4:
            $x = $x - $w;
            $y = $y - $h;
            break;
        case 5:
            $x = ($x - $w) / 2;
            $y = ($y - $h) / 2;
            break;
    }
    imagecopymerge($bg,$logo,$x,$y,0,0,$w,$h,60); // 合成水印到图片上
    return $bg;
}

// 缩略图，返回图片资源，参数依次是原图路径、缩略图宽高、缩略图背景色（十进制 rgb 表示）、图片格式
function thumbnail($big, $bgw, $bgh, $color, $format = 'png') {
    header('content-type:image/'.$format);        //设置响应头
    $w = getimagesize($big)[0];                   // 获取原图宽高
    $h = getimagesize($big)[1];
    $bl = $w / $bgw > $h / $bgh ? $w / $bgw : $h / $bgh;              // 计算缩小倍数
    $bg = imagecreatetruecolor($bgw, $bgh);       // 创建缩略图画布
    $big = imagecreatefrompng($big);              // 读取原图
    $color = imagecolorallocate($bg,$color[0],$color[1],$color[2]);   // 创建颜色
    imagefill($bg,10,10,$color);                  // 填充背景色
    imagecopyresampled($bg, $big, ($bgw - $w / $bl) / 2, ($bgh - $h / $bl) / 2, 0, 0, $w/$bl, $h/$bl, $w, $h);
    return $bg;
}






