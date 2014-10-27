<?php
	$big_app_select = $this->load->view('include/widget/big_app_select', '', true); 
	$menu_name_level1 ='运营管理';
	$menu_name_level2 = '删除区服';
?>
<div class="page-header">
    <h1>
        <span><?php echo $menu_name_level1;?></span>
        <span><i class="icon-double-angle-right"></i><small><?php echo $menu_name_level2; ?></small></span>
    </h1>
</div>

<div class="row">
    <div class="panel panel-primary">
        <div class="panel-heading"></div>

		<div class="panel-body">
			<div class="table-responsive">
			   <form action="" method="post">
				大平台名 : <?php echo $big_app_select;?><br><br>
				<br>
				<div id='zids'>
					已开服 :  <?php foreach($init_servers as $info): ?>
						<input type='checkbox' name='zid_selected[]' value='<?php echo $info;?>' onmouseover='hideerror()'>  <?php echo $info;?>
					<?php endforeach;?>
				</div>
				<br><br>
				<div id='msg_div'><font color='red'><?php if ($msg == 1){
					echo "删除成功！";
				} elseif (!isset($msg) && $msg == 0) {
					echo "删除失败，请重新操作！";}?></font></div>  
				<br>
				<input style="width:340px" class="btn btn-sm btn-primary btn-block" type="submit" name='submit' value='删除'>
			  </form>
			</div>
		</div>
    </div>
</div>

<script type="text/javascript">
function hideerror(){
	$("#msg_div").hide();
}
$(document).ready(function() {
	$("#big_app_id").change(function() {
		var big_app_id_value = $("#big_app_id").val();
		htmlobj = $.ajax({url: "<?php echo site_url("accountant/manage/get_new_servers") ?>" + "/" + big_app_id_value, async: false});
		var str = htmlobj.responseText;
		var strs = new Array();
		strs = str.split(",");
		var html_string = "已开服 : ";
		for (var i = 0; i < strs.length; i++){
			html_string += "<input type='checkbox' name='zid_selected[]' value='" + strs[i] + "' onmouseover='hideerror()'> " + strs[i] + " ";
		}
		$("#zids").html(html_string);
	});
});
</script>