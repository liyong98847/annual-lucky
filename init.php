<?php
include_once('ajax/pdo.php');
if(!empty($_POST)){
    $DB = DBPDO::getInstance();
    $sql="update user set is_selected=0,level=0,selected_time=null,sort=0";
    $execRet=$DB->execute($sql);
    $msg="初始化成功了";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>init</title>
</head>
<body>
<div>
    <form id="form1" method="post" action="init.php">
        <div><?php echo isset($msg)?$msg:""; ?></div>

        <input name="initname" id="btnInit" type="submit" onclick="return init();" style="width:100px;height:50px;" value="初始化数据" />
    </form>

</div>
</body>
<script>
    function init() {
        if(!confirm("确定要初始化数据吗？抽奖记录将清空？")){
            return false;
        }
    }
</script>
</html>
