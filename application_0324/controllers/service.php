<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once 'errorchk.php';
class Service extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->database();
		$this->load->model('all_list');
		$this->load->helper('url');	
		$this->load->helper('file');
		$this->load->helper(array('form', 'url'));
		$this->load->helper('download');		
		$this->load->dbutil();	
		//$this->load->library('session');			
	}

	function eng_month($int_month){
		switch ($int_month) {
			case '01' : $str_month = 'January'; break;
            case '02' : $str_month = 'February'; break;
            case '03' : $str_month = 'March'; break;
            case '04' : $str_month = 'April'; break;
            case '05' : $str_month = 'May'; break;
            case '06' : $str_month = 'June'; break;
            case '07' : $str_month = 'July'; break;
            case '08' : $str_month = 'August'; break;
            case '09' : $str_month = 'September'; break;
            case '10' : $str_month = 'October'; break;
            case '11' : $str_month = 'November'; break;
            case '12' : $str_month = 'December'; break;
		}
		return $str_month;
	}

	public function index()
	{			
		if($this->session->userdata('is_login')){
			$classify = $this->session->userdata('classify');

			if($classify == 0){ // Admin
				$data['cate'] = 'service';
				$this->load->view('head',$data);				
				
				$data['all_year'] = $this->all_list->service_all_year_data();
				$data['all_usr'] = $this->all_list->all_usr();

				$data['cate'] = 'admin';
				$this->load->view('/service_view/admin_service_index',$data);		
				$this->load->view('footer');					
				
			}else{	// Editor
				$cate['cate'] = 'service';
				$this->load->view('head',$cate);

				$usr_id = $this->session->userdata('id');				
				//$data['pjlist'] = $this->all_list->editor_pjlist($usr_id);
				$data['all_usr'] = '';
				$data['cate'] = 'service';
				$this->load->view('/service_view/index',$data);		
				$this->load->view('footer');					
			}	
		}else{
			redirect('/');
		}		
	}	

	public function get_writing()
	{
		$secret = 'isdyf3584MjAI419BPuJ5V6X3YT3rU3C';
		$email = $this->session->userdata('email');		
		$access = $this->curl->simple_post('http://ec2-54-199-4-169.ap-northeast-1.compute.amazonaws.com/editor/auth', array('secret'=>$secret,'email'=>$email));

		// $access = '{
		// 		    "status": true,
		// 		    "data": {
		// 		        "token": "~~~"
		// 		    }
		// 		}';		

		$access_status = json_decode($access, true);		
		
		if($access_status['status']){
			
			// success -> token
			$token = $access_status['data']['token'];
			
			// re_curl
			$result_data = $this->curl->simple_post('http://ec2-54-199-4-169.ap-northeast-1.compute.amazonaws.com/editor/get', array('token'=>$token));


			/** "is_24hr":"1" == true  "0" == 'false'   "is_critique":"1" == true  "0" == 'false' **/
			
			// $result_data = '{
			// 			    "status": true,
			// 			    "data": 
			// 		        {
			// 		            "id": 1,
			// 		            "kind": "essay",
			// 		            "is_24hr":"1",
			// 		            "is_critique": "0",
			// 		            "title": "Which is better for children to grow up in the countryside or in a big city.personally disagree with the former idea since children in the urban area can acquire the better educational conditions as well as improve their capability through positive competition",
			// 		            "writing": " personally disagree with the former idea since children in the urban area can acquire the better educational conditions as well as improve their capability through positive competition",					            
			// 		            "date": "2014-03-12 15:06:03"
			// 		        }						   
			// 			}';		
			
			$json['result'] = $result_data;	
			$json['access'] = $access;

		}else{ //status = false
			$json['result'] = $access;
			$json['access'] = $access;

		}		
		
		// $usr_id = $this->session->userdata('id');
		// if($classify_cate == 'pj_mem_history'){
		// 	$todo_list = $this->all_list->pj_memList($pj_id,$usr_id); // todo list 	
		// 	$list_count = $this->all_list->editor_pj_list_count($pj_id,$usr_id);
			
		// }else{
		// 	$todo_list = $this->all_list->essayList($usr_id); // todo list 	
		// 	$list_count = $this->all_list->list_count($usr_id);
		// }		

		// $todo_list = $this->all_list->essayList($usr_id); // todo list 	
		// $list_count = $this->all_list->list_count($usr_id);

		// $list_array = array();
		// $data_value = array();

		// foreach ($todo_list as $rows) {
		// 	$title = $rows->prompt;
		// 	$kind = $rows->kind;
		// 	$date = $rows->start_date;
		// 	$type_kind = $rows->type;
		// 	$essay_id = $rows->essay_id;								
		// 	$draft = $rows->draft;
		// 	$submit = $rows->submit;	
		// 	$id = $rows->id;	
		// 	unset($data_value);
		// 	$data_value = array();
		// 	array_push($data_value, $title);
		// 	array_push($data_value, $kind);
		// 	array_push($data_value, $date);
		// 	array_push($data_value, $type_kind);
		// 	array_push($data_value, $essay_id);
		// 	array_push($data_value, $draft);
		// 	array_push($data_value, $submit);
		// 	array_push($data_value, $id);

		// 	array_push($list_array, $data_value);
		// }	
		
		//$json['cate'] = $classify_cate;
		//$json['list_count'] = $list_count;
		//$json['tagging_list'] = $list_array;
		//$json['access'] = $access;

		$this->output->set_content_type('application/json')->set_output(json_encode($json));
	}	

	public function auth(){
		$token = $this->input->post('token');
		$w_id = $this->input->post('w_id');		

		$access = $this->curl->simple_post('http://ec2-54-199-4-169.ap-northeast-1.compute.amazonaws.com/editor/editing/start', array('token'=>$token,'id'=>$w_id));

		// $access = '{
		// 		    "status": true,
		// 		    "data": {
				        
		// 		    }
		// 		}';		

		$json['result'] = $access;						
		// $conform = json_decode($access,true);		
		
		// if($conform['status']){
		//	$json['result'] = $access;						
		// }else{			
		// 	$json['result'] = $access;			
		// }

		$this->output->set_content_type('application/json')->set_output(json_encode($json));
	}

	public function writing(){
		if($this->session->userdata('is_login')){
			$data['cate'] = 'service';
			$this->load->view('head',$data);

			/** service_view/index 에서 form으로 전송된값 **/
			$token = $this->input->post('token');
			$w_id = $this->input->post('w_id');
			$title = $this->input->post('title');
			$writing = $this->input->post('writing');
			$is_critique = $this->input->post('is_critique');		
			$kind = $this->input->post('kind');

			$data['title'] = str_replace('"', '', $title);
			
			$data['writing'] = $writing;			
			$data['re_raw_writing'] = preg_replace("/[\n\r]/","<br>", $writing); //Tagging 할때 쓰임! All clear
			$data['token'] = $token;
			$data['id'] = $w_id;
			$data['is_critique'] = $is_critique;
			$data['critique'] = '';
			$data['kind'] = $kind;
			$data['type'] = '';
			$data['conf'] = true;
			$data['error_chk'] = '';
			$data['submit'] = '';
			$data['discuss'] = '';
			$data['time'] = 0;
			$data['cate'] = 'writing';
			$data['pj_id'] = '';

			$this->load->view('editor',$data);		
			$this->load->view('footer');					
		}else{
			redirect('/');
		}
	}

	// public function writing_view(){
	// 	$token = $this->input->post('token');
	// 	$w_id = $this->input->post('w_id');
	// 	$title = $this->input->post('title');
	// 	$writing = $this->input->post('writing');
	// 	$critique = $this->input->post('critique');		
		
	// 	$data['cate'] = 'todo_writing';
	// 	$this->load->view('head',$data);

	// 	$data['title'] = $title;
	// 	$data['writing'] = $writing;
	// 	$data['token'] = $token;
	// 	$data['w_id'] = $w_id;
	// 	$data['critique'] = $critique;

	// 	$this->load->view('writing_view',$data);		
	// 	$this->load->view('footer');									
	// }

	public function w_submit(){
		$usr_id = $this->session->userdata('id');
		$token = $this->input->POST('token');
		$w_id = $this->input->POST('w_id');	
		$time = $this->input->POST('time');			
		$kind = $this->db->escape($this->input->POST('kind'));
		$title = $this->db->escape($this->input->POST('title'));
		$editing = $this->db->escape($this->input->POST('editing'));
		$critique = $this->db->escape($this->input->POST('critique'));
		$tagging = $this->db->escape($this->input->POST('tagging'));
		$raw_writing = $this->db->escape($this->input->post('raw_writing'));		
		$type = 'writing';
		// Scoring
		$json['ibc'] = $this->input->POST('ibc');			
		$json['thesis'] = $this->input->POST('thesis');			
		$json['topic'] = $this->input->POST('topic');			
		$json['coherence'] = $this->input->POST('coherence');			
		$json['transition'] = $this->input->POST('transition');			
		$json['mi'] = $this->input->POST('mi');			
		$json['si'] = $this->input->POST('si');			
		$json['style'] = $this->input->POST('style');			
		$json['usage'] = $this->input->POST('usage');			
		$scoring = json_encode($json);

		//local DB save		
		$query_res = $this->all_list->local_save($usr_id,$w_id,$raw_writing,$editing,$tagging,$critique,$title,$kind,$scoring,$time,$type);		
				
		if($query_res){ // True or false
			// Error chk
			$errorchk_class = new Errorchk;
			$error_chk = $errorchk_class->error_chk('once',$w_id,$type);

			$access = $this->curl->simple_post('http://ec2-54-199-4-169.ap-northeast-1.compute.amazonaws.com/editor/editing/done', array('token'=>$token, 'id'=>$w_id, 'editing'=>$this->input->POST('editing'), 'critique'=>$this->input->POST('critique')));

			// $access = '{
			// 		    "status": true,
			// 		    "data": {
					        
			// 		    }
			// 		}';		

			$conform = json_decode($access,true);		
			if($conform['status']){
				$json['result'] = true;	
				
			}else{			
				$json['result'] = 'curl';							
			}
			$json['error_chk'] = $error_chk;	
		}else{
			$json['result'] = 'localdb';
		}				
		$this->output->set_content_type('application/json')->set_output(json_encode($json));
	}

	public function get_service_yen(){
		if($this->session->userdata('is_login')){
			$yen = $this->input->post('yen');			
			$result = $this->all_list->service_month_data($yen);			
			$json['data'] = $result;			
		}else{
			redirect('/');
		}
		$this->output->set_content_type('application/json')->set_output(json_encode($json));
	}

	public function enter($month,$year){ // 0
		if($this->session->userdata('is_login')){			
			$cate['cate'] = 'service';
			$this->load->view('head',$cate);			
			$page = 1;
			$data['page'] = $page;
			$list = 20; // 한페이지에 보요질 갯수.			
			$data['list'] = $list;						

			$str_month = $this->eng_month($month); // 숫자 달을 영문으로 변경하는 함수!

			$data['str_month'] = $str_month;
			$data['month'] = $month;
			$data['year'] = $year;

			$this->load->view('/service_view/admin_enter_view',$data);		
			$this->load->view('footer');					
		}else{
			redirect('/');
		}		
	}

	public function get_service_data(){
		if($this->session->userdata('is_login')){			
			$cate = $this->input->post('cate');
			$page = $this->input->post('page');
			$month = $this->input->post('month');
			$year = $this->input->post('year');

			$page_list = 20;			
			$limit = ($page - 1) * $page_list;					

			$totalcount = $this->all_list->get_service_month_count($year,$month);
			$json['data_count'] = $totalcount->count;
			$json['data_list'] = $this->all_list->get_service_month_data($year,$month,$limit,$page_list);
			
			$json['page'] = $page;
			$json['page_list'] = $page_list;			
			$json['cate'] = $cate;											
		}else{
			redirect('/');
		}	
		$this->output->set_content_type('application/json')->set_output(json_encode($json));
	}

	public function export($month,$year){
		if($this->session->userdata('is_login')){			
			$data['cate'] = 'service';
			$this->load->view('head',$data);

			$data['str_month'] = $str_month = $this->eng_month($month);
			$data['month'] = $month;




			$this->load->view('/service_view/service_export_view',$data);		
			$this->load->view('footer');								
		}else{
			redirect('/');
		}			
	}


	// public function todo_list(){
	// 	$last_num = $this->input->post('last_num');
	// 	$pj_id = $this->input->post('pj_id');
	// 	$cate = $this->input->post('cate');		
	// 	$usr_id = $this->session->userdata('id');
		
	// 	if($cate == 'pj_mem_history'){
	// 		$todo_list = $this->all_list->editor_pj_todolist($usr_id,$pj_id,$last_num); // pj_todo list 				
	// 	}else{
	// 		$todo_list = $this->all_list->page_essayList($usr_id,$last_num); // todo list 				
	// 	}		

	// 	//$todo_list = $this->all_list->get_todolist($usr_id,$last_num); // todo list 		
	// 	$list_array = array();
	// 	$data_value = array();

	// 	foreach ($todo_list as $rows) {
	// 		$title = $rows->prompt; //0
	// 		$kind = $rows->kind; // 1
	// 		$date = $rows->start_date; // 2
	// 		$type_kind = $rows->type; // 3
	// 		$essay_id = $rows->essay_id; // 4								
	// 		$draft = $rows->draft; // 5
	// 		$submit = $rows->submit; // 6
	// 		$id = $rows->id; // 7
	// 		unset($data_value); 
	// 		$data_value = array();
	// 		array_push($data_value, $title);
	// 		array_push($data_value, $kind);
	// 		array_push($data_value, $date);
	// 		array_push($data_value, $type_kind);
	// 		array_push($data_value, $essay_id);
	// 		array_push($data_value, $draft);
	// 		array_push($data_value, $submit);
	// 		array_push($data_value, $id);

	// 		array_push($list_array, $data_value);
	// 	}
		
	// 	$json['tagging_list'] = $list_array;		
	// 	$json['count'] = count($list_array);
	// 	$json['cate'] = $cate;

	// 	$this->output->set_content_type('application/json')->set_output(json_encode($json));
	// }

}
?>