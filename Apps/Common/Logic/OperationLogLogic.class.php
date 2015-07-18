<?php
namespace Common\Logic;
class OperationLogLogic extends Logic {
    
    /*
     * ol_type 1资源推荐 2资源推优 3资源推送 4资源发布 5资源审核 6资源淘汰
     */
    public function insert($data) {

        $saveData = D('OperationLog', 'Model')->create($data);
        if (!$saveData) {
            $this->error = D('OperationLog', 'Model')->getError();
            return false;
        }

        return D('OperationLog', 'Model')->insert($saveData);
    }
}