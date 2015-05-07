<?php
$area_zid = $this->load->view('include/widget/area_select', '', TRUE);
$date_widget = $this->load->view('include/widget/single_date', '', TRUE);
?>
<div class="page-header">
	<?php
	echo $area_zid;
	?>
</div>

<div class="row">
	<div class="panel panel-primary">
        <div class="panel-heading">	
            <form action="<?php echo site_url($current_base_url); ?>" method="get">
                <h3 class="panel-title">
					<i class="fa fa-money">
						<input type="hidden" name="big_app_id" value="<?php echo $big_app_id; ?>">
						<input type="hidden" name="sub_app_id" value="<?php echo $selected_app_id; ?>">
						<input type="hidden" name="zid" value="<?php echo $selected_zid; ?>">
						用户ID: <input type="text" name="uid" required="required" value="<?php echo $uid;?>">
						<?php echo $date_widget; ?>
						<button type="submit" class="btn btn-sm btn-success" onclick="query()">query</button>
					</i>
				</h3>
			</form>
		</div>

		<div class="panel-body">
			<div class="table-responsive">
				<?php foreach($view_items as $key => $item){ ?>
					<table class="table table-bordered table-hover table-striped tablesorter">
						<thead>
							<tr>
								<?php foreach($item_descs[$key] as $ke => $value){ ?>
									<td><?php
										if($ke == 'addition'){
											echo "$value<i style='cursor:pointer' class='blue icon-question-sign' data-rel='tooltip' data-placement='top' title='' data-original-title='鼠标移动到如下项目中显示信息详情'></i>";
										} else {
											echo "$value";
										}?>
									</td>
								<?php } ?>
							</tr>
						</thead>
						<tbody>
							<?php foreach($item as $val){ ?>
							<tr>
								<?php foreach($item_descs[$key] as $k => $v){ ?>
								<td><?php if($k == 'time'){
										echo date('Y-m-d H:i:s',$val['time']);
									} elseif($k == 'addition'){
										if(!empty($val['addition'])){
											$str_json = json_encode($val['addition']);
											$json = strlen($str_json) > 45 ? substr($str_json, 0, 42)."..." : $str_json;
											echo "<div class='div_show' id='div_show' style='cursor:pointer;'>".$json."<input type='hidden' name='json' value='".$str_json."'></div>";
										}
									} else {
										echo $val[$k];
									} ?>
								</td>
								<?php } ?>
							</tr>
							<?php } ?>
						</tbody>
					</table>
				<?php } ?>
			</div>
		</div>
	</div>
</div>
<div id="float_box" style="display: none;"></div>
<script type="text/javascript">
$(document).ready(function(){
	$(".div_show").mouseover(function(){
		var ps = $(this).position();
		var data = $(this).find("input").val();
		var result = JSON.stringify($.parseJSON(data), null, 4);
//		var result = JSON.stringify($.parseJSON(data), null, '<br>&nbsp;');
		
		$("#float_box").css("position", "absolute");
		$("#float_box").css("right", $(document).width() - ps.left);
		$("#float_box").html('<pre>'+result+'</pre>');
//		$("#float_box").html(result.replace(new RegExp('(<br>&nbsp;)+',"gm"),'<br>&nbsp;'));
		
		if((ps.top + $("#float_box").height() + 30) < $(document).height()){
			$("#float_box").css("top", ps.top + 20);
		} else {
			$("#float_box").css("top", ps.top + 20 - $("#float_box").height());
		}
		
		$("#float_box").show();
		$(".div_show").mouseout(function(){
			$("#float_box").hide();
		});
	});
	$('[data-rel=tooltip]').tooltip();
	$('[data-rel=popover]').popover({html:true});
})
</script>

