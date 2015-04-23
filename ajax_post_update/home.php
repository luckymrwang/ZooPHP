<?php
class Home extends MY_Controller {
	
	public function __construct() {
        parent::__construct();
        $this->load->model('/as/model_home');
		$this->load->model('/as/model_detail');
    }
	
	//考勤总览
	public function index(){
		$param['cal_select'] = $this->input->get_post('myselect');
		if (empty($param['cal_select'])){
			$last_cal = $this->model_home->get_last_calendar(date('Y-m'));
			$param['info'] = $this->model_home->get_attendance($param['cal_select'] = $last_cal['date']);
		} else {
			$param['info'] = $this->model_home->get_attendance($param['cal_select']);
		}
		$param['calendar'] = $this->model_home->get_calendar($table = "attendance");
		$this->load->view('public/header',array('act_item' => 'home','title' => '考勤数据'));
		$this->load->view('as/index',$param);
		$this->load->view('public/footer');
	}
	
	//考勤明细展示
	public function detail(){
		$param['cal_select'] = $this->input->get_post('myselect');
		if (empty($param['cal_select']) or $param['cal_select'] == "All"){
			 $last_cal = $this->model_home->get_last_calendar(date('Y-m'));
			 $param['cal_select'] = $last_cal['date'];
		}
		$time = strtotime($param['cal_select']);
		$param['tag'] = date('t',$time);
		$param['calendar'] = $this->model_home->get_calendar($table = "attendance");
		$param['info'] = $this->detail_gather($param);

		$this->load->view('public/header',array('act_item' => 'detail','title' => '考勤详细'));
		$this->load->view('as/detail',$param);
		$this->load->view('public/footer');
	}
	
	//详细信息按输出格式总括
	public function detail_gather($param){
		$staffs = $this->model_home->get_all_staffs();
		$i = 0;
		foreach($staffs as $staff){
			$res = $this->model_home->get_detail($param['cal_select'],$staff['enno']);
			$res_sum = $this->model_home->get_detail_sum($param['cal_select'],$staff['enno']);
			$info[$i++] = $this->get_be_late($staff,$res,$res_sum,$param['tag']);
			$info[$i++] = $this->get_sick_leave($staff['enno'],$res,$res_sum,$param['tag']);
			$info[$i++] = $this->get_unpaid_leave($staff['enno'],$res,$res_sum,$param['tag']);
			$info[$i++] = $this->get_absent($staff['enno'],$res,$res_sum,$param['tag']);
		}
		return $info;
	}

	//获取展示中的迟到数据
	public function get_be_late($staff,$res,$res_sum,$tag){
		$i = 3;
		$info['enno'] = $staff['enno'];
		$info['type'] = "be_late";
		
		$info[0] = $staff['enno'];
		$info[1] = $staff['name'];
		$info[2] = "迟到";
		$info[3] = $res_sum['be_late_sum'];
		foreach($res as $arr){
			for($j=1; $j<=$tag; $j++){
				if (substr($arr['date'],-2) == $j && $arr['be_late'] == 1){
					$info[$i+$j] = "X";
				}
			}
		}
		for($j=4; $j<=$tag+4; $j++){
			if(!isset($info[$j])){
				$info[$j] = "";
			}
		}
		return $info;
	}
	
	//获取展示中的病假数据
	public function get_sick_leave($enno,$res,$res_sum,$tag){
		$i = 3;
		$info['enno'] = $enno;
		$info['type'] = "sick_leave";
		
		$info[0] = "";
		$info[1] = "";
		$info[2] = "病假";
		$info[3] = $res_sum['sick_leave_sum'];
		foreach($res as $arr){
			for($j=1; $j<=$tag; $j++){
				if (substr($arr['date'],-2) == $j){
					if ($arr['sick_leave'] == 1){
						$info[$i+$j] = "全天";
					} elseif ($arr['sick_leave'] == 0.5){
						$info[$i+$j] = "半天";
					}
				}
			}
		}
		for($j=4; $j<=$tag+4; $j++){
			if(!isset($info[$j])){
				$info[$j] = "";
			}
		}
		return $info;
	}
	
