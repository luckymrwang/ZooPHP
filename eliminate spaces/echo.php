<?php
$file = 'D:/tool/data.txt';
$content = file_get_contents($file);
$array = explode("\r\n", $content);
//print_r($array);
for($i=1; $i<10; $i++)
{
	$array[$i] = preg_replace('/\s(?=\s)/','',$array[$i]);	//去掉字符串中连续多余的空格
	$array[$i] = preg_replace('/[\n\r\t]/',' ',$array[$i]);	//去掉非空格的空白，用一个空格代替
    echo $array[$i]."\n";
	$result = explode(" ",$array[$i]);
	echo "Name:".$result[3]."\n";
	echo "Time:".$result[4]." ".$result[5]."\n";
}
?>