<?php 
$project_widget = $this->load->view('include/widget/project_widget','',TRUE);
$priority_widget = $this->load->view('include/widget/priority_widget','',TRUE);
?>
<div class="page-header">
	<h1>
		<span><?php echo $banner['menu_desc1'];?></span>
		<span><i class="icon-double-angle-right"></i><small><?php echo $banner['menu_desc2'];?></small></span>
		<?php echo $project_widget;?>
		<?php echo $priority_widget;?>
	</h1>
</div>	
<div class="panel-body">
	<div class="table-responsive">
		<form action='<?php echo site_url("start/batch_update_priority"); ?>' method='post'>
			<table class="table table-bordered table-hover table-striped tablesorter">
				<td width="80px">全选 ：<input type="checkbox" name="select_all" id="select_all"></td>
				<td width="80px" style="text-align:center">优先级:</td>
				<td width="120px"><input type="text" name ="select_priority" ></td>
				<td><input type='submit' value='修改' class="btn btn-xs btn-info" ></td>
			</table>
		<!-- 约定：表格显示的项及次序靠$item_descs来控制 -->
		<table class="table table-bordered table-hover table-striped tablesorter">
			<thead>
				<tr>
					<th></th>
					<?php foreach($item_descs as $item_desc): ?>
					<th><?php echo $item_desc; ?></th>
					<?php endforeach; ?>
				</tr>
			</thead>
			<tbody>
				<?php $i = 0; foreach($items as $item): ?>
					<tr>
						<td><input type="checkbox" name="task_id[]" value="<?php echo $item['id'];?>"></td>
		                <td><?php echo $item['title']; ?></td>
						<td><?php echo $item['pro_name']; ?></td>
						<td><?php echo $status[$item['status']]; ?></td>
						<td><?php echo $item['priority']; ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		</form>
	</div>
</div>

<script type="text/javascript">
	$(function() {
		//方法一
		var select_all = $("#select_all");
		var task_id = $('input[name="task_id[]"]');
        select_all.change(function(){
            task_id.prop("checked",this.checked);
        });
        task_id.click(function(){
             select_all.prop("checked",task_id.length == $("input[name='task_id[]']:checked").length ? true : false);
        });
		//方法二
		$("#select_all").click(function() {
			if ($(this).prop("checked")) {
				$("input[name='task_id[]']").each(function() {
					$(this).prop("checked",true);  
				});
			} else {
				$("input[name='task_id[]']").each(function() {  
					$(this).prop("checked",false);  
            });
		};
	});	
});
</script>