	//获取展示中的事假数据
	public function get_unpaid_leave($enno,$res,$res_sum,$tag){
		$i = 3;
		$info['enno'] = $enno;
		$info['type'] = "unpaid_leave";
		
		$info[0] = "";
		$info[1] = "";
		$info[2] = "事假";
		$info[3] = $res_sum['unpaid_leave_sum'];
		foreach($res as $arr){
			for($j=1; $j<=$tag; $j++){
				if (substr($arr['date'],-2) == $j){
					if ($arr['unpaid_leave'] == 1){
						$info[$i+$j] = "全天";
					} elseif ($arr['unpaid_leave'] == 0.5){
						$info[$i+$j] = "半天";
					}
				}
			}
		}
		for($j=4; $j<=$tag+4; $j++){
			if(!isset($info[$j])){
				$info[$j] = "";
			}
		}
		return $info;
	}
	
	//获取展示中的未打卡数据
	public function get_absent($enno,$res,$res_sum,$tag){
		$i = 3;
		$info['enno'] = $enno;
		$info['type'] = "unrecord";
		
		$info[0] = "";
		$info[1] = "";
		$info[2] = "未打卡";
		$info[3] = $res_sum['unrecord_sum'];
		foreach($res as $arr){
			for($j=1; $j<=$tag; $j++){
				if (substr($arr['date'],-2) == $j && $arr['unrecord'] != 0){
					$info[$i+$j] = $arr['unrecord'];
				}
			}
		}
		for($j=4; $j<=$tag+4; $j++){
			if(!isset($info[$j])){
				$info[$j] = "";
			}
		}
		return $info;
	}

	//考勤管理
	public function manage(){
		$param['cal_select'] = $this->input->get_post('myselect');
		if (empty($param['cal_select'])){
			 $last_cal = $this->model_home->get_last_calendar(date('Y-m'));
			 $param['cal_select'] = $last_cal['date'];
		}
		$param['leav_select'] = $this->input->get_post('l_select');
		if (empty($param['leav_select'])){
			 $last_leav = $this->model_home->get_last_leave();
			 $param['leav_select'] = substr($last_leav['date'],0,7);
		}
		$param['infos'] = $this->model_home->get_all_leave($param['leav_select']);
		$param['l_calendar'] = $this->model_home->get_leave_calendar();
		$param['time'] = $this->model_home->get_time_config();
		$param['staffs'] = $this->model_home->get_all_staffs();
		$param['calendar'] = $this->model_home->get_calendar($table = "a_data");
		$param['addtime'] = date('Y-m-d');
		$this->load->view('public/header',array('act_item' => 'manage'));
		$this->load->view('as/manage',$param);
		$this->load->view('public/footer');
	}
	
	//员工信息
	public function show_staffs(){
		$param['staffs'] = $this->model_home->get_staffs();
		$this->load->view('public/header',array('act_item' => 'show_staffs'));
		$this->load->view('as/show_staffs',$param);
		$this->load->view('public/footer');
	}
	
	public function delete_staff($id){
		$this->model_home->delete_staff($id);
		redirect('as/home/show_staffs');
	}
	
	public function delete_detail($id){
		$this->model_home->delete_detail($id);
		redirect('as/home/manage');
	}
	
	//更新员工信息
	public function ajax_update_staff(){
		$item['enno'] = $this->input->get_post('ajax_id');
		$item['name'] = $this->input->get_post('ajax_txt');
		$this->model_home->update_staff($item);
	}
	
