<?php
namespace app\index\controller;

use \think\db;

class Index
{
    public function index()
    {
        return 'index';
    }

    /**
     * @return \think\response\View
     */
    public function readFromMongo() {
        $db = db_mongo();
//        $db1 = db_mysql();
        //修复2017-04后面到数据
        $vips = $db->name('vip')->where([
            'amt' => 0,
            'operDate' => ['>=', '2017-04'],
            'isDeal' => ['neq', 1]
//            'merchantId' => '10000201'
        ])->select();

        $vips = array_reverse($vips);

        $merchants = $db->name('merchant')->select();
        foreach ($merchants as $key => $val) {
            $kindex = $val['merchantId'].'-'.$val['branchId'];
            $merchants[$kindex] = $val['name'];
        }

        $flowIds = [];
        foreach ($vips as $key => $val) {
            $flowIds[] = $val['vipFlowId'];
            $kindex = $val['merchantId'].'-'.$val['branchId'];
            try {
                $vips[$key]['name'] = $merchants[$kindex];
            }catch (\Exception $e) {
            }
        }
        $flowIdsStr = join(',', $flowIds);


        return view('readFromMongo', [
            'vips' => $vips
        ]);
    }

    public function getVipflow($flowId, $operDate, $branchId) {
        $db = db_mysql();
        $vip = $db->name('viprecharge')->where([
            'VipId' => $flowId,
            'BranchId' => $branchId,
            'OperDate' => [
                'like', $operDate.'%'
            ]
        ])->find();

        return json([
            'status'    => 1,
            'obj'       => $vip
        ]);
    }

    public function repiarMongo($flowId, $getAmt, $totalAmt, $operDate) {
        $amt = $totalAmt - $getAmt;
        $data = [
            'amt' => $amt,
            'totalAmt' => $totalAmt,
            'getAmt'    => $getAmt,
            'isDeal'    => 1
        ];
        $where = ['vipFlowId' => $flowId, 'operDate' => $operDate];

        $db = db_mongo();
        $effectCounts = $db->name('vip')->where($where)->update($data);
        return ['status' => $effectCounts];
    }
}
