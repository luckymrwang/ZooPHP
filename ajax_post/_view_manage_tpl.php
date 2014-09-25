<div class="page-header">
	<h1>
		<span><?php echo $menu_name_level1; ?></span>
		<span><i class="icon-double-angle-right"></i><small><?php echo $menu_name_level2; ?></small></span>
	</h1>
</div>

<div class="row">
	<div class="panel panel-primary">
		<div class="panel-heading">
			<form action="<?php echo site_url($current_base_url); ?>" method="get">
				<h3 class="panel-title">
					<i class="fa fa-money">
						
						<?php if(!empty($form_items)) : ?>
							<?php foreach($form_items as $item) : ?>
								<?php echo $item; ?>
							<?php endforeach; ?>
							<button type="submit">query</button>
						<?php endif; ?>
					</i>
				</h3>
			</form>
		</div>
		
		<div class="panel-body">
			<div class="table-responsive">
				<!-- 约定：表格显示的项及次序靠$item_descs来控制 -->
				<table class="table table-bordered table-hover table-striped tablesorter">
					<thead>
						<tr>
							<?php foreach($item_descs as $item_desc): ?>
								<th><?php echo $item_desc; ?></th>
							<?php endforeach; ?>
						</tr>
					</thead>
					<tbody>
						<?php
							function check_danger($item) {
								foreach($item as $value) {
									if($value === '!!!请配置!!!')
										return true;
								}
								return false;
							}
						?>
						<?php foreach($items as $item): ?>
							<tr <?php if(check_danger($item)) echo 'class="danger"'; ?>>
	
                                <td><?php echo $item['appid']; ?></td>
									<td uid="<?=$item['appid'] ?>" id="temp" class="unm" ><?php echo $item['appname']; ?></td>
								<?php endforeach; ?>
							</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<script language="javascript">
$().ready(function(){
$(".unm").dblclick(function(){
	var id=$(this).attr("uid");
	var value=$(this).text();
	if(value){
		$(this).html("<input type='text' id="+id+" value="+value+">");
		$(".unm > input").blur(function(){
			if($("#"+id).val() != value){
				if(confirm("您确定要修改吗？")){
					var url="<?=site_url("accountant/manage/change_name_by_appid")?>";
					$.post(url,{
						pid: id,
						pname: $("#"+id).val(),
					},
					function(data,status){
					//setTimeout('window.location= "<?php echo 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];?>"',3000)
    				//alert("Status: " + status);
					window.location = "<?php echo 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];?>";
					});
				};
			};
		});
	};
});
});
</script> 