	//通过ajax更新attendance
	public function ajax_update_attendance(){
		$id = $this->input->get_post('ajax_id');
		$pid = $this->input->get_post('ajax_pid');
		$txt = $this->input->get_post('ajax_txt');
		
		$info = $this->model_home->get_attendance_by_id($id);
		if($pid == 'work_extra_small'){
			if($info['be_late'] > $txt){
				$item['offset_late'] = $info['be_late'] - $txt;
			} else {
				$item['offset_late'] = 0;
			}
		}
		
		if($pid == 'be_late'){
			if($txt > $info['work_extra_small']){
				$item['offset_late'] = $txt - $info['work_extra_small'];
			} else {
				$item['offset_late'] = 0;
			}
		}
		
		$big_weekend = array("work_extra_big" => "", "work_weekend" => "");
		if(array_key_exists($pid, $big_weekend)){
			
			unset($big_weekend[$pid]);
			foreach($big_weekend as $key => $val){
				$work_sum = number_format(($txt + $info[$key])/2,1);
			}
			
			if ($info['ask_leave'] > number_format(($work_sum + $info['work_extra_sum']),1)){
				$item['offset_leave'] = number_format($info['ask_leave'] - $work_sum - $info['work_extra_sum'],1);
				$item['offset_sum'] = 0;
			} else {
				$item['offset_leave'] = 0;
				$item['offset_sum'] = number_format($work_sum + $info['work_extra_sum'] - $info['ask_leave'],1);
			}
		}
		
		if($pid == 'ask_leave'){
			$work_sum = number_format(($info['work_extra_big'] + $info['work_weekend'])/2,1);
			
			if ($txt > number_format(($work_sum + $info['work_extra_sum']),1)){
				$item['offset_leave'] = number_format($txt - $work_sum - $info['work_extra_sum'],1);
				$item['offset_sum'] = 0;
			} else {
				$item['offset_leave'] = 0;
				$item['offset_sum'] = number_format($work_sum + $info['work_extra_sum'] - $txt,1);
			}
		}
		
//		if($pid == 'work_extra_big'){
//			$work_sum = number_format(($item['work_extra_big'] + $item['work_weekend'])/2,1);
//			if ($item['ask_leave'] > number_format(($work_sum + $item['work_extra_sum']),1)){
//				$item['offset_leave'] = number_format($item['ask_leave'] - $work_sum - $item['work_extra_sum'],1);
//			}
//			$offset_sum = number_format($work_sum + $item['work_extra_sum'] - $item['ask_leave'],1);
//			$item['offset_sum'] = ($offset_sum < 0)? 0 : $offset_sum;
//		}
		
		$item[$pid] = $txt;
		$this->model_home->update_attendance($id, $item);
		
		$info = $this->model_home->get_attendance_by_id($id);
		$html_string = "<td width='60'>".$info['enno']."</td><td width='60'>".$info['name']."</td>";
		$html_string.= "<td width='60' id='work_extra_small' uid='".$info['id']."' pid='work_extra_small' class='table_txt' >".$info['work_extra_small']."</td>";
        $html_string.= "<td width='50' id='be_late' uid='".$info['id']."' pid='be_late' class='table_txt'>".$info['be_late']."</td>";
        $html_string.= "<td width='150'>".$info['offset_late']."</td>";
        $html_string.= "<td width='60' id='work_extra_big' uid='".$info['id']."' pid='work_extra_big' class='table_txt'>".$info['work_extra_big']."</td>";
        $html_string.= "<td width='145' id='work_weekend' uid='".$info['id']."' pid='work_weekend' class='table_txt'>".$info['work_weekend']."</td>";
        $html_string.= "<td width='190'>".$info['work_extra_sum']."</td>";
        $html_string.= "<td width='75' id='ask_leave' uid='".$info['id']."' pid='ask_leave' class='table_txt'>".$info['ask_leave']."</td>";
        $html_string.= "<td width='160' id='offset_leave' uid='".$info['id']."' pid='offset_leave' class='table_txt'>".$info['offset_leave']."</td>";
        $html_string.= "<td width='205'>".$info['offset_sum']."</td>";
		$html_string.= "<td id='desc' style='width:234px;word-break:break-all;' uid='".$info['id']."' pid='desc' class='table_txt'>".$info['desc']."</td>";
		echo $html_string;
		
//		redirect('as/home/index');
	}
	
	public function ajax_update_detail(){
		$no = $this->input->get_post('ajax_id');
		$type = $this->input->get_post('ajax_type');
		$item['enno'] = $this->input->get_post('ajax_enno');
		$txt = $this->input->get_post('ajax_txt');
		$date = $this->input->get_post('ajax_date');
		
		$item['date'] = date('Y-m-d', strtotime($date."-".$no));
		$item[$type] = $txt;
		
		$this->model_detail->upsert_detail($item);

		redirect('as/home/detail');
	}

