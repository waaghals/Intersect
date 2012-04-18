<div class="row">
	<div class="span12">
		<?php echo form_open('/page/modify', array('class' => 'form-horizontal')); ?>
			<fieldset>
				<legend>
					Page
				</legend>
				<div class="control-group">
					<label class="control-label" for="password">Slug</label>
					<div class="controls">
						<input type="text" id="slug" name="slug">
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="password">Title</label>
					<div class="controls">
						<input type="text" id="title" name="title">
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="textarea">Markdown</label>
					<div class="controls">
						<textarea class="input-xlarge span12" id="textarea" name="markdown" rows="30"><?php echo $markdown;?></textarea>
					</div>
				</div>
				<div class="form-actions">
					<button type="submit" class="btn btn-primary">
						Save page
					</button>
				</div>
			</fieldset>
		</form>
	</div>
</div>