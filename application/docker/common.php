<?php
/**
 * Created by PhpStorm.
 * User: jafar
 * Date: 2017/8/7
 * Time: 下午5:01
 */


//

//获取某个项目某个环境下的push脚本
function get_latest_build_sh($projectName, $env='dev') {

    $buildScripts = '
docker build -t ${projectName} .
docker login --username=${userName} -p${userPass} ${repos}
docker tag ${projectName} ${reposUrl}
docker push ${reposUrl}
docker rmi ${reposUrl}
';
    //docker rmi ${projectName}
    return compile_build_script($buildScripts, $projectName, $env, '', true);
}

//获取某个项目下某个环境下的run脚本
function get_latest_run_sh($projectName, $env='env', $port=80) {

    $runScripts = '
docker login --username=${userName} -p${userPass} ${repos}
docker stop ${projectName}
docker rm ${projectName}
docker run -d --name ${projectName} -p ${port}:${port} ${reposUrl}
    ';

    return compile_build_script($runScripts, $projectName, $env, $port);
}

//编译脚本中的东西
function compile_build_script($str, $projectName, $env, $port='', $autoInc=false) {
    $aliyun = get_aliyun_hub_conf();
    $userName = $aliyun['username'];
    $userPass = $aliyun['userpass'];
    $projectVersion = get_latest_version($projectName, $env, $autoInc);
    $repos = $aliyun['repos.hn'];
    if(!$autoInc && ($env == 'kmbk' || $env == 'www')) {
        $repos = str_replace('registry', 'registry-internal', $repos);
    }
    $reposNs = $aliyun['ns'];
    $reposUrl = $repos.'/'.$reposNs.'/'.$projectName.':'.$projectVersion;
    return str_replace(
        ['${projectName}', '${userName}', '${userPass}', '${repos}', '${reposUrl}', '${port}'],
        [$projectName, $userName, $userPass, $repos, $reposUrl, $port],
        $str
    );
}

//获取阿里云的配置
function get_aliyun_hub_conf() {
    return config('aliyunhub');
}

//获取中餐扫码点餐的代称
function get_wxdczc_server_name() {
    return 'wxdczcserver';
}

//获取老板通的代称
function get_yunpos_boss_name() {
    return 'yunposboss';
}

//获取快餐扫码点餐的代称
function get_wxdc_server_name() {
    return 'wxdcserver';
}

//获取零售推送后台的代称
function get_push_items_name() {
    return 'pushitems';
}

//获取repostory和version

//获取某个项目某个环境下最新的版本号
function get_latest_version($project, $env='dev', $autoInc=true) {
    $db = db_mongo();
    $tableName = 'project';
    $where = [
        'name'  => $project,
        'env'   => $env
    ];
    $project = $db->name($tableName)->where($where)->find();
    $version = 100;
    if(!$project) {
        $where['version'] = $version;
        $db->name($tableName)->insert($where);
    }else {
        $version = $project['version'];
        if($autoInc) {
            $version++;
        }
        $db->name($tableName)->where($where)->update(['version' => $version]);
    }
    $db->close();
    $resultVersion = $version/100;
    return $resultVersion;
}