	//追加请假数据
	public function add_detail(){
		$days = $this->input->get_post('date');
		$item['enno'] = $this->input->get_post('name');
		$cate_laeve = $this->input->get_post('cate_leave');
		$se_day = $this->input->get_post('se_day');
		if ($se_day == 1){
			$item['unrecord'] = -2;
		}
		if ($cate_laeve == "unpaid_leave"){
			$item['unpaid_leave'] = $se_day;
		} else {
			$item['sick_leave'] = $se_day;
		}
		if (!empty($days)){
			foreach($days as $day){
				$item['date'] = substr($day,9,8);
				$this->model_home->insert_detail($item);
			}
		}
		$this->message_and_redirect("数据添加成功");
		redirect('as/home/manage');
	}

	//设置上班时间和日期
	public function set_time(){
		$duty_time_hour = $this->input->get_post('duty_time_hour');
		$duty_time_min = $this->input->get_post('duty_time_min');
		
		$off_time_hour = $this->input->get_post('off_time_hour');
		$off_time_min = $this->input->get_post('off_time_min');
		
		$id = $this->input->get_post('time_id');
		$item['duty_time'] = $duty_time_hour.":".$duty_time_min;
		$item['off_time'] = $off_time_hour.":".$off_time_min;
		$item['alw_time'] = $this->input->get_post('alw_time');
		
		$this->model_home->set_time($id,$item);
		
		$days = $this->input->post('days');
		if (!empty($days)){
			foreach($days as $day){
				$param['weekday'] = substr($day,9,8);
				$param['GD'] = substr($day,25,strlen($day) - 26);
				$this->model_home->insert_day($param);
			}
		}
		$this->message_and_redirect("时间设置成功");
		redirect('as/home/manage');
	}
	
//	//设置上班日期
//	public function set_days(){
//		$days = $this->input->post('days');
//		if (!empty($days)){
//			foreach($days as $day){
//				$item['weekday'] = substr($day,9,8);
//				$this->model_home->insert_day($item);
//			}
//		}
//	}
	
	//个人考勤
	public function per_info(){
		$param['cal_select'] = $this->input->get_post('myselect');
		if (empty($param['cal_select'])){
			 $last_cal = $this->model_home->get_last_calendar(date('Y-m'));
			 $param['cal_select'] = $last_cal['date'];
		}
		$param['enno'] = $this->input->get_post('enno');
		if (empty($param['enno'])){
			$param['enno'] = 1;
		}
		$param['staffs'] = $this->model_home->get_all_staffs();
		$param['calendar'] = $this->model_home->get_calendar($table = "a_data");
		$param['weekdays'] = $this->get_weekdays_GDs($param['cal_select']);
		
		$res = $this->model_home->get_staff_data($param['enno'],$param['cal_select']);
		//print_r($res);
		$param['infos'] = array();
		$param['infos'] = "\" \",";
		foreach($res as $v){
			$param['infos'] .= "\"".substr($v['datetime'],5,2).substr($v['datetime'],8,2)." ".substr($v['datetime'],-8)."<br>\",";
		}
		$param['infos'] .= "\" \"";
		//print_r($param['infos']);
		
		$this->load->view('public/header',array('act_item' => 'per_info'));
		$this->load->view('as/per_info',$param);
		$this->load->view('public/footer');
	}

	//添加员工信息
	public function add_staff(){
		$this->load->view('public/header',array('act_item' => 'show_staffs'));
		$this->load->view('as/add_staff');
		$this->load->view('public/footer');
	}
	
	public function add_staff_action(){
		$item['name'] = $this->input->get_post('name');
		$entry_time = $this->input->get_post('entry_time');
		if(!empty($entry_time)) {
			$item['entry_time'] = $entry_time;
		}
		$item['desc'] = $this->input->get_post('desc');
		
		$this->model_home->insert_staff($item);
		redirect('as/home/show_staffs');
	}
	
	//修改员工信息
	public function alter_staff($id){
		$param['staff'] = $this->model_home->get_staff_by_id($id);
		$this->load->view('public/header',array('act_item' => 'show_staffs'));
		$this->load->view('as/alter_staff',$param);
		$this->load->view('public/footer');
	}
	
	public function alter_staff_action(){
		$id = $this->input->get_post('id');
		$item['name'] = $this->input->get_post('name');
		$entry_time = $this->input->get_post('entry_time');
		if(!empty($entry_time)) {
			$item['entry_time'] = $entry_time;
		}
		$leave_time = $this->input->get_post('leave_time');
		if(!empty($leave_time)) {
			$item['leave_time'] = $leave_time;
		}
		$item['desc'] = $this->input->get_post('desc');
		
		$this->model_home->alter_staff($id,$item);
		redirect('as/home/show_staffs');
	}

