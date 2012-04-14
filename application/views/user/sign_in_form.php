<h1>Sign In</h1>
<?php echo validation_errors(); ?>
<?php echo form_open('user/sign_in', array('class' => 'form-horizontal')); ?>
  <fieldset>
    <legend>Sign in</legend>
    <div class="control-group">
      <label class="control-label" for="input01">Username</label>
      <div class="controls">
        <input type="text" class="input-xlarge" name="username" id="username">
      </div>
    </div>
    
    <div class="control-group">
      <label class="control-label" for="input01">Password</label>
      <div class="controls">
        <input type="password" class="input-xlarge" name="password" id="password">
      </div>
    </div>
    
    <div class="form-actions">
	<input type="submit" class="btn btn-primary" value="Sign In" name="submit" /> <a href="/user/sign_in">I don't have an account</a>
	</div>
  </fieldset>
</form>