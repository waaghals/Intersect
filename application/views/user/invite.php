<h1>Generate Registration Key</h1>
<?php if($numkeys >= 5):
?>
<div class="alert alert-error">
	You already have generated 5 keys that are all unused. Wait one month for the keys to expire or for keys to be used so you can generate new ones.
</div>
<?php endif; ?>
<p>
	Send the code <code><?php echo $key; ?></code>
	to the person you would like to invite. <br />Remember the following:
</p>
<ul>
	<li>
		A key can only be used once
	</li>
	<li>
		Keys expire after a month
	</li>
	<li>
		You can only have 5 unused keys at one time
	</li>
</ul>
<p>You currently have <code><?php echo $numkeys+1; ?></code> unused keys. <i>(includes the key currently displayed)</i></p>