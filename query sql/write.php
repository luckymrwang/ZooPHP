<?php 
function write($table) {
	//$table_sql = file_get_contents('./table_data_user_ips.sql');
	$sql = "use ga; select userid,sum(cost) as sum_cost from $table group by userid order by sum_cost desc;"; 
	echo $sql;
	system("mysql -u root -e \"$sql\" > $table.txt ");
	echo "$table ok\n";
}

$tables = array(
 '360android_1011',
 'android_1015',
 'android_3k_1020',
 'android_sevenga_1018',
 'dny_efun_1014',
 'fl_guonei_1016',
 'fl_yueyu_1017',
 'ios_sevenga_1019',
 'ioskuaiyong_1009',
 'jp_1029',
 'korea_1025',
 'lenovo_1021',
 'mmandroid_3k_1023',
 'na_1026',
 'na_1mobile_1028',
 'nanmei_1027',
 'order_1031_arab',
 'russia_1024',
 'tw_efun_1013',
);
foreach($tables as $table) {
	write($table);
}
