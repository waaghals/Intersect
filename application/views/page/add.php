<div class="row">
	<div class="span12">
		<?php echo form_open('/page/add', array('class' => 'form-horizontal')); ?>
			<fieldset>
				<legend>
					Create a page
				</legend>
				<div class="control-group">
					<label class="control-label" for="slug">Slug</label>
					<div class="controls">
						<input type="text" id="slug" name="slug">
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="title">Title</label>
					<div class="controls">
						<input type="text" id="title" name="title">
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="textarea">Markdown</label>
					<div class="controls">
						<textarea class="input-xlarge span12" id="textarea" name="markdown" rows="30">You can use markdown :)</textarea>
					</div>
				</div>
				<div class="form-actions">
					<button type="submit" class="btn btn-primary">
						Create Page
					</button>
				</div>
			</fieldset>
		</form>
	</div>
</div>