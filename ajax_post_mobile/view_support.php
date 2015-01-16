<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>客服支持</title>
<link rel="stylesheet" href="http://code.jquery.com/mobile/1.3.2/jquery.mobile-1.3.2.min.css">
<script src="http://code.jquery.com/jquery-1.8.3.min.js"></script>
<script src="http://code.jquery.com/mobile/1.3.2/jquery.mobile-1.3.2.min.js"></script>
</head>
<body>
<div id="pageone" data-theme="b">
<table border="0">
  <tr><td>
        <div data-role="header">
          <h1>客户支持</h1>
          <a href="javascript:history.go(-1);" data-role="button" class="ui-btn-right" data-icon="delete">关闭</a>
        </div>
  </td></tr>
  <tr><td>
  	<fieldset data-role="collapsible">
          <legend><center>角色</center></legend>
          <p><b>角色名称：</b><?php echo $user_name;?></p>
          <input type="hidden" name="user_id" value="<?php echo $user_id;?>" />
		  <p><b>服务器：</b><?php echo $zid;?></p>
          <input type="hidden" name="zid" value="<?php echo $zid;?>" />
        </fieldset>
  </td></tr>
  <tr><td><a href="#" data-role="button" data-icon="grid">类型</a></td></tr>
  <tr><td>
  	<div data-role="content">
          <fieldset data-role="controlgroup">
			  <input name="radio" type="radio" value="1" id="问题反馈" checked /><label for="问题反馈">问题反馈</label>
			  <input name="radio" type="radio" value="2" id="账号安全" /><label for="账号安全">账号安全</label>
			  <input name="radio" type="radio" value="3" id="游戏建议" /><label for="游戏建议">游戏建议</label>
          </fieldset>
  	</div>
  </td></tr>
  <tr><td><a href="#" data-role="button" data-icon="grid">叙述</a></td></tr>
  <tr><td><b>标题：</b><input name="title" style="width:80%;" type="text" data-role="none" required="required" /></td></tr>
  <tr><td>
<textarea id="status" required="required" name="status" rows="6" cols="40" placeholder="输入信息..." onkeydown='countChar("status","counter");' onkeyup='countChar("status","counter");'></textarea>
<div id="right" style="float:right">可以输入<span id="counter">140</span>字</div></td></tr>
</table>
    <button type="submit" id="submit" data-inline="true" onclick="return checklength();">提交</button>
</div>
</body>
<script language="javascript">  
function countChar(textareaName,spanName){
  document.getElementById(spanName).innerHTML = 140 - document.getElementById(textareaName).value.length;
}
function checklength(){
    var text_length = document.getElementById("status").value.length;
    if (parseInt(text_length) > 140){
        alert("字数超过140字！");
		document.getElementById("status").focus();
		return false;
	} else if (parseInt(text_length) < 20){
		alert("字数至少需要超过20字！");
		document.getElementById("status").focus();
		return false;
	}
	var user_id = $('[name="user_id"]').val();
	var zid = $('[name="zid"]').val();
	var radio = $('[name="radio"]').val();
	var title = $('[name="title"]').val();
	var content = $('[name="status"]').val();
	var url = "<?php echo site_url("service/problem/handle_support")?>";
		$.post(url,{
			user_id: user_id,
			zid: zid,
			radio: radio,
			title: title,
			content: content,
		},
		function(data,status){
			if(data != ""){
				alert("存在敏感词'"+data+"'，请删除后再提交!");
			}
		});
}
</script> 
</html>