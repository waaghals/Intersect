<?php
	foreach($rows as $row):
		?><div class="row"><?php
	 	foreach($row as $image):
			$this->load->view('resized_image', $image);
		endforeach;
		?></div><?php
	endforeach;
?>