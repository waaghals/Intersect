<?php if ($this->session->flashdata('error') != ''): 
		echo 'Error: ' . $this->session->flashdata('error');
	endif;
?>
<?php if ($this->session->flashdata('notice') != ''): 
		echo 'Notice: ' . $this->session->flashdata('notice');
	endif;
?>
<?php if ($this->session->flashdata('success') != ''): 
		echo 'Notice: ' . $this->session->flashdata('success');
	endif;
?>
<?php if ($this->session->flashdata('warning') != ''): 
		echo 'Warning: ' . $this->session->flashdata('warning');
	endif;
?>