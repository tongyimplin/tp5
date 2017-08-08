<?php
/**
 * Created by PhpStorm.
 * User: jafar
 * Date: 2017/8/7
 * Time: 下午3:36
 */

namespace app\docker\controller;


class ShellScript
{
    public function getWxdcServerBuildScript($env='dev') {
        $projectName = get_wxdc_server_name();
        //项目版本
        $buildScripts = '
sed -i "s/const config = ALL_CONFIG\.dev/const config = ALL_CONFIG\.${env}/g" ./config.js
pwd
cat ./config.js | grep const
'.get_latest_build_sh($projectName, $env);
        $buildScripts = str_replace(
            ['${env}'],
            [$env],
            $buildScripts);
        return $buildScripts;
    }

    public function getWxdcServerRunScript($env='dev') {
        $projectName = get_wxdc_server_name();
        $buildScripts = get_latest_run_sh($projectName, $env, 8970);
        return $buildScripts;
    }

    public function getPushItemsBuildScript($env='dev') {
        $projectName = get_push_items_name();
        //项目版本
        $buildScripts = '
sed -i "s/const config = ALL_CONFIG\.dev/const config = ALL_CONFIG\.${env}/g" ./config.js
pwd
cat ./config.js | grep const
'.get_latest_build_sh($projectName, $env);
        $buildScripts = str_replace(
            ['${env}'],
            [$env],
            $buildScripts);
        return $buildScripts;
    }

    public function getPushItemsRunScript($env='dev') {
        $projectName = get_push_items_name();
        $buildScripts = get_latest_run_sh($projectName, $env, 8968);
        return $buildScripts;
    }
}