	//数据导入
	public function import(){
		if (empty($_FILES['file']['tmp_name'])){
			exit;
		}
		$file_url = $_FILES['file']['tmp_name'];
		$content = file_get_contents($file_url);
		$array = explode("\r\n", $content);
		$j = 0;
		$flag = FALSE;
		for($i = 0; $i<count($array); $i++){
			$array[$i] = preg_replace('/\s(?=\s)/','',$array[$i]);
			$array[$i] = preg_replace('/[\n\r\t]/',' ',$array[$i]);
			if (empty($array[$i])){
				continue;
			}
			$split_str = explode(" ",$array[$i]);
			$item['name'] = iconv('GB2312', 'UTF-8',$split_str[3]);
			if($this->model_home->check_name($item)) {
				$error[$j++] = $item['name'];
				$flag = TRUE;
			}
		}
		if($flag) {
			$param['msg'] = array_unique($error);
			//$param['back'] = base_url('as/home/show_staffs');
			$this->load->view('public/header',array('act_item' => 'show_staffs','title' => '员工管理'));
			$this->load->view('as/msg',$param);
		} else {
			for($i = 0; $i<count($array); $i++){
				if(empty($array[$i])) {
					continue;
				}
				$split_str = explode(" ",$array[$i]);
				$item['name'] = iconv('GB2312', 'UTF-8',$split_str[3]);
				$item['enno'] = $this->model_home->get_one_staff($item['name'], "enno");
				$item['datetime'] = $split_str[4]." ".$split_str[5];
				$res = $this->model_home->import($item);
			}
			if(isset($res)) {
				$param['msg'] = "考勤数据导入成功！";
				$this->load->view('public/header',array('act_item' => 'show_staffs','title' => '员工管理'));
				$this->load->view('as/msg',$param);
			} else {
				$param['msg'] = "数据为空，请重新选择文件导入！";
				$this->load->view('public/header',array('act_item' => 'show_staffs','title' => '员工管理'));
				$this->load->view('as/msg',$param);
			}
		}
	}
	
	//数据处理
	public function handle(){
		$get_post = $this->input->get_post('myselect');
		if(!empty($get_post)){
			//判断上班日期是否已添加
			$data['weekdays'] = $this->get_all_weekdays($get_post);
			if(empty($data['weekdays'])){
				$param['msg'] = "上班日期为空，请先设置该月的上班日期！";
				$this->load->view('public/header',array('act_item' => 'manage','title' => '数据处理'));
				$this->load->view('as/msg',$param);
				return;
			}
			//判断是否已经存在该日期的数据
			$calendars = $this->model_home->get_calendar('attendance');
			if($this->_check_calendar($get_post, $calendars)){
				$param['msg'] = "该日期数据已经执行过，重新执行需要超级管理员权限！";
				$this->load->view('public/header',array('act_item' => 'manage','title' => '数据处理'));
				$this->load->view('as/msg',$param);
				return;
			} else {
				$this->_handle($get_post, $data['weekdays']);
			}
		}
		redirect('as/home/index');
	}
	
	public function _check_calendar($cal, $calendars){
		foreach($calendars as $val){
			if($cal == $val['date']){
				return TRUE;
			}
		}
		return FALSE;
	}

	public function _handle($get_post, $weekdays){
			$data['weekdays'] = $weekdays;
			//清除已存在的数据
//			$this->model_home->clear_detail($get_post);
//			$this->model_home->clear_attendance($get_post);
//			$this->model_home->clear_record($get_post);
			
			$data['dates_rd'] = $this->model_home->get_all_dates($get_post);
//			$data['weekdays'] = $this->get_all_weekdays($get_post);
//			if (empty($data['weekdays'])){
//				$data['weekdays'] = array("0" => 0);
//			}
			
			$data['staffs'] = $this->model_home->get_all_staffs();
			//处理数据更新"a_detail"表
			$this->update_detail($data);
			//处理数据更新"attendance"表
			$this->update_attendance($data,$get_post);
	}


