<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

use \think\db;

function db_mongo() {
    return Db::connect('db_mongo');
}

function db_mysql() { return Db::connect('db_mysql'); }
function db_kmbk() { return Db::connect('db_kmbk'); }

function db_sqlite($dbName = 'wgyf') {
    $config = config('db_sqlite');
    $config['dsn'] = 'sqlite:'.ROOT_PATH.'application/db/'.$dbName.'.db';
    return Db::connect($config);
}

function guid(){
    if (function_exists('com_create_guid')){
        return com_create_guid();
    }else{
        mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
        $charid = strtoupper(md5(uniqid(rand(), true)));
        $hyphen = chr(45);// "-"
        $uuid = // "{"chr(123)
            substr($charid, 0, 8).$hyphen
            .substr($charid, 8, 4).$hyphen
            .substr($charid,12, 4).$hyphen
            .substr($charid,16, 4).$hyphen
            .substr($charid,20,12);
//            .chr(125);// "}"
        return $uuid;
    }
}