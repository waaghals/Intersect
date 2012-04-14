<h1>Sign Up</h1>

<?php echo form_open('user/sign_up', array('class' => 'form-horizontal')); ?>
  <fieldset>
    <legend>Sign up</legend>
    <div class="control-group">
      <label class="control-label" for="input01">Username</label>
      <div class="controls">
        <input type="text" class="input-xlarge" name="username" id="username">
        <span class="help-inline">Spaces are allowed, Case insensitive</span>
      </div>
    </div>
    
    <div class="control-group">
      <label class="control-label" for="input01">Password</label>
      <div class="controls">
        <input type="password" class="input-xlarge" name="password" id="password">
      </div>
    </div>
    
    <div class="control-group">
      <label class="control-label" for="input01">Ctrl + V</label>
      <div class="controls">
        <input type="password" class="input-xlarge" name="passconf" id="passconf">
        <span class="help-inline">Repeat your password</span>
      </div>
    </div>
    
    <div class="form-actions">
	<input type="submit" class="btn btn-primary" value="Sign Up!" name="submit" />
	</div>
  </fieldset>
</form>