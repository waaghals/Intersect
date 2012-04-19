<div class="row">
	<div class="span12">
		<?php echo form_open('/page/modify/' . $slug, array('class' => 'form-horizontal')); ?>
			<fieldset>
				<legend>
					Modify Page
				</legend>
				<div class="control-group">
					<label class="control-label" for="textarea">Markdown</label>
					<div class="controls">
						<textarea class="input-xlarge span12" id="textarea" name="markdown" rows="30"><?php echo $markdown;?></textarea>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="comment">Comment</label>
					<div class="controls">
						<input type="text" id="comment" name="comment">
					</div>
				</div>
				<div class="form-actions">
					<button type="submit" class="btn btn-primary">
						Modife Page
					</button>
				</div>
			</fieldset>
		</form>
	</div>
</div>