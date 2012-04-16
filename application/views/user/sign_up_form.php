<h1>Sign Up</h1>
<?php if(validation_errors() != ''): ?>
	<div class="alert alert-error">
	<?php echo validation_errors(); ?>
	</div>
<?php endif; ?>
	
<?php echo form_open('user/sign_up', array('class' => 'form-horizontal'));?>
<fieldset>
	<legend>
		Sign up
	</legend>
	<div class="control-group">
		<label class="control-label" for="username">Username</label>
		<div class="controls">
			<input type="text" class="input-xlarge" name="username" id="username" pattern="^[a-zA-Z\s]{1,50}$" placeholder="John Doe">
			<span class="help-inline">a-Z Case insensitive, spaces allowed</span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="password">Password</label>
		<div class="controls">
			<input type="password" class="input-xlarge" name="password" id="password">
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="passconf">Ctrl + V</label>
		<div class="controls">
			<input type="password" class="input-xlarge" name="passconf" id="passconf">
			<span class="help-inline">Repeat your password</span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="key">Key</label>
		<div class="controls">
			<input type="text" class="input-xlarge" name="key" id="key" pattern="^[A-Z]{10}$" placeholder="ABCDEFGHIJ">
			<span class="help-inline">You need a valid key from an existing member to be able to create an account</span>
		</div>
	</div>
	<div class="form-actions">
		<input type="submit" class="btn btn-primary" value="Sign Up!" name="submit" /> <a href="/user/sign_in">I already have an account!</a>
	</div>
</fieldset>
</form>