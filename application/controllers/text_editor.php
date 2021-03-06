<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//require_once 'musedata/project.php';
require_once 'errorchk.php';

class Text_editor extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->database();
		$this->load->model('all_list');
		$this->load->helper('url');		
	}

	function get_templet_ele($type,$kind){
		return $this->all_list->get_templet_ele($type,$kind);
	}

	function score_pattern_replace($data_obj){
		$pattern = array('({)','(})','(")');
        $replace = array('','','');
        return preg_replace($pattern, $replace, $data_obj);			
	}

	function get_datas($cate,$essay_id,$type,$pj_id){
		$data['cate'] = 'musedata';
		$this->load->view('head',$data);
		$usr_id = $this->session->userdata('id');
		
		$datas = $this->all_list->get_one_essay($cate,$essay_id);	
		
		$pjname = $this->all_list->getproject_name($pj_id);				
		$scoring = $datas->scoring;
		$kind = $datas->kind;
		$score2 = $datas->score2;		

		$data['tag_templet'] = $this->all_list->get_tag($kind);
		$data['score_templet'] = $this->all_list->get_scores_temp($kind);		

		$data['templet'] = $this->get_templet_ele($type,$kind);
		$score1 = $this->score_pattern_replace($scoring);			
		$data['score1'] = $score1;	

		$score2 = $this->score_pattern_replace($score2);
		$data['score2'] = $score2;	

		$data['kind'] = $kind;
		$data['time'] = $datas->time;
		$data['title'] = str_replace('"', '&quot', $datas->prompt);		
		
		// <p>  </p> 간혹 테스트하다가 태그로 감싸진 것이 있다. 그럴경우 detection 표시가 안된다!
		$editing = str_replace('<p>', '', trim($datas->editing));
		$editing = str_replace('</p>', '', $editing);
		$start_doubble_quotationConfirm = substr($editing,0,1);
		$end_doubble_quotationConfirm = substr($editing,-1);
		if($start_doubble_quotationConfirm == '"'){
			$editing = substr($editing, 1);
		}

		if($end_doubble_quotationConfirm == '"'){
			$editing = substr($editing, 0,-1);
		}
		$editing = str_replace('"', '&quot',$editing); 

		$data['edit_writing'] = preg_replace("/[\n\r]/","<br>",$editing);

		$convert = str_replace('"', '&quot',$datas->raw_txt); 
		$convert = str_replace('’', "'",$convert);
		$convert = str_replace('“', '"',$convert);
		$convert = str_replace('”', '"',$convert);
		$data['raw_writing'] = $convert;
		$data['re_raw_writing'] = preg_replace("/[\n\r]/","<br>", $convert);
		$data['discuss'] = $datas->discuss;
		$data['writing'] = '';
		$data['id'] = $datas->id;
		$data['token'] = '';
		$tagging = str_replace('"','',preg_replace("/[\n\r]/","<br>", $datas->tagging));
		
		$data['tagging'] = $tagging;
		$data['critique'] = $datas->critique;
		$data['type'] = $type;
		$data['word_count'] = $datas->word_count;
		$data['error_chk'] = $datas->error;			
		$data['submit'] = $datas->submit;
		$data['draft'] = $datas->draft;
		$data['pjname'] = $pjname->name;
		$data['pj_id'] = $pj_id;	
		$data['cate'] = $cate;
		return $data;
	}

	public function todo($essay_id,$type,$pj_id){
		if($this->session->userdata('is_login')){
			$data['cate'] = 'musedata';
			$this->load->view('head',$data);
			$datas = $this->all_list->getEssay($essay_id,$type);				
			$pjname = $this->all_list->getproject_name($pj_id);				
			$score1 = $datas->scoring;
			$score2 = $datas->score2;

			$data['score1'] = $score1;
			$data['score2'] = $score2;
			$data['time'] = $datas->time;
			$data['title'] = str_replace('"', '&quot', $datas->prompt);
			$writing = preg_replace("/[\n\r]/","<br>", $datas->raw_txt);
			$data['writing'] = str_replace('"', '&quot', $writing);

			$convert = str_replace('"', '&quot',$datas->raw_txt); 
			$convert = str_replace('“', '"',$convert);
			$convert = str_replace('”', '"',$convert);
			$data['re_raw_writing'] = preg_replace("/[\n\r]/","<br>", $convert);

			$data['id'] = $datas->essay_id;
			$data['token'] = '';
			$data['kind'] = ''; // ex) toefl, essay, toeic
			$data['type'] = $datas->type;
			$data['conf'] = false;
			$data['submit'] = $datas->submit;
			$data['error_chk'] = $datas->chk;
			$classify = $this->session->userdata('classify');			
			
			$data['cate'] = 'todo';			
			$data['discuss'] = $datas->discuss;
			$data['pj_id'] = $pj_id;
			$data['pjname'] = $pjname->name;
			
			$this->load->view('/editor/editor',$data);		
			$this->load->view('footer');					
		}else{
			redirect('/');
		}
	}

	public function tbd($essay_id,$type,$pj_id){
		if($this->session->userdata('is_login')){
			$data = $this->get_datas('tbd',$essay_id,$type,$pj_id);

			$this->load->view('/editor/editor',$data);
			$this->load->view('footer');					
		}else{
			redirect('/');
		}
	}

	public function draft($essay_id,$type,$pj_id){
		if($this->session->userdata('is_login')){					
			$data = $this->get_datas('draft',$essay_id,$type,$pj_id);	

			$this->load->view('/editor/editor',$data);	
			$this->load->view('footer');		
		}else{
			redirect('/');
		}
	}	

	// Submit
	public function completed($essay_id,$type,$pj_id){
		if($this->session->userdata('is_login')){
			$data = $this->get_datas('com',$essay_id,$type,$pj_id);		
			
			$this->load->view('/editor/editor',$data);
			$this->load->view('footer');					
		}else{
			redirect('/');
		}
	}

	public function draft_save(){ //<br>을 다시 \r\n으로 // 0
		if($this->session->userdata('is_login')){			
			$data_id = $this->input->POST('data_id');						
			$time = $this->input->post('time');
			$score1 = $this->input->post('score1');
			$score2 = $this->input->post('score2');
			$editing = mysql_real_escape_string(trim($this->input->POST('editing')));
			$critique = mysql_real_escape_string(trim($this->input->POST('critique')));
			$tagging = mysql_real_escape_string(trim($this->input->POST('tagging')));			

			$result = $this->all_list->draft($data_id,$editing,$critique,$tagging,$score1,$score2,$time);
			
			$json['status'] = $result;			
		}else{
			redirect('/');
		}		
		$this->output->set_content_type('application/json')->set_output(json_encode($json));	
	}

	public function submit(){
		if($this->session->userdata('is_login')){						
			$data_id = $this->input->POST('data_id');						
			$time = $this->input->post('time');
			$score1 = $this->input->post('score1');
			$score2 = $this->input->post('score2');
			$editing = mysql_real_escape_string(trim($this->input->POST('editing')));
			$critique = mysql_real_escape_string(trim($this->input->POST('critique')));
			$tagging = mysql_real_escape_string(trim($this->input->POST('tagging')));			

			$pj_id = $this->input->POST('pj_id');			

			$result = $this->all_list->submit($data_id,$editing,$critique,$tagging,$score1,$score2,$time);
			
			if($result){	
				$errorchk_class = new Errorchk;
				$error_chk = $errorchk_class->error_chk('once',$data_id);
				$json['error_chk'] = $error_chk;	
				//$json['error_chk'] = 'a';	
				
				if($error_chk == 'true'){ 
					$json['status'] = $error_chk;
				}else{ // false 라면 DB error_count 에 1을 증가 시킨다.
					$error_count_up = $this->all_list->error_count_up($usr_id,$pj_id);
					//$json['status'] = $error_count_up;						
					$json['status'] = true;						
				}
			}else{
				$json['status'] = $result;	
			}			
		}else{
			redirect('/');
		}
		$this->output->set_content_type('application/json')->set_output(json_encode($json));	
	}

	public function editsubmit(){ //0
		if($this->session->userdata('is_login')){						
			$data_id = $this->input->POST('data_id');					
			$score1 = $this->input->post('score1');
			$score2 = $this->input->post('score2');
			$editing = mysql_real_escape_string(trim($this->input->POST('editing')));
			$critique = mysql_real_escape_string(trim($this->input->POST('critique')));
			$tagging = mysql_real_escape_string(trim($this->input->POST('tagging')));		
			
			$json['status'] = $this->all_list->editsubmit($data_id,$editing,$critique,$tagging,$score1,$score2);
			 
			$this->output->set_content_type('application/json')->set_output(json_encode($json));	
		}else{
			redirect('/');
		}
	}

	function get_all_tag(){
		$json['all_tag'] = $this->all_list->all_tag();
		$this->output->set_content_type('application/json')->set_output(json_encode($json));	
	}

