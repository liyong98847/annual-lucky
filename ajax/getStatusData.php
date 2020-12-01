<?php
/**
 * User: maqiuyu
 * 获取各轮状态
 * Date: 2017/1/13
 * Time: 21:59
 */
include_once('pdo.php');
$result = array('code' => 500, 'msg' => 'error', 'result' => 0);
$status=array(
    1 => 0,
    2 => 0,
    3 => 0,
    4 => 0,
    5 => 0,
    6 => 0
);
try{
    $DB = DBPDO::getInstance();


    $rows=$DB->fetchAll("SELECT level,count(*) as level_count FROM user group by level");
    foreach($rows as $row){
        if(isset($status[$row['level']])){
            $status[$row['level']]=(int)$row['level_count'];
        }
    }
    
    $result['code']    = 200;
    $result['msg']     = 'success';
    $result['result']  = $status;


}catch (Exception $e){
    $result['msg'] = $e->getMessage();
}
echo json_encode($result);