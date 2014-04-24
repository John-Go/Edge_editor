<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Errorchk extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->database();
		$this->load->model('all_list');
		$this->load->helper('url');	
		$this->load->helper('file');
		$this->load->helper(array('form', 'url'));
		$this->load->helper('download');		
		$this->load->dbutil();				
	}

	// Option 'once' == 하나의 에세이만 검사  'all' == 여러개 검사.
	public function error_chk($option,$essay_id,$type){ //0 member_list
		if($this->session->userdata('is_login')){						
			$error_array = array();			
			$done_essay = array();
			
			$essays = $this->all_list->get_essay($essay_id,$type);		

			$error_array_temp = array();
			foreach ($essays as $values) {
				$editing = $values->editing;
				$essay_id = $values->essay_id;
				$ex_editing = $values->ex_editing; //export Editing		

				/*
				스타일 케이스 삭제!
				<s style="-webkit-box-sizing: border-box; ">
				<u style="-webkit-box-sizing: border-box; color: rgb(47, 157, 39); ">
				<br style="-webkit-box-sizing: border-box; ">
				<b style="-webkit-box-sizing: border-box; font-weight: bold; color: rgb(1, 0, 255); ">
				<b style=""-webkit-box-sizing: border-box; font-weight: bold; "">
				<u style="-webkit-box-sizing: border-box; ">
				*/
				$string = str_replace('<s style="-webkit-box-sizing: border-box; ">', '<strike>',$editing);
				$string = str_replace('<u style="-webkit-box-sizing: border-box; color: rgb(47, 157, 39); ">', '<u>',$string);
				$string = str_replace('<u style="-webkit-box-sizing: border-box; ">', '<u>',$string);
				$string = str_replace('<br style="-webkit-box-sizing: border-box; ">', '<br>',$string);
				$string = str_replace('<b style="-webkit-box-sizing: border-box; font-weight: bold; color: rgb(1, 0, 255); ">', '<b>',$string);
				$string = str_replace('<b style="-webkit-box-sizing: border-box; font-weight: bold; ">', '<b>',$string);
				$string = str_replace('&nbsp;', ' ',$string);
				$string = str_replace('“', '"',$string);
				$string = str_replace('”', '"',$string); // “ ” Del				
				$string = str_replace("’", "'", $string); // ’ Del



				$patterns = array('(<s>)','(</s>)'); // <s> 태그는 <strike> 태그가 오류난 것이다! 이것을 <strike>로 돌려줘야 한다!
				$replace = array("<strike>","</strike>");
				$editing = preg_replace($patterns, $replace, $string);	// result = ["<mod>to//that</mod>", "<mod>investing money//city investments</mod>"]

				// $data = mysql_real_escape_string($editing);
				// $this->all_list->editing_update($essay_id,$data);				

				$s_tagmatch = preg_match('/<s>/',$editing);	

				if($s_tagmatch > 0){
					array_push($error_array_temp, $essay_id);					
				}else{				
					$editing = preg_replace('/<span[^>]+\>/i','',$editing); //span 테그 제거!
					$editing = preg_replace('/<font[^>]+\>/i','',$editing); //font 테그 제거!							
					$editing = str_replace('</font>', '',$editing);
					$editing = str_replace('</span>', '',$editing);

					// u 태그 안에 다른 태그가 들어가 있는지 검사 한다!					
					$u_tagconfirm = $this->getTextBetweenTags('cou','<u>',$editing); 

					foreach ($u_tagconfirm as $value) {
						preg_match_all('/<strike>/', $value, $strike_count);
						preg_match_all('/<b>/', $value, $b_count);

						if(count($strike_count[0]) > 0 || count($b_count[0]) > 0){
							array_push($error_array_temp, $essay_id);							
							break;
						}
					}				

					// <u>태그</u> 사이에 있는 값 가져오기! u태그안에 슬라이스 갯수를 파악하기 위해서.
					$content_count = $this->getTextBetweenTags('cou','u', $editing); 
					// Count End

					foreach ($content_count as $value) { // Error sentence chk
						preg_match_all('/\/\//', $value, $matche_count);						
						if(count($matche_count[0]) != 1){ // u 태그안에 슬라스가 없거나 1개 이상이면 에러!
							array_push($error_array_temp, $essay_id);
							break;
						}
					}				

					// 에디팅 한것중에 전체 슬아이스 갯수 카운트.
					$slash = preg_match_all('/\/\//', $editing, $matches); //  '//' slash count
					$match = preg_match('/\/\/\/\//', $editing); // <u>문자////문자</u> -- Error

					// <u> 태그와 // 태그 카운터가 같아야 한다! 아니면 에러!				
					if($slash != count($content_count) || $match == 1){ 
						// Error											
						array_push($error_array_temp, $essay_id);						
					}										

					// Done					
					$done_content = $this->getTextBetweenTags('conf','u', $editing); // <u>태그</u> 사이에 있는 값 가져오기!										
					
					// 태그 변형!
					foreach ($done_content as $value) {														
						$explode = explode('//', $value); 							
						//$del_mod = eregi_replace('<u>','',$explode[0]); // first word.
						$editing = str_replace('<u>'.$explode[0].'//', '<mod target = '.$explode[0].'>', $editing);							
						$editing = preg_replace('/<\/u>/','</mod>',$editing); // second word.				
					}			
					$patterns = array("(<strike>)","(</strike>)","(<b>)","(</b>)");
					$replace = array("<del>","</del>","<ins>","</ins>");
					$editing = preg_replace($patterns, $replace, $editing);	// result = ["<mod>to//that</mod>", "<mod>investing money//city investments</mod>"]
					
					// 마지막에 replace가 되지 않으면, error로 판별한다! 
					preg_match_all('/<u>/', $editing, $not_replace);
					if(count($not_replace[0]) > 0){
						array_push($error_array_temp, $essay_id);							
					}		
				} // s_tag_match if end

				//$error_array_temp 이곳에 에세이 아이디가 하나도 없으면 에러 아님!
				$error_count = count($error_array_temp); 
				if($error_count == 0){
					$data = mysql_real_escape_string($editing);						
					
					$update = $this->all_list->ex_editing_update_service($essay_id,$data,$type);	

					if($update){								
						array_push($done_essay,$essay_id);	
						if($option == 'once'){
							return $update; // true or false									
						}
					}else{
						if($option == 'once'){
							return $update; // true or false									
						}
					}																									
				}else{ // 에러 에세이 경우!
					if($option == 'once'){
							return false; // true or false
					}else{
						array_push($error_array, $essay_id);
					}
				}	
			} //foreach end.

			if($option == 'all'){
				return $done_essay;
			}
		}else{
			redirect('/');
		}
	}

	public function getTextBetweenTags($option,$tag, $html, $strict=0) { // 0
	    /*** a new dom object ***/
	    $dom = new domDocument;

	    /*** load the html into the object ***/
	    if($strict==1)
	    {
	        $dom->loadXML($html);
	    }
	    else
	    {
	        $dom->loadHTML($html);
	    }

	    /*** discard white space ***/
	    $dom->preserveWhiteSpace = false;

	    /*** the tag by its tag name ***/
	    $content = $dom->getElementsByTagname($tag);

	    /*** the array to return ***/
	    $out = array();

	    // $option == cou or conf
	    if($option == 'cou'){
	    	foreach ($content as $item)
		    {
		        /*** add node value to the out array ***/		        
	        	$value = $item->nodeValue;
	        	$out[] = $value;	
		    }

	    }elseif($option == 'conf'){ //  태그 사이에 값이 없는건 Array에 추가하지 않음
	    	foreach ($content as $item)
		    {
		        /*** add node value to the out array ***/
		        if($item->nodeValue != ''){
		        	//$value = '<u>'.$item->nodeValue.'</u>';
		        	$value = $item->nodeValue;
		        	$out[] = $value;	
		        }
		        
		    }	
	    }
	    
	    /*** return the results ***/
	    return $out;
	}		
}