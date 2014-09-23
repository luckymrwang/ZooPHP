<?php 
function update($db) {
	$table_sql = file_get_contents('./table_data_user_ips_call_system.sql');
	$sql = "use $db; $table_sql";
	system("mysql -u root -e \"$sql\"");
	echo "db $db ok\n";
}

$dbs = array(
	'ga',
	'ga_toy_war',
	'ga_war_commander',
	'ga_red_fire',
	'ga_world_war',
);
foreach($dbs as $db) {
	update($db);
}