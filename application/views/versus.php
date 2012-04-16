<ul class="thumbnails">
	<li class="span6">
		<div class="thumbnail">
			<img src="/image/resize/<?php echo $left['id'];?>/460/4000" alt="Left Wins!" onclick="document.left.submit();">
			<?php if( ! is_null($left['tags'])):
			?>
			<h5>Tags</h5>
			<?php
			$tags = explode(',', $left['tags']);
			foreach($tags as $tag):
				echo '<span class="label">' . $tag . '</span> ';
			endforeach;
			endif;
			?>

			<h5>Uploader</h5>
			<p>
				<i><?php echo $left['title'] . '</i> ' . $left['username'];?>
			</p>
			<h5>Uploaded</h5>
			<p>
				<?php echo $left['uploaded'];?>
			</p>
		</div>
	</li>
	<li class="span6">
		<div class="thumbnail">
			<img src="/image/resize/<?php echo $right['id'];?>/460/4000" alt="Right wins!" onclick="document.right.submit();">
			<?php if( ! is_null($right['tags'])):
			?>
			<h5>Tags</h5>
			<?php
			$tags = explode(',', $right['tags']);
			foreach($tags as $tag):
				echo '<span class="label">' . $tag . '</span> ';
			endforeach;
			endif;
			?>

			<h5>Uploader</h5>
			<p>
				<i><?php echo $right['title'] . '</i> ' . $right['username'];?>
			</p>
			<h5>Uploaded</h5>
			<p>
				<?php echo $right['uploaded'];?>
			</p>
		</div>
	</li>
</ul>
<?php
//Left image
echo form_open('rate', array('name' => 'left'));
echo form_hidden(array('winner' => $left['id'], 'loser' => $right['id']));
echo form_close();

//Right image
echo form_open('rate', array('name' => 'right'));
echo form_hidden(array('winner' => $right['id'], 'loser' => $left['id']));
echo form_close();
?>