<ul class="thumbnails">
	<li class="span12">
		<div class="thumbnail">
			<img src="/<?php echo $path;?>" alt="#<?php echo $id;?>">
			<div class="caption">
				<?php 
				if($tags):
				?>
				<h5>Tags</h5>
				<?php
				foreach(explode(',', $tags) as $tag):
					echo '<i class="icon-tag"></i>' . ucfirst($tag) . ' ';
				endforeach;
				endif;
				?>

				<?php 
					echo form_open('image/tag', array('class' => 'form-inline'));
					echo form_hidden('image_id', $id);
				?>
				<div class="control-group">
					<div class="controls">
						<div class="input-prepend input-append">
							<span class="add-on">Add</span><input class="input" id="tags" type="text" name="tags" placeholder="Tag, Tag, ..." pattern="(([a-zA-Z0-9 ]{2,25})[\s,]?)+"/><input class="btn btn-primary" type="submit"  value="Tags!"/>
						</div>
					</div>
					<p class="help-block">
						See the <a href="/info/tag-reference">tag reference</a> on how to tag!
					</p>
				</div>
				</form>
			</div>
		</div>
	</li>
</ul>