<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class Manage extends MY_Controller {
	
	public function __construct() {
		parent::__construct();

		$this->layout = 'layout/classic';
		$this->layout_data['title'] = 'Manage';
		$this->layout_data['sidebar_file'] = 'manage';

		$this->load->model('Model_app');
		$this->load->model('Model_admin_manage');
	}

	public function index() {
		redirect('accountant/manage/change_name_appid');
	}

	public function change_name_appid() {
		if(!$this->_check_user() || !$this->_check_permission()) {
			$this->_please_login();
			return;
		}

		$game_name = $this->input->get_post('game');
		
		$view_data['item_descs'] = array(
			'appid' => '游戏ID',
			'appname' => '游戏名称  【双击游戏名称进行修改】',
		);
		
		if($game_name === false) {
			$view_data['items'] = array();
			$this->render('accountant/manage/view_name_appid', $view_data);
			return;
		}

		$view_data['items'] = $this->Model_app->get_by_game($game_name);
		$this->render('accountant/manage/view_name_appid', $view_data);
	}
	
	public function change_name_by_appid(){
		if(!$this->_check_user() || !$this->_check_permission()) {
			$this->_please_login();
			return;
		}
		$appid = $this->input->post('pid');
		$appname = $this->input->post('pname');
		$this->Model_admin_manage->update_name_by_appid($appid,$appname);
	}
	
	//新服添加处理（从setting处转移过来）
	public function add_server() {
		$flag = $this->_check_user();
		if(!$flag) {
			$this->_please_login();
			return;
		}

		$appid = $this->input->get_post('big_app_id');
		$appid = empty($appid)?'1009':$appid;
		$server = $this->input->get_post('server');
		$submit = $this->input->get_post('submit');
		$viewData['msg'] = $this->input->get_post('msg');
		$ret = $this->Model_app->getById($appid);
		$viewData['init_servers'] = $ret['newservers'];
		
		if(empty($submit)){
			$this->render('accountant/manage/view_add_server', $viewData);
			return ;
		}
		
		$addserverapp = get_sub_app_ids($appid);

		// 新规则和部分旧规则的大平台ID不算子平台ID
		// 但是有些算法需要直接拿大平台ID来方便计算
		if(!in_array($appid, $addserverapp))
			$addserverapp[] = $appid;

		foreach($addserverapp as $aid) {
			$ret = $this->Model_app->getById($aid);
			if(empty($ret))
				continue;

			$newservers = $ret['newservers'];
			$newservers = explode(',', $newservers);
			$servers = $ret['servers'];
			$servers = explode(',', $servers);
			if($server <= 0) {
				$pos = array_search(max($newservers), $newservers);
				$server = $newservers[$pos]+1;
			}

			if(!in_array($server, $newservers)) {
				$newservers[] = $server;
				$servers[] = $server;
				$data = array();
				$data['appid'] = $aid;
				$data['servers'] = $servers;
				$data['newservers'] = $newservers;
				$ret = $this->Model_app->update($data);
			}
		}

		$viewData['msg'] = $ret ? 'Add Server Success' : 'Add Server Failure';
		$this->layout_data['server_string'] = $newservers;
		redirect("accountant/manage/add_server?msg=$viewData[msg]&big_app_id=$appid");
	}
	
	//删除区服
	public function del_server(){
		$flag = $this->_check_user();
		if(!$this->_check_user() || !$this->_check_permission()) {
			$this->_please_login();
			return;
		}
		
		$appid = $this->input->get_post('big_app_id');
		$appid = empty($appid)? '1009' : $appid;
		$zid_selected = $this->input->get_post('zid_selected');
		$submit = $this->input->get_post('submit');
		$view_data['msg'] = $this->input->get_post('msg');
		$view_data['init_servers'] = $this->Model_app->get_online_servers($appid);

		if(empty($submit) or empty($zid_selected)){
			$this->render('accountant/manage/view_del_server', $view_data);
			return ;
		}
		
		foreach($zid_selected as $zid){
			$key = array_search($zid,$view_data['init_servers']);
			unset($view_data['init_servers'][$key]);
		}
		
		$all_ids = MY_App::get_sub_app_ids($appid);
		if(!in_array($appid, $all_ids))
			$all_ids[] = $appid;
		
		foreach($all_ids as $sub_id) {
			$data['appid'] = $sub_id;
			$data['servers'] = $view_data['init_servers'];
			$data['newservers'] = $view_data['init_servers'];
			$ret = $this->Model_app->update($data);
		}

		$view_data['msg'] = $ret ? '1' : '0';
		redirect("accountant/manage/del_server?msg=$view_data[msg]&big_app_id=$appid");
	}
	
	public function get_new_servers($big_app_id){
		$ret = $this->Model_app->getById($big_app_id);
		$html_string = $ret['newservers'];
		echo $html_string;
	}
	
	//ajax动态获取appid对应的newserver
	public function ajax_get_new_server($big_app_id){
		$ret = $this->Model_app->getById($big_app_id);
		if(!empty($ret)){
			$newservers = $ret['newservers'];
			$response = "已开服 : ".$newservers;
		}else{
			$response = "big_app_id : wrong";	
		}
			echo $response;
	}
}