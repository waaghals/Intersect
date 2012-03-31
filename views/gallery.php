<div style="width: <?php echo $container_width; ?>px;">
<?php
	foreach($rows as $row):
		?><div style="width: <?php echo $container_width; ?>px;"><?php
	 	foreach($row as $image):
			$this->load->view('resized_image', $image);
		endforeach;
		?></div><?php
	endforeach;
?>
</div>