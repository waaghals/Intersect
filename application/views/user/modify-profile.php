<div class="row">
	<div class="span8">
		<?php echo form_open('/user/modify/profile', array('class' => 'form-horizontal')); ?>
			<fieldset>
				<legend>
					Profile
				</legend>
				<div class="control-group">
					<label class="control-label" for="textarea">Markdown</label>
					<div class="controls">
						<textarea class="input-xlarge span6" id="textarea" name="markdown" rows="20"><?php echo $markdown;?></textarea>
					</div>
				</div>
				<div class="form-actions">
					<button type="submit" class="btn btn-primary">
						Modify Profile
					</button>
				</div>
			</fieldset>
		</form>
		<?php echo form_open('/user/modify/password', array('class' => 'form-horizontal')); ?>
			<fieldset>
				<legend>
					Password
				</legend>
				<p>Only fill when you would like to set a new password.</p>
				<div class="control-group">
					<label class="control-label" for="password">Current password</label>
					<div class="controls">
						<input type="password" id="password" name="password">
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="passnew">New password</label>
					<div class="controls">
						<input type="password" id="passnew" name="passnew">
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="passconf">Repeat password</label>
					<div class="controls">
						<input type="password" id="passconf" name="passconf">
					</div>
				</div>
				<div class="form-actions">
					<button type="submit" class="btn btn-primary">
						Change password
					</button>
				</div>
			</fieldset>
		</form>
	</div>
	<div class="span4">
		<h3>Variables</h3>
		<p>
			Use any of the following variables in you profile to create a dynamic profile page.
		</p>
		<ul>
			<li><em>{username}</em></li>
			<li><em>{user_id}</em></li>
			<li><em>{rankth}</em><br />Your rank as 123th</li>
			<li><em>{rank}</em><br />Your rank as 123</li>
			<li><em>{title}</em></li>
			<li><em>{timeframe}</em><br />Relative date when you signed up, does not include 'ago'</li>
			<li><em>{since}</em><br />Date and time from when you signed up</li>
			<li><em>{karma}</em></li>
		</ul>
		<hr>
		<h3>Markdown</h3>
		<p>
			You can style you profile with markdown. For an indepth guide to markdown see the <a href="http://daringfireball.net/projects/markdown/syntax">markdown syntax guide</a>.
		</p>
	</div>
</div>