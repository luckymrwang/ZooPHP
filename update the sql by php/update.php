<?php 
function update($db) {
	$table_sql = file_get_contents('./table_data_user_ips.sql');//�������ļ������ַ���
	$cnn = mysql_connect("localhost","root","");
	mysql_query("set names 'utf8'");
	mysql_select_db($db,$cnn);
	$result = mysql_query($table_sql);
	mysql_close($cnn);
	echo "db $db ok\n";
}
$dbs = array(
	"ga_toy_war",
);
foreach($dbs as $db) {
	update($db);
}
