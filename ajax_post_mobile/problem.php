<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Problem extends MY_Controller {

	public function __construct() {
		parent::__construct();

		$this->layout_data['title'] = 'Problem Report';
		$this->layout = 'layout/classic';
		$this->layout_data['sidebar_file'] = 'cs';
		
		$this->load->library('form_validation');
		$this->load->library('session');
		
		$this->load->model('model_problem');
		$this->load->model('model_auth');
		$this->load->model('model_reply');
		$this->config->load('blacklist_keys');
		
		$this->load->helper('common');
	}

	public function problem_question_list(){
		
		$this->_problem_list(1);
	}
	
	public function problem_account_list(){
		$this->_problem_list(2);
	}

	public function problem_advice_list(){
		$this->_problem_list(3);
	}
	
	function _problem_list($type){
		if(!$this->_check_user()){
			return $this->_please_login();
		}
		
		$problem_state = $this->input->post('problem_state');
		$date_time = $this->input->post('date');
		if($problem_state == 'all' || $problem_state == ''){
			$is_read = '';
		} else {
			$is_read = $problem_state;
		}
		if(empty($date_time)){
			$start_time = strtotime(date('Y-m-d').' 00:00:00');
			$end_time = strtotime(date('Y-m-d').' 23:59:59');
		}else{
			$start_time = strtotime($date_time.' 00:00:00');
			$end_time = strtotime($date_time.' 23:59:59');
		}
		
		if($type == 1){
			$url = 'problem_question_list';
		}elseif ($type == 2) {
			$url = 'problem_account_list';
		}elseif ($type == 3) {
			$url = 'problem_advice_list';
		}
		
		$items = $this->model_problem->get_type_problem($type, $is_read, $start_time, $end_time);
		foreach ($items as &$item){
			if($item['replay_uid'] != 0){
				$user = $this->model_auth->get_user_by_uid($item['replay_uid']);
				$item['replay_uid'] = $user['username'];
			}else{
				$item['replay_uid'] = '暂无';
			}
		}
		unset($item);
		
		$view_data['problem_states'] = Common::get_problem_states();
		$view_data['items'] = $items;
		$view_data['url'] = $url;
		
		$this->render('service/problem/view_problem_list', $view_data);
	}
	
	public function problem_view(){
		if(!$this->_check_user()){
			return $this->_please_login();
		}
		$id = $this->input->get('id');
		$user = $this->session->all_userdata();
		$problem = $this->model_problem->get_problem_view_by_id($id);
		$reply = $this->model_reply->get_problem_reply_by_id($id);
		if(empty($reply)){
			$reply = array('id' => '', 'problem_id' => '', 'reply_title' => '', 'reply_content' => '', 'reply_uid' => '', 'addtime' => '', 'memo' => '');
		}
		$player_info = '';
		
		$view_data['reply'] = $reply;
		$view_data['player_info'] = $player_info;
		$view_data['problem'] = $problem;
		$view_data['user'] = $user;
		$view_data['problem_states'] = Common::get_problem_states();
		$this->render('service/problem/view_problem_view', $view_data);
	}
	
	public function updata_problem_reply(){
		if(!$this->_check_user()){
			return $this->_please_login();
		}
		$problem_id = $this->input->post('id');
		$reply_uid = $this->input->post('reply_uid');
		$reply_title = $this->input->post('reply_title');
		$reply_content = $this->input->post('reply_content');
		$problem_state = $this->input->post('problem_state');
		if(!empty($reply_title) || !empty($reply_content)){
			$is_reply = 1;
		}
		if($problem_state == 'all'){
			$problem_state = 0;
		}
		$problem_data = array('is_read' => $problem_state, 'is_reply' => $is_reply, 'id' =>$problem_id, 'replay_uid' => $reply_uid );
		$state = $this->model_problem->update($problem_data);
		
		$data = array('problem_id' => $problem_id, 'reply_title' => $reply_title, 'reply_content' => $reply_content, 'reply_uid' => $reply_uid);
		
		$ret = $this->model_reply->update_problem_reply($data);
		if($ret == true || $state == true){
			$view_data['msg'] = '回复成功！！！！';
		}  else {
			$view_data['msg'] = '回复失败！！！';
		}
		$this->render('login/view_msg', $view_data);
	}
	
	public function view_support($user_id, $user_name, $zid){
		$view_data['user_id'] = $user_id;
		$view_data['user_name'] = $user_name;
		$view_data['zid'] = $zid;

		$this->load->view('login/view_support', $view_data);
	}
	
	public function handle_support(){
		$item['title'] = $this->input->post('title');
		$item['content'] = $this->input->post('content');
		
		$response = $this->check_blacklist($item['title'].$item['content']);
		if(!empty($response)){
			echo $response;
			exit;
		}
		
		$item['user_id'] = $this->input->post('user_id');
		$item['zid'] = $this->input->post('zid');
		$item['type'] = $this->input->post('radio');
		
		//TODO向数据库中写入数据（以下为测试）
		$item['num'] = "H000005";
		$item['addtime'] = time();
		$item['is_read'] = 0;
		$this->model_problem->insert($item);
	}
	
	public function check_blacklist($str){
		$blacklist_keys = $this->config->item('blacklist_keys');
		if(empty($blacklist_keys)){
			return NULL;
		}
		$blacklist = "/".implode("|",$blacklist_keys)."/i";
		if(preg_match($blacklist, $str, $matches)){
			return $matches[0];
		} else {
			return NULL;
		}
	}
}
