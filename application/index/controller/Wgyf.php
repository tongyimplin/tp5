<?php
/**
 * Created by PhpStorm.
 * User: jafar
 * Date: 2017/7/29
 * Time: 上午10:19
 */

namespace app\index\controller;


class Wgyf
{
    public function showTables() {
        $db = db_sqlite();
        $item = $db->name('item')->find();
        echo 'hello';
        dump($item);
    }

    public function upload() {
        $sqls[] = $this->uploadSQL('vip', 'sdzb');
        $sqls[] = $this->uploadSQL('balanceflow', 'sdzb');
        echo join('<br/>', $sqls);

//        $this->uploadTable('balanceflow', 'sdzb');
//        $this->uploadTable('merchant', 'sdzb');
//        $this->uploadTable('branch', 'sdzb');
//        $this->uploadTable('operator', 'sdzb');
    }

    public function uploadSQL($tName, $localDb) {
        $kmbk = db_kmbk();
        $sqlite = db_sqlite($localDb);
        $tableName = $tName;
        $fieldsArr = $kmbk->query('desc '.$tableName);
        $vips = $sqlite->name($tableName)->select();

        $tmpArr = [];
        foreach ($fieldsArr as $f => $field) {
            $tmpArr[] = $field['Field'];
        }

        $rsArr = [];
        $termined = '';
        foreach ($vips as $v => $vip) {
            $lineArr = [];
            foreach ($fieldsArr as $f => $field) {
                $fieldType = $field['Type'];
                $fieldName = $field['Field'];
                $val = $vip[$fieldName] ?? '';
                if($fieldType == 'datetime') {
//                    dump($val);
//                    $val = date('Y-m-d H:i:s', intval($val)/1000);
                }else if($fieldType == 'date'){
//                    $val = date('Y-m-d', intval($val)/1000);
                }
                $lineArr[$fieldName] = "\"{$val}\"";
            }
            $rsArr[] = join(',', $lineArr);
        }
        dump($lineArr);
        $rsStr = join('######', $rsArr);

        $fileDirName = getTempFile($tableName);
        writeToFile($fileDirName, $rsStr);
        $fileName = basename($fileDirName);

        $fields = join(',', $tmpArr);

        $sql = "load data local infile '/Users/jafar/phproot/tp5/runtime/temp/backup/{$fileName}' replace into table {$tableName}
		fields terminated by ','
		optionally enclosed by '\"'
		lines terminated by '######' 
		(
			{$fields}
		);";
        return $sql;
    }

    private function uploadTable($tName, $localDb) {
        $kmbk = db_kmbk();
        $sqlite = db_sqlite($localDb);
        dump("更新表[{$tName}]");
        $tableName = $tName;
        $fieldsArr = $kmbk->query('desc '.$tableName);
        $vips = $sqlite->name($tableName)->select();
        $merchants = $sqlite->query('select merchantId, id from branch group by merchantId');
        $rsArr = [];
        foreach ($vips as $key => $vo) {
            $lineArr = [];
            foreach ($fieldsArr as $k => $field) {
                $fieldName = $field['Field'];
                $fieldType = $field['Type'];
                $val = $vo[$fieldName] ?? null;
                if($val == null) continue;
                if($fieldType == 'datetime') {
                    $val = date('Y-m-d H:i:s', intval($val));
                }else if($fieldType == 'date'){
                    $val = date('Y-m-d', intval($val)/1000);
                }
                $lineArr[ucfirst($fieldName)] = $val;
            }
            $where = [
                'MerchantId' => $lineArr['MerchantId'],
                'BranchId' => $lineArr['BranchId']
            ];
            unset($lineArr['MerchantId']);
            unset($lineArr['BranchId']);
            if($tableName == 'vip') {
                $where['Id'] = $lineArr['Id'];
                $where['FlowId'] = $lineArr['FlowId'];
                unset($lineArr['Id']);
                unset($lineArr['FlowId']);
            }else if($tableName == 'balanceflow') {
                $where['FlowId'] = $lineArr['FlowId'];
                unset($lineArr['FlowId']);
            }
            $rs = $kmbk->name($tableName)->where($where)->find();
            dump($rs);
            dump($lineArr);
            dump($vips);

//            if($rs) {            dump($lineArr);
////                $kmbk->name($tableName)->where($where)->update($lineArr);
//            }else {
////                $kmbk->name($tableName)->insert($lineArr);
//            }
            $rsArr[] = $lineArr;
        }
//        foreach ($merchants as $m => $merchant) {
//            if($tableName == 'merchant') {
//                $where = [
//                    'Id'  => $merchant['MerchantId']
//                ];
//            }else if($tableName == 'branch') {
//                $where = [
//                    'MerchantId' => $merchant['MerchantId'],
//                    'Id'  => $merchant['Id']
//                ];
//            }else {
//                $where = [
//                    'MerchantId' => $merchant['MerchantId'],
//                    'BranchId'  => $merchant['Id']
//                ];
//            }
//            $count = $kmbk->name($tableName)->where($where)->delete();
//
//            dump($where);
//            dump("删除了{$tableName}表中{$count}条数据");
//
//        }


    }

