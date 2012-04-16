<?php echo form_open_multipart('upload/process');?>

<div class="control-group">
	<label class="control-label" for="file_input">File input</label>
	<div class="controls">
		<input class="input-file" id="file_input" type="file" name="userfile" onchange="showImg(this);"/>
	</div>
</div>
<ul class="thumbnails">
	<li class="span3">
		<div class="thumbnail">
			<img id="preview" src="http://placehold.it/260x180" alt="Your image"/>
			<div class="caption">
				<div class="control-group">
					<label class="control-label" for="tags">Tags</label>
					<div class="controls">
						<input class="input" id="tags" type="text" name="tags" placeholder="tag1, tag2, tag3"/>
					</div>
				</div>
			</div>
		</div>
	</li>
</ul>
<div class="form-actions">
	<button type="submit" class="btn btn-primary">
		Upload
	</button>
</div>
</form>
<?php $this->load->view('upload/js'); ?>