	//获得工作日
	public function get_detail($calendar){
		$arr = $this->model_home->get_detail($calendar);//
		if (count($arr) != 0){
			foreach ($arr as $k => $sim){
				$res[$k] = $sim['date'];
			}
		}
		return $res;
	}
	
	//获得详细信息中的date
	public function get_all_weekdays($get_post){
		$start_time = $get_post."-01";
		$end_time = $get_post."-31";
		$all_weekdays = $this->model_home->get_all_weekdays($start_time,$end_time);//
		if (count($all_weekdays) != 0){
			foreach ($all_weekdays as $k => $simple_weekday){
				$weekdays[$k] = $simple_weekday['weekday'];
			}
		return $weekdays;
		}
	}
	
	//获得工作日和GDs
	public function get_weekdays_GDs($get_post){
		$start_time = $get_post."-01";
		$end_time = $get_post."-31";
		$res = $this->model_home->get_all_weekdays($start_time,$end_time);//
		$result = array();
		foreach ($res as $k => $v){
			$result[$k] = $v['GD'];
		}
		return $result;
	}
	
	//处理数据更新"a_detail"表
	public function update_detail($data){
		foreach($data['staffs'] as $staff){
				$item['enno'] = $staff['enno'];
			foreach($data['dates_rd'] as $date){
				$item['date'] = $date['date'];
				if (in_array($item['date'],$data['weekdays'])){
					$this->week_fetch_record($item);
				} else {
					$this->weekend_fetch_record($item);
				}
			}
		}
	}
	
	//处理数据更新"attendance"表
	public function update_attendance($data,$get_post){
		$start_time = $get_post."-01";
		$end_time = $get_post."-31";
		foreach ($data['staffs'] as $staff){
			$item = $this->model_home->get_sum_data($staff['enno'],$start_time,$end_time);
			$item['enno'] = $staff['enno'];
			$item['date'] = $get_post;
			if ($item['be_late'] > $item['work_extra_small']){
				$item['offset_late'] = $item['be_late'] - $item['work_extra_small'];
			} else {
				$item['offset_late'] = 0;
			}
			if (substr($get_post,-2) == 1) {
				$item['work_extra_sum'] = 0;
			} else {
				$res = $this->model_home->get_extra_sum($staff['enno'],$start_time);
				if (empty($res['offset_sum']) || $res['offset_sum'] < 0){
					$item['work_extra_sum'] = 0;
				} else {
					$item['work_extra_sum'] = $res['offset_sum'];
				}
			}
			
			$work_sum = number_format(($item['work_extra_big'] + $item['work_weekend'])/2,1);
			if ($item['ask_leave'] > number_format(($work_sum + $item['work_extra_sum']),1)){
				$item['offset_leave'] = number_format($item['ask_leave'] - $work_sum - $item['work_extra_sum'],1);
				$item['offset_sum'] = 0;
			} else {
				$item['offset_leave'] = 0;
				$item['offset_sum'] = number_format($work_sum + $item['work_extra_sum'] - $item['ask_leave'],1);
			}
			$this->model_home->upsert_attendance($item);
		}
	}

	//工作日
	public function week_fetch_record($item){
		$min_time = $this->model_home->fetch_record($tag = "MIN",$item);
		if (!empty($min_time['time'])){
			$this->on_duty($min_time['time'], $item);
			$max_time = $this->model_home->fetch_record($tag = "MAX",$item);
			$this->off_duty($max_time['time'], $item);
		} else {
			$item['unrecord'] = 2;
			$this->model_detail->upsert_detail($item);
		}
	}
	
	//判断上班_工作日
	public function on_duty($time,$item){
		$time_set = $this->model_home->get_time_config();
		$end_time = $item['date']." "."13:00:00";
		$define_time = strtotime($time_set['duty_time']) + 60*$time_set['alw_time']; //容许的迟到时间
		$define_time = $item['date']." ".date('H:i:s',$define_time);
		if (strtotime($time) <= strtotime($end_time)){
			if (strtotime($time) > strtotime($define_time)){
				$item['be_late'] = 1;
				$this->model_detail->upsert_detail($item);
			}
		} else {
			$item['unrecord'] = 1;
			$this->model_detail->upsert_detail($item);
		}
	}
	
