<?php echo form_open_multipart('upload/process', array('class' => 'form-inline'));?>

<div class="control-group">
	<label class="control-label" for="file_input">Select File(s)</label>
	<div class="controls">
		<input class="input-file" id="file_input" type="file" name="userfile[]" multiple="multiple"/>
	</div>
</div>
<div class="form-actions">
	<button type="submit" class="btn btn-primary">Upload</button>
</div>
</form>