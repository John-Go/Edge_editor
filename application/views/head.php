<!DOCTYPE html>
<html>
  <head>
    <title>Administrator2.0</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content=""> 

    <link href="/public/css/bootstrap.css" rel="stylesheet" media="screen">    
    <link href="/public/css/bootstrap-select.css" rel="stylesheet" media="screen">      
    <!-- Custom styles for this template -->
    <link href="/public/css/customize.css" rel="stylesheet" media="screen">  
    <style type="text/css">
        body{padding-top: 80px;}       
    </style>  

    <link href="http://netdna.bootstrapcdn.com/font-awesome/3.0.2/css/font-awesome.css" rel="stylesheet">
    <link href="/public/wy/index.css" rel="stylesheet"> 
    <!--[if lt IE 9]>
    <script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <!--[if lt IE 9]>
    <script src="http://ie7-js.googlecode.com/svn/version/2.1(beta4)/IE9.js"></script>
    <![endif]-->

    <!--[if lt IE 8]>
    <script src="http://ie7-js.googlecode.com/svn/version/2.1(beta4)/IE8.js"></script>
    <![endif]-->

    <!--[if lt IE 7]>
    <script src="http://ie7-js.googlecode.com/svn/version/2.1(beta4)/IE7.js"></script>
    <![endif]-->
    
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>    
    <script src="/public/js/bootstrap.js"></script>       
    <script src="/public/js/bootstrap-select.js"></script>       
    <script src="/public/js/jquery.selection.js"></script>
    
    <?php    
    if($this->session->userdata('classify') == 1 && $this->session->userdata('service') == 'true'){
      if($cate != 'service'){ ?>    
    <script src="/public/js/service_chk.js"></script>
    <?php } } ?>   
    <script src="/public/js/countdown.js"></script>     
    
  </head>
  
<body>
<div class="navbar navbar-inverse navbar-fixed-top">
  <div class="container">
    <div class="navbar-header">      
      <a class="navbar-brand" href="/">Administrator</a>      
    </div>        
    
    <div class="navbar-collapse collapse">          
      <ul class="nav navbar-nav">
      <?php
      if($this->session->userdata('is_login')){              
        if($cate == 'musedata'){                  
          if($this->session->userdata('classify') == 0){
          ?>
            <li class="active"><a href="/musedata/project">Data</a></li>          
            <li id="service_chk"><a href="/service">Service</a></li>            
            <li><a href="/setting/info">Setting</a></li>
            <li><a href="/notice">Notice</a></li>            
          <?php
          }else{
            if($this->session->userdata('musedata') == 'true'){

          ?>
            <li class="active"><a href="/musedata/project">Data</a></li>
          <?php
            }
            if($this->session->userdata('service') == 'true'){
          ?>
            <li id="service_chk"><a href="/service">Service</a></li>
          <?php
            }
          ?>         
            <li><a href="/notice">Notice</a></li>            
          <?php
          }               
        }elseif($cate == 'service'){        
          if($this->session->userdata('classify') == 0){ // ADmin
          ?>
          <li><a href="/musedata/project">Data</a></li>          
          <li class="active" id="service_chk"><a href="/service">Service</a></li>              
          <li><a href="/setting/info">Setting</a></li>
          <li><a href="/notice">Notice</a></li>            
          <?php
          }else{ // Editor
            if($this->session->userdata('musedata') == 'true'){
            ?>
              <li><a href="/musedata/project">Data</a></li>
            <?php
            }
            if($this->session->userdata('service') == 'true'){
            ?>
              <li class="active" id="service_chk"><a href="/service">Service</a></li>
            <?php
            }
            ?>            
            <li><a href="/notice">Notice</a></li>                    
          <?php
          }                          
        }elseif($cate == 'setting'){        
          if($this->session->userdata('classify') == 0){ // Admin
          ?>
          <li><a href="/musedata/project">Data</a></li>          
          <li><a href="/service">Service</a></li>              
          <li class="active"><a href="/setting/info">Setting</a></li>
          <li><a href="/notice">Notice</a></li>            
          <?php
          }                          
        }else if($cate == 'notice'){ // Notice head
          if($this->session->userdata('classify') == 0){ // Admin
          ?>
            <li><a href="/musedata/project">Data</a></li>
            <li><a href="/service">Service</a></li>              
            <li><a href="/setting/info">Setting</a></li>
            <li class="active"><a href="/notice">Notice</a></li>            
          <?php
          }else{ // Editor
            if($this->session->userdata('musedata') == 'true'){
          ?>
            <li><a href="/musedata/project">Data</a></li>
          <?php
            }
            if($this->session->userdata('service') == 'true'){
          ?>
            <li id="service_chk"><a href="/service">Service</a></li>
          <?php
            }
          ?>
            <li class="active"><a href="/notice">Notice</a></li>            
          <?php
          }                                 
        }else{ // index head    
          if($this->session->userdata('classify') == 0) { // Admin
          ?>
            <li><a href="/musedata/project">Data</a></li>
            <li id="service_chk"><a href="/service">Service</a></li>            
            <li><a href="/setting/info">Setting</a></li>              
            <li><a href="/notice">Notice</a></li>  
            <!-- <li><a href="/pay/pay">Pay</a></li>           -->
          <?php
          }else{ // Editor
            if($this->session->userdata('musedata') == 'true'){
            ?>
              <li><a href="/musedata/project">Data</a></li>
            <?php
            }
            if($this->session->userdata('service') == 'true'){
            ?>
              <li id="service_chk"><a href="/service">Service</a></li>            
            <?php
            }
          ?>                     
            <li><a href="/notice">Notice</a></li>            
          <?php
          }                                      
        }
        ?>
       </ul> 
        <form class="navbar-form navbar-right">
          <span class="glyphicon glyphicon-user"></span>&nbsp;&nbsp;<?=$this->session->userdata('nickname');?>&nbsp;&nbsp;
          <a href="/sign/logout" class="btn btn-success">Logout</a>          
        </form>
      <?php
      }else{ // Not Sign in     
        ?>
        <li><a href="/">Data</a></li>
        <li><a href="/">Service</a></li>        
        <li><a href="/">Notice</a></li>            
        </ul> 
        <form class="navbar-form navbar-right">
          <a href="index.php/signup" class="btn btn-success">Sign Up</a>          
        </form>
      <?php      
      } 
      ?> 
    </div><!--/.navbar-collapse -->            
  </div>        
</div>     
<!-- head End -->