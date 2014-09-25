<?php $this->load->view('include/header');?>
	<div class="main-container-inner">            
		<div class="page-content">
			<div class="page-header">
				<h1>雷尚游戏管理系统</h1>
				<p>
				<ul class="nav nav-tabs" role="tablist" id="tabs">
					<li role="presentation" id="tab_app_list" class="active"><?php echo anchor('/applist/applist', 'App List'); ?></li>
					<li role="presentation" id="tab_big_view"><?php echo anchor('big_view/view', 'Big View'); ?></li>
				</ul>
				<p>

				<?php echo $content;?>
			</div><!-- /.page-header -->

		</div><!-- /.page-content -->
	</div><!-- /.main-container-inner -->

<script type="text/javascript">
$(document).ready(function() {
	if(window.location.pathname.search(/\/applist\/applist/) != -1) {
		$('#tab_app_list').addClass('active');
	} else if(window.location.pathname.search(/\/big_view\/view/) != -1) {
		$('#tab_big_view').addClass('active');
	}
});
</script>

<?php
if(!empty($_COOKIE['rayjoyuser1502'])) {
	$user = json_decode($_COOKIE['rayjoyuser1502'], true);
	if($user['group'] == 1) {
		echo anchor('/accountant/audit', '审计');
	} else { ?>
		<script type="text/javascript">$('#tab_big_view').remove();</script>
	<?php }
} else { ?>
		<script type="text/javascript">$('#tabs li').remove();</script>
<?php }
?>
<?php $this->load->view('include/footer');?>
