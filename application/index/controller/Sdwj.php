<?php
/**
 * Created by PhpStorm.
 * User: jafar
 * Date: 2017/7/30
 * Time: 下午1:06
 */

namespace app\index\controller;


class Sdwj
{
    public function uploadBalanceflow() {
        $db = db_sqlite('sdzb');
//        $db1 = db_mysql();

        $flows = $db->name('balanceflow')->where('merchantId', '10000259')->select();
        foreach ($flows as $k => $flow) {
        }
    }
    public function compareVip() {
        $db01 = db_sqlite('sdwj01');
        $db02 = db_sqlite('sdwj02');
        $where['MerchantId'] = ['in', "10000792,10000813"];

        $vips1 = $db01->name('vip')->where($where)->select();
        $vips2 = $db02->name('vip')->where($where)->select();

        $flowIdArr = [];
        $noRepeatFlowId01 = [];
        $noRepeatFlowId02 = [];
        foreach ($vips1 as $k => $vip1) {
            foreach ($vips2 as $k1 => $vip2) {
                if($vip1['MerchantId'] == $vip2['MerchantId']
                    && $vip1['BranchId'] == $vip2['BranchId']
                    && $vip1['Id'] == $vip2['Id']
                    && $vip1['FlowId'] == $vip2['FlowId']) {
                    $flowIdArr[] = $vip1['FlowId'];
                }
            }
        }

        foreach ($vips2 as $k => $vip1) {
            $isNotFound = true;
            foreach ($flowIdArr as $k1 => $flowId) {
                if($vip1['FlowId'] == $flowId) {
                    $isNotFound = false;
                    break;
                }
            }
            if($isNotFound) {
                $noRepeatFlowId01[] = $vip1;
            }
        }

        foreach ($noRepeatFlowId01 as $k => $flow) {
            unset($flow['generatedId']);
            $db01->name('vip')->insert($flow);
        }

        $output[] = "db01中有".count($vips1)."个会员";
        $output[] = "db02中有".count($vips2)."个会员";
        $output[] = "重复的有".count($flowIdArr)."个会员";
        $output[] = "重复的id: ".join(",", $flowIdArr);
        $output[] = "db01中不重复的:".count($noRepeatFlowId01)."个";
        echo join('<br />', $output);
    }

    public function compareVipflows() {
        $db01 = db_sqlite('sdwj01');
        $db02 = db_sqlite('sdwj02');
        $where['MerchantId'] = ['in', "10000792,10000813"];

        $vips1 = $db01->name('balanceflow')->where($where)->select();
        $vips2 = $db02->name('balanceflow')->where($where)->select();

        $flowIdArr = [];
        $noRepeatFlowId01 = [];
        $noRepeatFlowId02 = [];
        foreach ($vips1 as $k => $vip1) {
            foreach ($vips2 as $k1 => $vip2) {
                if($vip1['MerchantId'] == $vip2['MerchantId']
                    && $vip1['BranchId'] == $vip2['BranchId']
                    && $vip1['FlowId'] == $vip2['FlowId']) {
                    $flowIdArr[] = $vip1['FlowId'];
                }
            }
        }

        foreach ($vips2 as $k => $vip1) {
            $isNotFound = true;
            foreach ($flowIdArr as $k1 => $flowId) {
                if($vip1['FlowId'] == $flowId) {
                    $isNotFound = false;
                    break;
                }
            }
            if($isNotFound) {
                $noRepeatFlowId01[] = $vip1;
            }
        }

        foreach ($noRepeatFlowId01 as $k => $flow) {
            unset($flow['generatedId']);
            $db01->name('balanceflow')->insert($flow);
        }

        $output[] = "db01中有".count($vips1)."条数据";
        $output[] = "db02中有".count($vips2)."条数据";
        $output[] = "重复的有".count($flowIdArr)."条数据";
        $output[] = "重复的id: ".join(",", $flowIdArr);
        $output[] = "db01中不重复的:".count($noRepeatFlowId01)."条";
        echo join('<br />', $output);


    }

    public function dealDate() {
        $db = db_sqlite('xxt');

        $vips = $db->name('vip')->select();
        $count = 0;
        foreach ($vips as $v => $vip) {
            $dateTime = $vip['LastConsumeDate'];
//            dump($dateTime);
            if(strlen($dateTime) == 13) {
                $dateStr = date('Y-m-d H:i:s', $dateTime/1000);
                $db->name('vip')->where([
                    'FlowId' => $vip['FlowId']
                ])->setField('LastConsumeDate', $dateStr);
                $count ++;
            }
        }
        echo "共计处理{$count}条数据";
    }
}