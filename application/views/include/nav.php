  <!-- Navbar
    ================================================== -->
    <div class="navbar navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <a class="brand" href="/">Intersect</a>
          <div class="nav-collapse">
            <ul class="nav">
             <?php if($this->auth->is_logged_in()): ?>
              <li class="">
                <a href="/">Rate</a>
              </li>
              <li class="">
                <a href="/upload">Upload</a>
              </li>
              <li class="">
                <a href="/view/top/100">Top 100</a>
              </li>
              <?php if($this->auth->is_autoconfirmed()): ?>
              <li class="">
                <a href="/user/invite">Invite Others</a>
              </li>
              <?php endif; ?>
              <li class="">
                <a href="/user/table">Users</a>
              </li>
             <?php endif; ?>
            </ul>
            <ul class="nav pull-right">
              <?php if($this->auth->is_logged_in()): ?>
              <li class="">
                <a href="/user/modify">Edit Profile</a>
              </li>
              <li class="">
                <a href="/user/sign_out">Sign Out</a>
              </li>
              <?php else: ?>
              <li class="">
                <a href="/user/sign_in">Sign In</a>
              </li>
              <li class="">
                <a href="/user/sign_up">Sign Up</a>
              </li>
              <?php endif; ?>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <div class="container">
  <?php if($this->session->flashdata('warning') != ''): ?>
	<div class="alert warning">
	  <a class="close" data-dismiss="alert">×</a>
	  <strong>Warning!</strong> <?php echo $this->session->flashdata('warning');?>
	</div>
  <?php endif;?>
  
  <?php if($this->session->flashdata('notice') != ''): ?>
	<div class="alert alert-info">
	  <a class="close" data-dismiss="alert">×</a>
	  <strong>Notice</strong> <?php echo $this->session->flashdata('notice');?>
	</div>
  <?php endif;?>
  
  <?php if($this->session->flashdata('error') != ''): ?>
	<div class="alert alert-error">
	  <a class="close" data-dismiss="alert">×</a>
	  <strong>Error:</strong> <?php echo $this->session->flashdata('error');?>
	</div>
  <?php endif;?>
  
  <?php if($this->session->flashdata('success') != ''): ?>
	<div class="alert alert-success">
	  <a class="close" data-dismiss="alert">×</a>
	  <strong>Success</strong> <?php echo $this->session->flashdata('success');?>
	</div>
  <?php endif;?>
  
  <?php if(validation_errors() != ''): ?>
	<div class="alert alert-warning">
	  <a class="close" data-dismiss="alert">×</a>
	  <strong>Validation Error:</strong> <?php echo validation_errors();?>
	</div>
  <?php endif;?>

