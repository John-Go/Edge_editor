<?php
class Stat extends CI_Model{

   function get_musedata_stat($gubun) {
      switch ($gubun) {
         case 'daily':  $d_format = '%Y-%m-%d'; break;
         case 'monthly':  $d_format = '%Y-%m'; break;
         case 'yearly':  $d_format = '%Y'; break;
         default : $d_format = '%Y-%m';
      }

      $query = "SELECT date_format(created, '$d_format') AS regdate, 
            sum(data1) AS essay_count, sum(data2) AS w_count, sum(data3) AS s_count 
            FROM `stat_daily` 
            WHERE service = 'musedata' 
            GROUP BY regdate 
            ORDER BY regdate asc ";
            log_message('error', '[debug] sql : '. $query);


      $source_data = $this->db->query($query);

      $stat_list = array();

      foreach ( $source_data->result() as $row) {
         $stat_info = array();
         $stat_info['date'] = $row->regdate;
         $stat_info['essay_count'] = $row->essay_count;
         $stat_info['word_count'] = $row->w_count;
         $stat_info['sentence_count'] = $row->s_count;

         $stat_list[] = $stat_info;
          

      } // foreach end.

   	return $stat_list;
   }

   function get_pictodata_stat($gubun) {
      switch ($gubun) {
         case 'daily':  $d_format = '%Y-%m-%d'; break;
         case 'monthly':  $d_format = '%Y-%m'; break;
         case 'yearly':  $d_format = '%Y'; break;
         default : $d_format = '%Y-%m';
      }

      $query = "SELECT date_format(created, '$d_format') AS regdate, 
            sum(data1) AS essay_count, sum(data2) AS s_count
            FROM `stat_daily` 
            WHERE service = 'picto' 
            GROUP BY regdate 
            ORDER BY regdate asc ";
         log_message('error', '[debug] sql : '. $query);

      $source_data = $this->db->query($query);

      $stat_list = array();

      foreach ( $source_data->result() as $row) {
         $stat_info = array();
         $stat_info['date'] = $row->regdate;
         $stat_info['essay_count'] = $row->essay_count;
         $stat_info['sentence_count'] = $row->s_count;

         $stat_list[] = $stat_info;
          

      } // foreach end.

      return $stat_list;
   }

   function get_speakingdata_stat($gubun) {
      switch ($gubun) {
         case 'daily':  $d_format = '%Y-%m-%d'; break;
         case 'monthly':  $d_format = '%Y-%m'; break;
         case 'yearly':  $d_format = '%Y'; break;
         default : $d_format = '%Y-%m';
      }

      $query = "SELECT date_format(created, '$d_format') AS regdate, 
            sum(data1) AS essay_count, sum(data2) AS sum_audio_duration, sum(data3) AS s_count 
            FROM `stat_daily` 
            WHERE service = 'speaking' 
            GROUP BY regdate 
            ORDER BY regdate asc ";
         log_message('error', '[debug] sql : '. $query);

      $source_data = $this->db->query($query);

      $stat_list = array();

      foreach ( $source_data->result() as $row) {
         $stat_info = array();
         $stat_info['date'] = $row->regdate;
         $stat_info['essay_count'] = $row->essay_count;
         $stat_info['sentence_count'] = $row->s_count;
         $stat_info['audio_duration'] = $row->sum_audio_duration;

         $stat_list[] = $stat_info;
          

      } // foreach end.

      return $stat_list;
   }



   function get_musedata_stat2($gubun) {
      switch ($gubun) {
         case 'daily':  $d_format = '%Y-%m-%d'; break;
         case 'monthly':  $d_format = '%Y-%m'; break;
         case 'yearly':  $d_format = '%Y'; break;
         default : $d_format = '%Y-%m';
      }

      //$query = "SELECT * FROM adjust_data WHERE pj_id = $source_pj_id AND submit = 1 AND error = 'N' and ex_editing != ''";
      $query = "SELECT date_format(sub_date, '$d_format') AS regdate, 
            count(*) AS essay_count, sum(word_count) AS w_count, sum(sentence_count) AS s_count 
            FROM `adjust_data` 
            WHERE pj_active = 0 and submit = 1 
            GROUP BY regdate 
            ORDER BY regdate asc ";

      $source_data = $this->db->query($query);

      $stat_list = array();

      foreach ( $source_data->result() as $row) {
         $stat_info = array();
         $stat_info['date'] = $row->regdate;
         $stat_info['essay_count'] = $row->essay_count;
         $stat_info['word_count'] = $row->w_count;
         $stat_info['sentence_count'] = $row->s_count;

         $stat_list[] = $stat_info;

      } // foreach end.

      return $stat_list;
   }


   function make_daily_stat($service) {
      switch ($service) {
         case 'musedata':  
            $query = "SELECT date_format(sub_date, '%Y-%m-%d') AS regdate, 
               count(*) AS essay_count, sum(word_count) AS w_count, sum(sentence_count) AS s_count 
               FROM `adjust_data` 
               WHERE pj_active = 0 and submit = 1 
               GROUP BY regdate 
               ORDER BY regdate asc "; 
            break;
         case 'picto':  
            $query = "SELECT date_format(created, '%Y-%m-%d') AS regdate, 
               count(*) AS essay_count, sum(sentence_count) AS s_count 
               FROM `picto_data` 
               GROUP BY regdate 
               ORDER BY regdate asc "; 
            break;
         case 'speaking':  
            $query = "SELECT date_format(created, '%Y-%m-%d') AS regdate, 
               count(*) AS essay_count, sum(audio_duration) AS sum_audio_duration, sum(sentence_count) AS s_count 
               FROM `speaking_data` 
               GROUP BY regdate 
               ORDER BY regdate asc "; 
            break;
         default : return false;
      }

      $del = $this->db->query("DELETE FROM stat_daily WHERE service = '$service'");

      $source_data = $this->db->query($query);

      $count = 0;
      foreach ( $source_data->result() as $row) {
         
         $created = $row->regdate;

         switch ($service) {
            case 'musedata':  
               $data1 = $row->essay_count;
               $data2 = $row->w_count;
               $data3 = $row->s_count;
               $data4 = 0;
               $data5 = 0;
               break;
            case 'picto':  
               $data1 = $row->essay_count;
               $data2 = $row->s_count;
               $data3 = 0;
               $data4 = 0;
               $data5 = 0;
               break;
            case 'speaking':  
               $data1 = $row->essay_count;
               $data2 = $row->sum_audio_duration;
               $data3 = $row->s_count;
               $data4 = 0;
               $data5 = 0;
               break;
            default : return false;
         }

         $ins = $this->db->query("INSERT INTO stat_daily (service, data1, data2, data3, data4, data5, created) 
                                    VALUES('$service',$data1,$data2,$data3,$data4,$data5,'$created')");

         if(!$ins){
            return false;
         }
         $count++;
      } // foreach end.

      return $count;
   }

}
?>