	//判断下班_工作日
	public function off_duty($time,$item){
		$time_set = $this->model_home->get_time_config();
		$start_time = $item['date']." "."13:00:00";
		$off_time = $item['date']." ".$time_set['off_time'];
		$week_extra_small = $item['date']." "."21:00:00";
		$week_extra_big = $item['date']." "."22:55:00";
		
		if (strtotime($time) > strtotime($start_time) && strtotime($time) < strtotime($off_time)){
			$item['early_leave'] = 1;
			$this->model_detail->upsert_detail($item);
		} elseif (strtotime($time) >= strtotime($week_extra_small)){
			if (strtotime($time) >= strtotime($week_extra_big)){
				$item['work_extra_big'] = 1;
				$this->model_detail->upsert_detail($item);
			} else {
				$item['work_extra_small'] = 1;
				$this->model_detail->upsert_detail($item);
			}
		}
	}
	
	//非工作日
	public function weekend_fetch_record($item){
		$min_time = $this->model_home->fetch_record($tag = "min",$item);
		if (!empty($min_time['time'])){
			$max_time = $this->model_home->fetch_record($tag = "max",$item);
			if ($min_time == $max_time){
				$item['work_weekend'] = 1;
				$this->model_detail->upsert_detail($item);
			} elseif (!empty($max_time['time'])) {
				$data = strtotime($max_time['time']) - strtotime($min_time['time']) + 600; 
				$day_half = floor($data/14400);
				$item['work_weekend'] = $day_half;
				$this->model_detail->upsert_detail($item);
			}
		}
	}
	
	public function export_excel_overall($date){
		$this->load->library('PHPExcel');
		$this->load->library('PHPExcel/IOFactory');
		
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setTitle("export")->setDescription("none");
		$objPHPExcel->setActiveSheetIndex(0);
		
		$fields = array("员工号", "姓名", "小加班","迟到","当月抵扣后迟到次数","大加班","非工作日加班半天","截止上月全年累计加班天数","请假天数","当月抵扣后请假天数","本月抵扣后全年累计加班天数","备注");
		$col = 0;
		foreach ($fields as $field) {
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col++, 1, $field);
		}
		
		$row = 2;
		$info = $this->model_home->get_attendance($date);
		foreach($info as $inf){
			$col = 0;
			foreach($inf as $key => $val){
				if($key == 'id')
					continue;
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col++, $row, $val);
			}
			$row++;
		}
		
		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->setTitle('考勤总览');
		$objWriter = IOFactory::createWriter($objPHPExcel, 'Excel5');
		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.'考勤总览_'.$date.'.xls"');
		header('Cache-Control: max-age=0');
		$objWriter->save('php://output');
	}
	
	public function export_excel_detail($date){
		$this->load->library('PHPExcel');
		$this->load->library('PHPExcel/IOFactory');
		
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setTitle("export")->setDescription("none");
		$objPHPExcel->setActiveSheetIndex(0);
		
		$param['cal_select'] = $date; 
		$time = strtotime($param['cal_select']);
		$param['tag'] = date('t',$time);
		$fields = array("员工号", "姓名", "类别","合计");
		for($i = 1; $i <= $param['tag']; $i++){
			$fields[] = $i;
		}
		$col = 0;
		foreach ($fields as $field) {
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col++, 1, $field);
		}
		
		$row = 2;
		$info = $this->detail_gather($param);
		foreach($info as $inf){
			$col = 0;
			unset($inf['enno']);
			unset($inf['type']);
			ksort($inf);
			foreach($inf as $val){
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col++, $row, $val);
			}
			$row++;
		}
		
		for($i = 2; $i <= count($info); $i += 4){
			$A = "A".$i.":A".($i+3);
			$objPHPExcel->getActiveSheet()->mergeCells($A);
			$objPHPExcel->getActiveSheet()->getStyle("A".$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$B = "B".$i.":B".($i+3);
			$objPHPExcel->getActiveSheet()->mergeCells($B);
			$objPHPExcel->getActiveSheet()->getStyle("B".$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		}
		
		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->setTitle('考勤详细');
		$objWriter = IOFactory::createWriter($objPHPExcel, 'Excel5');
		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.'考勤详细_'.$date.'.xls"');
		header('Cache-Control: max-age=0');
		$objWriter->save('php://output');
	}
	
}