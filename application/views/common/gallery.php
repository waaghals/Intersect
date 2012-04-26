<?php
if(is_array($rows)):
	foreach($rows as $row):
		?><div class="row"><?php
	 	foreach($row as $image):
			$this->load->view('common/gallery_image', $image);
		endforeach;
		?></div><?php
	endforeach;
elseif(is_string($rows)):
	echo $rows;
endif;
?>