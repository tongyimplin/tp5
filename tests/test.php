<?php
/**
 * Created by PhpStorm.
 * User: jafar
 * Date: 2017/7/29
 * Time: 下午12:25
 */

function sumAdd($a, $b) {
    $a = intval($a*100);
    $b = intval($b*100);
    return ($a+$b)/100;
}

//echo sumAdd(10.1, 3.2);/

//echo strcasecmp('2017-07-28 33' , '2017-07-28 33');

//$result = file_get_contents("http://kmbk.xiaozhuzhu.top/yunpos/common/desplusEnc.form?type=1&content=ef85322271482fa7e0ed7d593eb865cd");
//$json = json_decode($result);
//var_dump($json->main);


echo date('Y-m-d H:i:s', 1496890443000/1000);