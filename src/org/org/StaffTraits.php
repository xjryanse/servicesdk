<?php

namespace xjryanse\servicesdk\org\org;

/**
 * 员工trait
 */
trait StaffTraits {
    /**
     * 员工入职
     * @param type $userId              用户
     * @param type $joinDate            入职日期
     * @param type $contractFinalTime   合同到期
     * @return type
     */
    public function staffIn($userId, $joinDate, $contractFinalTime){
        $baseUrl            = 'org/staff/in';
        $data               = $this->postBaseData();
        $data['user_id']                = $userId;
        $data['join_date']              = $joinDate;
        $data['contract_final_time']    = $contractFinalTime;

        $res            = $this->queryLog($baseUrl, $data, 'worker');
        return $res['data'];
    }
    /**
     * 员工离职
     * @param type $id
     * @param type $leaveTime   离职时间
     * @param type $leaveJogId  离职时岗位
     * @return type
     */
    public function staffOut($id, $leaveTime, $leaveJogId){
        $baseUrl            = 'org/staff/out';
        $data               = $this->postBaseData();
        $data['id']             = $id;
        $data['leave_time']     = $leaveTime;
        $data['leave_job_id']   = $leaveJogId;

        $res            = $this->queryLog($baseUrl, $data, 'worker');
        return $res['data'];
    }
    
}
