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
				echo '<i class="icon-tag"></i>' . ucfirst($tag) . ' ';
			endforeach;
			endif;
			?>
			<ul>
				<li><i><?php echo $left['title'] . '</i> <a href="/user/profile/' . $left['username'] . '">' . ucfirst($left['username'] . '</a>');?></li>
				<li><?php echo $left['uploaded'];?></li>
			</ul>
			<?php
			echo form_open('image/fav', array('name' => 'left_fav'));
			echo form_hidden(array('image_id' => $left['id']));
			echo form_submit(array('name' => 'left_fav_sub', 'class' => 'btn'), 'Fav!');
			echo form_close(); ?>
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
				echo '<i class="icon-tag"></i>' . ucfirst($tag) . ' ';
			endforeach;
			endif;
			?>
			<ul>
				<li><i><?php echo $right['title'] . '</i> <a href="/user/profile/' . $right['username'] . '">' . ucfirst($right['username'] . '</a>');?></li>
				<li><?php echo $right['uploaded'];?></li>
			</ul>
			<?php
			echo form_open('image/fav', array('name' => 'right_fav'));
			echo form_hidden(array('image_id' => $right['id']));
			echo form_submit(array('name' => 'right_fav_sub', 'class' => 'btn'), 'Fav!');
			echo form_close(); ?>
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