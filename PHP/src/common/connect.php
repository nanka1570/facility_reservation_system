<?php
/*//MariaDB(SSD内のxampp用)
define('cDBip','localhost');
define('cDBname','卒研');
define('cDBid','USER1');
define('cDBpass','a');

$srvIp = cDBip;
$srvName = cDBname;
$srvId = cDBid;
$srvPass = cDBpass;
$conn = new PDO("mysql:host=$srvIp;dbname=$srvName;charset=utf8",$srvId,$srvPass);*/


//卒研用のSQLserver
define('cDBip','192.168.52.111');//IPアドレス
define('cDBname','M12JS2B');//DB名
define('cDBid','sa');//ID
define('cDBpass','Js2+');//パスワード

// define('cDBip','localhost');
// define('cDBname','ebi');
// define('cDBid','a');
// define('cDBpass','a');
$srvIp=cDBip;
$srvName=cDBname;
$srvId=cDBid;
$srvPass=cDBpass;
$conn=new PDO("sqlsrv:server=$srvIp;Database=$srvName",$srvId,$srvPass);
?>