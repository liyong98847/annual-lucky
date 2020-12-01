<?php
/**
 * User: maqiuyu
 * 获取抽奖结构数据
 * Date: 2017/1/13
 * Time: 21:59
 */
include_once('pdo.php');
$result = array('code' => 500, 'msg' => 'error', 'result' => 0);
$levels=array(
    1 => 30,
    2 => 35,
    3 => 11,
    4 => 2,
    5 => 25,
    6 => 2
);
try{
    $DB = DBPDO::getInstance();

    $level=isset($_REQUEST['level'])?$_REQUEST['level']:0;
    if(empty($level))
    {
        throw new Exception("No Level");
    }

    $rows=$DB->fetchAll("SELECT id,user_id,user_name,dept_name FROM user where is_selected=1 and level={$level} order by sort");

    $result['code']    = 200;
    $result['msg']     = 'success';
    $result['result']  = $rows;


}catch (Exception $e){
    $result['msg'] = $e->getMessage();
}
echo json_encode($result);