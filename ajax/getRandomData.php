<?php
/**
 * User: maqiuyu
 * 抽奖接口，第一次调用随机生成中奖人员，第二次调用返回上次中奖人员（为了防止重复调用）
 * Date: 2017/1/13
 * Time: 21:58
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
}
catch (Exception $e){
    $result['msg'] = $e->getMessage();
    echo json_encode($result);
    exit();
}

try{

    $level=isset($_REQUEST['level'])?$_REQUEST['level']:0;
    if(empty($level) || !isset($levels[$level]))
    {
        throw new Exception("No Level");
    }
    $count=$levels[$level];
    if(empty($count)){
        throw new Exception("No Count");
    }
    $rows=$DB->fetchAll("SELECT id,user_id,user_name,dept_name FROM user where is_selected=1 and level={$level} order by sort");
    if(empty($rows))
    {
        unset($rows);
        $allData = $DB->fetchAll("SELECT id FROM user");
        $allDatas = array_column($allData,'id');

        $filter = $DB->fetchAll("SELECT id FROM user where is_selected = 1");
        $filters = array_column($filter,'id');

        //有效数据，剔除已经中奖的id
        $effectData = array_diff($allDatas,$filters);
        //从新生成key值
        $effectData = array_values($effectData);

        $min = 0;
        $users=array();

        while (count($users) < $count){
            $max = count($effectData)-1;
            $rand = mt_rand($min,$max);
            $id = $effectData[$rand];
            //抽中 后删除
            array_splice($effectData,$rand,1);
            if(!in_array($id,$filters) && !in_array($id,$users)){
                $users[]=$id;
            }
        }
        error_log(print_r($users,1));
        $DB->pdo->beginTransaction();
        //这里失败回滚
        try{
            $sql="";
            for($i=0;$i<$count; $i++){
                $id=$users[$i];
                $sql="{$sql} update user set is_selected=1,selected_time=now(),level={$level},sort={$i} where id ={$id} ;";
            }
            $DB->execute($sql);

            $rows=$DB->fetchAll("SELECT id,user_id,user_name,dept_name FROM user where is_selected=1 and level={$level} order by sort");
            if(count($rows)!=$count){
                $rows=array();
                $DB->pdo->rollBack();
            }else{
                $DB->pdo->commit();
            }
        }catch (Exception $exc){
            $DB->pdo->rollBack();
            throw $exc;
        }

    }
    $result['code']    = 200;
    $result['msg']     = 'success';
    $result['total']  = count($rows);
    $result['result']  = $rows;

}catch (Exception $e){
    $result['msg'] = $e->getMessage();
}
echo json_encode($result);