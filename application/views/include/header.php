<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>The intersect</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Finding, rating, matching and tagging a large set of images">
    <meta name="author" content="Waaghals">

    <!-- Le styles -->
    <link href="/assets/css/bootstrap.css" rel="stylesheet">
    <link href="/assets/css/bootstrap-responsive.css" rel="stylesheet">
    <link href="/assets/css/docs.css" rel="stylesheet">
    <link href="/assets/js/google-code-prettify/prettify.css" rel="stylesheet">

    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <!-- Le fav and touch icons -->
    <link rel="shortcut icon" href="assets/ico/favicon.ico">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="assets/ico/apple-touch-icon-114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="assets/ico/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="assets/ico/apple-touch-icon-57-precomposed.png">
  </head>

  <body data-spy="scroll" data-target=".subnav" data-offset="50">
  <?php if($this->session->flashdata('warning') != ''): ?>
	<div class="alert warning">
	  <a class="close" data-dismiss="alert">×</a>
	  <strong>Warning!</strong> <?php echo $this->session->flashdata('warning'); ?>
	</div>
  <?php endif; ?>
  
  <?php if($this->session->flashdata('notice') != ''): ?>
	<div class="alert alert-info">
	  <a class="close" data-dismiss="alert">×</a>
	  <strong>Notice</strong> <?php echo $this->session->flashdata('notice'); ?>
	</div>
  <?php endif; ?>
  
  <?php if($this->session->flashdata('error') != ''): ?>
	<div class="alert alert-error">
	  <a class="close" data-dismiss="alert">×</a>
	  <strong>Error!</strong> <?php echo $this->session->flashdata('error'); ?>
	</div>
  <?php endif; ?>
  
  <?php if($this->session->flashdata('success') != ''): ?>
	<div class="alert alert-success">
	  <a class="close" data-dismiss="alert">×</a>
	  <strong>Success</strong> <?php echo $this->session->flashdata('success'); ?>
	</div>
  <?php endif; ?>
