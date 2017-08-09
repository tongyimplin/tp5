<?php
/**
 * Created by PhpStorm.
 * User: jafar
 * Date: 2017/8/7
 * Time: 下午3:36
 */

namespace app\docker\controller;


class Shellscript
{
    //中餐扫码点餐后台
    public function getWxdczcServerBuildScript($env='env') {
        $projectName = get_wxdczc_server_name();
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
    public function getWxdczcServerRunScript($env='dev') {
        $projectName = get_wxdczc_server_name();
        $buildScripts = get_latest_run_sh($projectName, $env, 7300);
        return $buildScripts;
    }
    //老板通后台
    public function getYunposBossBuildScript($env='dev') {
        $projectName = get_yunpos_boss_name();
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

    public function getYunposBossRunScript($env='dev') {
        $projectName = get_yunpos_boss_name();
        $buildScripts = get_latest_run_sh($projectName, $env, 8686);
        return $buildScripts;
    }
    //快餐扫码点餐后台
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

    //零售推送后台
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

    //获取构建脚本
    public function getPushItemsRunScript($env='dev') {
        $projectName = get_push_items_name();
        $buildScripts = get_latest_run_sh($projectName, $env, 8968);
        return $buildScripts;
    }
}