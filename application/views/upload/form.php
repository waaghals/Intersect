<?php echo form_open_multipart('upload/process', array('class' => 'form-inline'));?>

<div class="control-group">
	<label class="control-label" for="file_input">Select File</label>
	<div class="controls">
		<input class="input-file" id="file_input" type="file" name="userfile" onchange="showImg(this);" />
	</div>
</div>
<ul class="thumbnails">
	<li class="span4">
		<div class="thumbnail">
			<img id="preview" src="http://placehold.it/360x180&text=Image+preview" alt="Your image"/>
			<div class="caption">
				<div class="control-group">
				<label class="control-label" for="tags">Tags</label>
				<div class="controls">
					<div class="input-prepend">
						<span class="add-on"><i class="icon-tags"></i></span><input class="input" id="tags" type="text" name="tags" placeholder="Tag, Tag, ..." pattern="(([a-zA-Z0-9 ]{2,25})[\s,]?)+"/>
					</div>
					</div>
					<p class="help-block">
						See the <a href="/info/tag-reference">tag reference</a> on how to tag!
					</p>
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
<?php $this->load->view('upload/js');?> 