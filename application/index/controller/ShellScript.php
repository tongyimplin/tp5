<?php
/**
 * Created by PhpStorm.
 * User: jafar
 * Date: 2017/8/7
 * Time: 下午3:36
 */

namespace app\index\controller;


class ShellScript
{
    public function getWxdcServerBuildScript($env='dev') {

        $buildScripts = '
sed -i "s/const config = ALL_CONFIG\.dev/const config = ALL_CONFIG\.${env}/g" ./config.js
pwd
cat ./config.js | grep const
        ';
        $buildScripts = str_replace('${env}', $env, $buildScripts);
        return $buildScripts;
    }
}