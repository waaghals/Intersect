<html>
<head>
<title>My Form</title>
</head>
<body>

<?php echo validation_errors(); ?>

<?php echo form_open('user/sign_in'); ?>

<h5>Username</h5>
<input type="text" name="username" value="" size="50" />

<h5>Password</h5>
<input type="text" name="password" value="" size="50" />

<div><input type="submit" value="Submit" name="submit" /></div>
<a href="/user/sign_up">I don't have an account</a>
</form>

</body>
</html>