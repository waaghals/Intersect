
<?php echo form_open_multipart('upload/process');?>

<div class="control-group">
 <label class="control-label" for="file_input">File input</label>
 <div class="controls">
  <input class="input-file" id="file_input" type="file" name="userfile" />
 </div>
</div>

<div class="form-actions">
 <button type="submit" class="btn btn-primary">Upload</button>
</div>
</form>
