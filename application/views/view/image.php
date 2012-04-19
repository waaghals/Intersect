<ul class="thumbnails">
	<li class="span12">
		<div class="thumbnail">
			<img src="/<?php echo $path;?>" alt="#<?php echo $id;?>">
			<?php if($tags): ?>
			<h5>Tags</h5>
			<?php
			foreach($tags as $tag):
				echo '<i class="icon-tag"></i>' . ucfirst($tag) . ' ';
			endforeach;
			endif;
			?>
		</div>
	</li>
</ul>