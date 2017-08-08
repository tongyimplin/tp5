<?php
/**
 * Created by PhpStorm.
 * User: jafar
 * Date: 2017/7/24
 * Time: 下午5:09
 */

function formatNumber($n, $length=2) {
    $nLength = 0;
    $nTemp = $n;
    do {
        $nTemp = floor($nTemp/10);
        $nLength ++;
    }while($nTemp>0);
    $gap = $length-$nLength;
    if($gap > 0) {
        return str_repeat('0', $gap).$n;
    }
    return $n;
}


function sumAdd($a, $b) {
    $a = intval($a*100);
    $b = intval($b*100);
    return ($a+$b)/100;
}

function sumSub($a, $b) {
    $a = intval($a*100);
    $b = intval($b*100);
    return ($a-$b)/100;
}

function getTempFile($tName) {
    return ROOT_PATH.'runtime/temp/backup/'.$tName.'_'.guid().'.tb';
//    return tempnam('/Users/jafar/temp', $tName);
}


function writeToFile($fileName, $content) {
    touch($fileName);
    $file = fopen($fileName, 'w+') or die('Unable to open file: '.$fileName);
    fwrite($file, $content);
    fclose($file);
//    file_put_contents($fileName, $content);
    dump('成功写入文件: '.$fileName);
}

function encodeMoney($money) {
    $result = file_get_contents("http://kmbk.xiaozhuzhu.top/yunpos/common/desplusEnc.form?type=2&content={$money}");
    $arr = json_decode($result);
    return $arr->main;
}