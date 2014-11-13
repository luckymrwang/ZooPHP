<?php 
$project_widget = $this->load->view('include/widget/project_widget','',TRUE);
$priority_widget = $this->load->view('include/widget/priority_widget','',TRUE);
?>

<div id="showheader" class="page-header"></div>
<div class="page-header">
    <h1>
        <?php echo $project_widget;?>
        <?php echo $priority_widget;?>
    </h1>
</div>

<div class="panel-body">
	<div class="table-responsive">
		<!-- 约定：表格显示的项及次序靠$item_descs来控制 -->
		<?php foreach($items as $task_infos):?>
		<table class="table table-bordered table-hover table-striped tablesorter">
			<thead>
				<tr>
					<?php foreach($item_descs as $item_desc): ?>
						<th><?php echo $item_desc; ?></th>
					<?php endforeach; ?>
				</tr>
			</thead>
			<tbody>
				<?php $i = 0; foreach($task_infos as $item): $falg = ($task_infos[0]['priority']==$item['priority'])? "<font color=red>":'';?>
					<tr>
		                <td id="title_<?php echo $i++; ?>" uid="<?php echo $item['id']; ?>" pid="title" class="table_txt" ><?php echo $falg;?><?php echo $item['title']; ?></font></td>
						<td id="project_<?php echo $i++; ?>" uid="<?php echo $item['id']; ?>" tid="<?php echo $item['project_id']; ?>" pid="project_id" class="table_td" ><?php echo $falg;?><?php echo $item['pro_name']; ?></font></td>
						<td id="status_<?php echo $i++; ?>" uid="<?php echo $item['id']; ?>" tid="<?php echo $item['status']; ?>" pid="status" class="table_td" ><?php echo $falg;?><?php echo $status[$item['status']]; ?></font></td>
						<td id="priority_<?php echo $i++; ?>" uid="<?php echo $item['id']; ?>" tid="<?php echo $item['priority']; ?>" pid="priority" class="table_txt" ><?php echo $falg;?><?php echo $item['priority']; ?></font></td>
						<td><?php if ($item['done_time'] != 0) echo $falg.date('Y-m-d',$item['done_time']); ?></font></td>
						<td id="desc_<?php echo $i++; ?>" uid="<?php echo $item['id'] ?>" pid="desc" class="table_txt" ><?php echo $falg;?><?php echo $item['desc']; ?></font></td>
						<td><?php echo anchor("start/delete_task/$item[id]",'删除','onclick="return confirm(\'确定要删除这条任务吗？\');"'); ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php endforeach;?>
	</div>
</div>
<script language="javascript">
$().ready(function(){
	$(".table_td").dblclick(function(){
		var id = $(this).attr("id");
		var tid = $(this).attr("tid");
		var pid = $(this).attr("pid");
		var uid = $(this).attr("uid");
		var value = $(this).text();
		if(pid == 'status'){
			$(this).html("<select id='"+pid+id+"'><?php foreach($status as $k => $v): ?><option value='<?php echo $k; ?>'><?php echo $v; ?></option><?php endforeach; ?></select>");
			$("#"+pid+id+"").val(tid);
		} else {
			$(this).html("<select id='"+pid+id+"'><?php foreach($project_widget_param as $v): ?><option value='<?php echo $v['pro_id']; ?>'><?php echo $v['pro_name']; ?></option><?php endforeach; ?></select>");
			$("#"+pid+id+"").val(tid);
		};
		$(".table_td > select").blur(function(){
			if($("#"+pid+id+" option:selected").text() != value){
				if(confirm("您确定要修改吗？")){
					var url = "<?php echo site_url("start/ajax_update_task");?>";
					$.post(url,{
						ajax_id: uid,
						ajax_pid: pid,
						ajax_txt: $("#"+pid+id+" option:selected").val(),
					},
					function(data,status){
						window.location = "<?php echo 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];?>";
					});
				} else {
					$("#"+pid+id+"").val(tid);
				};
			};
		});
	});

	$(".table_txt").dblclick(function(){
		var id = $(this).attr("uid");
		var pid = $(this).attr("pid");
		var value = $(this).text();
		$(this).html("<input type='text' id="+pid+id+" value="+value+">");
		$(".table_txt > input").blur(function(){
			var val = $("#"+pid+id).val();
			if( val != value){
				if(pid == 'priority' && isNaN(val)){
					alert('格式不正确，请输入数字！');
					exit;
				};
				if(confirm("您确定要修改吗？")){
					var url = "<?php echo site_url("start/ajax_update_task"); ?>";
					$.post(url,{
						ajax_id: id,
						ajax_pid: pid,
						ajax_txt: val,
					},
					function(data,status){
						window.location = "<?php echo 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; ?>";
					});
				} else {
					$("#"+pid+id).val(value);
				};
			};
		});
                
                //alert(html_string);
	});
        var ul_open = $(".active .menu-text").text();
        var li_open = $(".submenu .active > a").text();
        var html_string = "<h1><span>"+ul_open+"</span><span><i class='icon-double-angle-right'></i><small>"+li_open+"</small></span></h1>";
        $("#showheader").html(html_string);
});
</script> 