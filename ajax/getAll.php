<?php
include_once('pdo.php');
$result = array('code' => 500, 'msg' => 'error', 'result' => 0);

try{
    $users=array();
    $DB = DBPDO::getInstance();
    $users = $DB->fetchAll("SELECT id,user_id,user_name,phone,dept_name FROM user where is_selected = 0 order by rand()");

    $result['code']    = 200;
    $result['msg']     = 'success';
    $result['result']  = $users;

}catch (Exception $e){
    $result['msg'] = $e->getMessage();
}
echo json_encode($result);
