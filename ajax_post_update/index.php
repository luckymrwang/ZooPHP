<style type="text/css">
.col-sm-9.col-sm-offset-3.col-md-10.col-md-offset-2.main form div #myselect {
	width: 80px;
	height: 20px;
}
.col-sm-9.col-sm-offset-3.col-md-10.col-md-offset-2.main form div table tr td input {
	margin-left: 10px;
}
.col-sm-9.col-sm-offset-3.col-md-10.col-md-offset-2.main .table-responsive .table.table-striped thead tr th {
	text-align: center;
}
.col-sm-9.col-sm-offset-3.col-md-10.col-md-offset-2.main .table-responsive .table.table-striped tbody tr td {
	text-align: center;
}
.col-sm-9.col-sm-offset-3.col-md-10.col-md-offset-2.main form #right {
	float: right;
	margin-bottom: 10px;
}

thead.scrollHead,tbody.scrollBody{
  display:block;
}
tbody.scrollBody{
  overflow-y:scroll;
  height:700px;
  width: 1470px;
}
</style>
<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
    <h1 class="page-header">考勤信息</h1>
	<form action='<?php echo site_url("as/home") ?>' method='post'>
	<div>
    	<div id="right"><a href="<?php echo site_url('as/home/export_excel_overall').'/'.$cal_select ?>" class="btn btn-primary  btn-middle" >导出数据</a></div>
		<table>
  			<tr>
    			<td width="77">选择日期：</td>
				<td width="81"><select name='myselect'>
				<?php foreach ($calendar as $cal) { ?> <option value='<?php echo $cal['date'] ?>' <?php if ($cal_select <> "" and $cal_select == $cal['date']) echo "selected"; ?> ><?php echo $cal['date'] ?></option> <?php }?>
			</select></td>
    			<td width="86"><input type='submit' value='查看' class="btn btn-sm btn-primary"></td>
			</tr>
		</table>
	</div>
    </form>
<div class="table-responsive">
	<table class="table table-bordered table-hover table-striped tablesorter">
		<thead class="scrollHead">
                <tr>
                <th width="60">员工号</th>
                <th width="60">姓名</th>
                <th width="60">小加班</th>
                <th width="50">迟到</th>
				<th width="150">当月抵扣后迟到次数</th>
                <th width="60">大加班</th>
                <th width="145">非工作日加班半天</th>
				<th width="190">截止上月全年累计加班天数</th>
                <th width="75">请假天数</th>
                <th width="160">当月抵扣后请假天数</th>
                <th width="205">本月抵扣后全年累计加班天数</th>
				<th width="234">备注</th>
                </tr>
        </thead>
            <tbody class="scrollBody">
                <?php foreach ($info as $val) { ?>
                    <tr id="<?php echo $val['id'];?>">
                    	<td width="60">
                            <?php echo $val['enno'] ?>
                        </td>
                        <td width="60">
                            <?php echo $val['name'] ?>
                        </td>
                        <td width="60" id="work_extra_small" uid="<?php echo $val['id']; ?>" pid="work_extra_small" class="table_txt" >
                            <?php echo $val['work_extra_small'] ?>
                        </td>
                        <td width="50" id="be_late" uid="<?php echo $val['id']; ?>" pid="be_late" class="table_txt">
                            <?php echo $val['be_late'] ?>
                        </td>
                        <td width="150">
                            <?php echo $val['offset_late'] ?>
                        </td>
                        <td width="60" id="work_extra_big" uid="<?php echo $val['id']; ?>" pid="work_extra_big" class="table_txt">
                            <?php echo $val['work_extra_big'] ?>
                        </td>
                        <td width="145" id="work_weekend" uid="<?php echo $val['id']; ?>" pid="work_weekend" class="table_txt">
                            <?php echo $val['work_weekend'] ?>
                        </td>
                        <td width="190">
                            <?php echo $val['work_extra_sum'] ?>
                        </td>
                        <td width="75" id="ask_leave" uid="<?php echo $val['id']; ?>" pid="ask_leave" class="table_txt">
                            <?php echo $val['ask_leave'] ?>
                        </td>
                        <td width="160" id="offset_leave" uid="<?php echo $val['id']; ?>" pid="offset_leave" class="table_txt">
                            <?php echo $val['offset_leave'] ?>
                        </td>
                        <td width="205">
                            <?php echo $val['offset_sum'] ?>
                        </td>
						<td id="desc" style="width:234px;word-break:break-all;" uid="<?php echo $val['id']; ?>" pid="desc" class="table_txt">
							<?php echo $val['desc'];?>
                        </td>
                    </tr>
                <?php } ?>
		</tbody>
	</table>
</div>
</div>
<script type="text/javascript">
$().ready(function(){
	$(".table_txt").dblclick(function(){
		var id = $(this).attr("uid");
		var pid = $(this).attr("pid");
		var value = $(this).text();
		if(pid == 'desc'){
			$(this).html("<input style='width:220px;' type='text' id="+pid+id+" value="+value+">");
		} else {
			$(this).html("<input style='width:30px;' type='text' id="+pid+id+" value="+value+">");
		}
		$(".table_txt > input").blur(function(){
			var val = $("#"+pid+id).val();
			if( val != value){
				if(pid != 'desc' && !$.isNumeric(val)){
					alert('格式不正确，请输入正数字！');
//					exit;
					window.location.href = "<?php echo 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; ?>";
				} else if(pid != 'desc' && val < 0){
					alert('格式不正确，请输入正数字！');
//					exit;
					window.location.href = "<?php echo 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; ?>";
//				if(confirm("您确定要修改吗？")){
				} else {
					var url = "<?php echo site_url("as/home/ajax_update_attendance"); ?>";
					$.post(url,{
						ajax_id: id,
						ajax_pid: pid,
						ajax_txt: val,
					},
					function(data,status){
//						window.location = "<?php echo 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; ?>";
						if(status == 'success')
//							alert(data);
							$("#"+id+"").html(data);
					});
//				} else {
//					$("#"+pid+id).val(value);
////				};
				}
			};
		});
	});
});
</script>
</div>
</div>