/* ======================================================================================================================== 
   Admin	*/ 

   function admin_get_datas($cate,$data_id){
		if($cate == 'error' || $cate == 'tbd' || $cate == 'history' || $cate == 'admin_export'){			
			$data['cate'] = 'musedata';
			$this->load->view('head',$data);
		}elseif ($cate == 'service') {			
			$data['cate'] = 'service';			
			$this->load->view('head',$data);			
		}
		$rows = $this->all_list->get_one_essay($cate,$data_id);		

		if($rows == false){
			return false;			
		}else{												
			$scoring = $rows->scoring;			
			$score2 = $rows->score2;			
			$kind = $rows->kind; // ex) toefl, essay, toeic
			$type = $rows->type;
			$data['kind'] = $kind;
			$data['kind_name'] = $rows->kind_name; // ex) toefl, essay, toeic			 

			$data['tag_templet'] = $this->all_list->get_tag($kind);
			$data['score_templet'] = $this->all_list->get_scores_temp($kind);		

			$data['templet'] = $this->get_templet_ele($type,$kind);
			$score1 = $this->score_pattern_replace($scoring);			
			$data['score1'] = $score1;	

			$score2 = $this->score_pattern_replace($score2);
			$data['score2'] = $score2;				
			
			$data['title'] = str_replace('"', '&quot', $rows->prompt);

			$editing = $rows->editing;
			$start_doubble_quotationConfirm = substr($editing,0,1);
			$end_doubble_quotationConfirm = substr($editing,-1);
			if($start_doubble_quotationConfirm == '"'){
				$editing = substr($editing, 1);
			}

			if($end_doubble_quotationConfirm == '"'){
				$editing = substr($editing, 0,-1);
			}
			$editing = str_replace('"', '&quot',$editing); 

			$data['edit_writing'] = preg_replace("/[\n\r]/","<br>",$editing);			

			$convert = str_replace('"', '&quot',$rows->raw_txt); 
			$convert = str_replace('’', "'",$convert);
			$convert = str_replace('“', '"',$convert);
			$convert = str_replace('”', '"',$convert);
			//”
			$convert = str_replace('”', '"',$convert);
			$data['raw_writing'] = preg_replace("/[\n\r]/","<br>", $convert);			
			$data['word_count'] = $rows->word_count;
			$data['re_raw_writing'] = preg_replace("/[\n\r]/","<br>", $convert);			
			
			$tagging = str_replace('"','',preg_replace("/[\n\r]/","<br>", $rows->tagging));			
			$data['tagging'] = $tagging;
			
			$data['writing'] = '';
			$data['critique'] = str_replace('"','&quot',$rows->critique);
			$data['id'] = $rows->id;			
			
			$data['type'] = $rows->type;

			if($cate != 'service'){
				$data['pj_id'] = $rows->pj_id;	
			}
			
			$data['time'] = $rows->time;
			$data['cate'] = $cate;
			$data['data_id'] = $data_id;			
			$data['usr_id'] = $rows->usr_id;

			return $data;
		}		
	}

	public function essays($cate,$id){
		if($this->session->userdata('is_login')){
			$data = $this->admin_get_datas($cate,$id);					
			if($data){
				$this->load->view('/editor/admin_editor',$data);
			}else{
				$this->load->view('404',$data);		
			}		
			$this->load->view('footer');					
		}else{
			redirect('/');
		}
	}

	public function error($id,$type){
		if($this->session->userdata('is_login')){			
			$data = $this->admin_get_datas('error',$id);

			if($data){
				$this->load->view('/editor/admin_editor',$data);
			}else{
				$this->load->view('404',$data);		
			}					
			$this->load->view('footer');					
		}else{
			redirect('/');
		}
	}

	public function comp($id,$type){
		if($this->session->userdata('is_login')){				
			$data = $this->admin_get_datas('admin_export',$id,$type);
			
			if($data){
				$this->load->view('/editor/admin_editor',$data);
			}else{
				$this->load->view('404',$data);		
			}		
			$this->load->view('footer');			
			
		}else{
			redirect('/');
		}
	}

	public function service_comp($service_name,$id,$month,$year){
		if($this->session->userdata('is_login')){	
			$data = $this->admin_get_datas('service',$id);					

			$row = $this->all_list->get_serviceId_num($service_name);
			$service_id = $row->id;
			$data['service_id'] = $service_id;			
			$data['service_name'] = $service_name;			

			switch ($month) {
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
			$data['str_month'] = $str_month;
			$data['month'] = $month;
			$data['year'] = $year;			
			//$data['classify'] = $classify;

			if($data){
				$this->load->view('editor/admin_service_editor',$data);
			}else{
				$this->load->view('404',$data);		
			}
			$this->load->view('footer');								
		}else{
			redirect('/');
		}
	}		

	public function admin_draft_save(){ //<br>을 다시 \r\n으로 // 0
		if($this->session->userdata('is_login')){						
			$data_id = $this->input->POST('data_id');			
			$time = $this->input->post('time');
			$editing = mysql_real_escape_string(trim($this->input->POST('editing')));
			$critique = mysql_real_escape_string(trim($this->input->POST('critique')));
			$tagging = mysql_real_escape_string(trim($this->input->POST('tagging')));			
			$scoring = $this->input->POST('score1');
			$score2 = $this->input->POST('score2');	
			$result = $this->all_list->admin_tbd_draft($data_id,$editing,$critique,$tagging,$scoring,$score2,$time);

			$json['status'] = $result;			
		}else{
			redirect('/');
		}		
		$this->output->set_content_type('application/json')->set_output(json_encode($json));	
	}

	public function admin_submit(){
		if($this->session->userdata('is_login')){			
			$usr_id = $this->session->userdata('id');
			$data_id = $this->input->POST('data_id');						
			$time = $this->input->post('time');
			$editing = mysql_real_escape_string(trim($this->input->POST('editing')));
			$critique = mysql_real_escape_string(trim($this->input->POST('critique')));
			$tagging = mysql_real_escape_string(trim($this->input->POST('tagging')));			
			$scoring = $this->input->POST('score1');
			$score2 = $this->input->POST('score2');
			
			$result = $this->all_list->admin_tbd_submit($usr_id,$data_id,$editing,$critique,$tagging,$scoring,$score2,$time);

			$json['status'] = $result;
			$this->output->set_content_type('application/json')->set_output(json_encode($json));	
		}else{
			redirect('/');
		}
	}


	

	// public function alldone($id){
	// 	if($this->session->userdata('is_login')){
	// 		$data['cate'] = 'admin';
	// 		$this->load->view('head',$data);			
			
	// 		$rows = $this->all_list->alldone_essay($id);

	// 		$scoring = $rows->scoring;
	// 		$scoring = json_decode($scoring,true);
	// 		//print_r($scoring);
	// 		$data['ibc'] = $scoring['ibc'];
	// 		$data['thesis'] = $scoring['thesis'];
	// 		$data['topic'] = $scoring['topic'];
	// 		$data['coherence'] = $scoring['coherence'];
	// 		$data['transition'] = $scoring['transition'];
	// 		$data['mi'] = $scoring['mi'];
	// 		$data['si'] = $scoring['si'];
	// 		$data['style'] = $scoring['style'];
	// 		$data['usage'] = $scoring['usage'];
			
	// 		$data['title'] = str_replace('"', '', $rows->prompt);
	// 		$data['edit_writing'] = str_replace('"','',$rows->editing);
	// 		$data['raw_writing'] = $rows->raw_txt;
	// 		$data['re_raw_writing'] = '';			
	// 		$data['tagging'] = str_replace('"','',preg_replace("/[\n\r]/","<br>", $rows->tagging));
	// 		$data['writing'] = '';
	// 		$data['critique'] = $rows->critique;
	// 		$data['id'] = $rows->id;
	// 		$data['token'] = '';
	// 		$data['kind'] = ''; // ex) toefl, essay, toeic
	// 		$data['type'] = $rows->type;
	// 		$data['conf'] = false;
	// 		$data['cate'] = 'admin';
			
	// 		$this->load->view('editor',$data);		
	// 		$this->load->view('footer');					
	// 	}else{
	// 		redirect('/');
	// 	}
	// }

	// public function all_history($id){
	// 	if($this->session->userdata('is_login')){
	// 		$data['cate'] = 'admin';
	// 		$this->load->view('head',$data);			
			
	// 		$rows = $this->all_list->alldone_essay($id);

	// 		$scoring = $rows->scoring;
	// 		$scoring = json_decode($scoring,true);
	// 		//print_r($scoring);
	// 		$data['ibc'] = $scoring['ibc'];
	// 		$data['thesis'] = $scoring['thesis'];
	// 		$data['topic'] = $scoring['topic'];
	// 		$data['coherence'] = $scoring['coherence'];
	// 		$data['transition'] = $scoring['transition'];
	// 		$data['mi'] = $scoring['mi'];
	// 		$data['si'] = $scoring['si'];
	// 		$data['style'] = $scoring['style'];
	// 		$data['usage'] = $scoring['usage'];
			
	// 		$data['title'] = str_replace('"', '', $rows->prompt);
	// 		$data['edit_writing'] = $rows->editing;
	// 		$data['raw_writing'] = $rows->raw_txt;
	// 		$data['re_raw_writing'] = '';			
	// 		$data['tagging'] = preg_replace("/[\n\r]/","<br>", $rows->tagging);
	// 		$data['writing'] = '';
	// 		$data['critique'] = $rows->critique;
	// 		$data['id'] = $rows->id;
	// 		$data['token'] = '';
	// 		$data['kind'] = ''; // ex) toefl, essay, toeic
	// 		$data['type'] = $rows->type;
	// 		$data['conf'] = false;
	// 		$data['cate'] = 'admin';
			
	// 		$this->load->view('editor',$data);		
	// 		$this->load->view('footer');					
	// 	}else{
	// 		redirect('/');
	// 	}
	// }	
}
?>