    public function getVips($merchantId='10000259', $page=0, $confirmUpdate=false) {
//        $db = db_sqlite('xxt');
        $db = db_mysql();
        /*$vipidsArr = $db->query('select vipid as c from balanceflow group by vipid;');
        $vipids = [];
        foreach ($vipidsArr as $k => $val) {
            $c = $val['c'];
            $vipids[] = $c;
        }dump($vipids);*/
        $vipid = input('get.vipid');
        $mobile = input('get.mobile');
        //查找所有的会员；
        $where = [
            'merchantId' => ['in', $merchantId],
            'deleteFlag' => 0,
//            'lastConsumeDate' => ['>', '2017-07-26']
//            'flowId' => '2ef5bf72024a4cf1a12d8951c10e5029'
        ];
        if(!empty($vipid)) {
            $where['id'] = $vipid;
        }
        if(!empty($mobile)) {
            $where['mobile'] = $mobile;
        }
        $vips = $db->name('vip')->where($where)->order('lastConsumeDate desc')
            ->limit($page*30, 30)
            ->select();
        $totalFlowsUpdates = 0;
        $totalVipUpdates = 0;
        $sqlArr = [];
//        dump($vips[0]);
        //查找会员各自的消费记录，并显示出来按照时间排序
        foreach ($vips as $key => $vip) {

            $lastConsumFlow = $db->name('balanceflow')->where([
                'vipid' => $vip['Id'],
                'operDate' => ['<', '2013-07-27']
            ])->whereOr([
                'vipid' => $vip['FlowId']
            ])->order('OperDate desc')->find();
            $flows = $db->name('balanceflow')->where([
                'vipid' => $vip['Id'],
//                'operDate' => ['>', '2017-07-27']
            ])->whereOr([
                'vipid' => $vip['FlowId']
            ])->order('OperDate asc')->select();
            if(!$lastConsumFlow) {
                $lastConsumFlow = $flows[0];
            }
            $lastSettleAmt = $lastConsumFlow['SettleAmt'];
            //赠送金额
            $totalAmt = 0;
            //充值金额
            $totalRecharge = 0;
            //余额
            $balance = 0;
//            array_unshift($flows, $lastConsumFlow);
            foreach ($flows as $f => $flow) {
                $operType = $flow['OperType'];
                $setAmt = $flow['SettleAmt'];
                $amt = $flow['Amt'];
                $beforeAmt = $flow['BeforeAmt'];
                $flow['BeforeAmt1'] = $beforeAmt;
                $flow['SettleAmt1'] = $setAmt;
                if($f == 0) {
                    $lastSettleAmt = $lastConsumFlow['BeforeAmt'];
                    $balance = $lastSettleAmt;
                }
                $beforeAmt1 = $lastSettleAmt;

//                $rechargeAmt = sumSub($setAmt, $beforeAmt1);
                $rechargeAmt = sumSub($setAmt, $beforeAmt);
//                $lastFlow = $flows[$f-1]??$flows[0];
                if($operType == 'A') {
                    $getAmt = $rechargeAmt - $amt; //赠送金额
                    $totalRecharge = sumAdd($totalRecharge, $amt);
                    $totalAmt = sumAdd($totalAmt, $getAmt);
                    $balance = sumAdd($balance, $rechargeAmt);
                    $balance = sumAdd($balance, $getAmt);
                    $setAmt = sumAdd($beforeAmt1, $rechargeAmt);
                }else if($operType == 'C'
                    || $operType == 'D'
                    || $operType == 'E') {
                    $setAmt = sumAdd($beforeAmt1, $amt);
                    $balance = sumAdd($balance, $amt);
                }else {
                    $setAmt = sumSub($beforeAmt1, $amt);
                    $balance = sumSub($balance, $amt);
                }
//                dump($balance.','.$rechargeAmt);
                if($beforeAmt != $lastSettleAmt) {
                    $flow['SettleAmt'] = $setAmt;
                    $flow['BeforeAmt'] = $beforeAmt1;
                    $flow['color'] = 'bg-danger';
                    //确认修改
                    $flow['SQL'] = "UPDATE balanceflow set SettleAmt={$setAmt}, BeforeAmt={$beforeAmt} "
                        ."WHERE merchantId={$flow['MerchantId']} and branchId='{$flow['BranchId']}' and flowid='{$flow['FlowId']}';";
//                    $sqlArr[] = $flow['SQL'];
                }else {
                    $flow['color'] = '';
                }
                $lastSettleAmt = $flow['SettleAmt'];
                $flows[$f] = $flow;
            }
            if($balance < 0) {
                $balance = 0;
                $totalAmt = sumAdd($totalAmt, $balance);
            }
            if($totalAmt < 0) {
                $totalAmt = 0;
            }
            $balance = sumSub($balance, $totalAmt);
            $vip['Balance1'] = $balance;
            $vip['GetAmt1'] = $totalAmt;
            $vip['flows'] = $flows;
            $encryptBalance = encodeMoney($balance);
            $vip['SQL'] = "UPDATE vip set balance={$balance}, getAmt={$totalAmt}, encryptBalance='{$encryptBalance}' "
                ."WHERE merchantId={$vip['MerchantId']} and branchId='{$vip['BranchId']}' and flowid='{$vip['FlowId']}';";
            $sqlArr[] = $vip['SQL'];
            //确认修改
            $vips[$key] = $vip;
        }
        //
//        dump($vips[0]['flows'][0]);

        return view('getVips', [
            'vips' => $vips,
            'vipCls' => [
                '普卡', '银卡', '金卡', '为定义'
            ],
            'vipStatus' => ['锁定', '使用中', '未使用'],
            'consumType' => [
                'A' => ['充值', 'success'],
                'B' => ['消费', 'info'],
                'C' => ['退货', 'warning'],
                'D' => ['赠送', 'danger'],
                'E' => ['修改', 'default'],
            ],
            'sqlArr' => join('<br />', $sqlArr)
        ]);
    }

}