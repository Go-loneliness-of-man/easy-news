<?php

namespace app\middleware;

class test {

    public static function testFunc($request = []) {
        header('Access-Control-Allow-Origin: *'); // 解决跨域
        header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
        header('Access-Control-Allow-Methods: GET, POST, PUT,DELETE');
    }
}
