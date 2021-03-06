<div class="container" style="margin-top:-15px;">	
	<div class="row">		
		<ol class="breadcrumb" style="background:white;">
	        	<li><a href="/">Home</a></li>	        	
	        	<li class="akacolor">Notice</li>   
	    </ol>
		<?php
		if($this->session->userdata('classify') == 0){
		?>
			<div>
				<h3 class="text-center">Notices</h3>
				<a href="/notice/write" class="btn btn-default btn-sm pull-right" id="write" style="margin-right:15px; border-color:#6799FF; color:#6799FF; margin-right:15px;">&nbsp;<span class="glyphicon glyphicon-pencil"></span>&nbsp;&nbsp;Write&nbsp;&nbsp;</a>
			</div>
		<?php } ?>
	</div>
	<br>	
		<table class="table table-hover">
	  	<thead>
			<tr>
				<th class="text-center">No.</th>
				<th class="text-center">Title</th>								
				<th class="text-center">Date</th>								
			</tr>
		</thead>
		
		<tbody>			
			<?php
			$num = 1;
			foreach ($list as $value) {
				$title = $value->title;
				$cont = $value->contents;
				$date = $value->date;
				$id = $value->id;
			?>
			<tr>
			<td class="text-center"><?=$num;?></td>
			<td><a href="/notice/contents/<?=$id;?>"><?=$title?></a></td>
			<td class="text-center"><?=substr($date,0,-3)?></td>
			</tr>
			<?php $num++; } ?>		
		</tbody>
		</table>

		<?php echo form_open_multipart('http://54.248.103.31/cate/do_upload');?>

		<input type="file" name="userfile" size="20" />
		<input type="hidden" name="email" value="<?=$this->session->userdata('email');?>" />
		<input type="hidden" name="current_url" value="<?=current_url();?>" />		
		<br />
		<input type="submit" value="upload" />		
		</form>
	
</div>
<script>

$("a#write").mouseover(function(){
  $(this).css("border-color","#4374D9");
  $(this).css("color","#4374D9");
  $(this).css("background","white");
});

$("a#write").mouseout(function(){
  $(this).css("border-color","#6799FF");
  $(this).css("color","#6799FF");
  $(this).css("background","white");
});
</script>
