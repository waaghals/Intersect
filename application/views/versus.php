<ul class="thumbnails">
  <li class="span6">
   <div class="thumbnail">
     <img src="/image/resize/<?php echo $left; ?>/460/4000" alt="Left Wins!" onclick="document.left.submit();">
    <h5>Tags</h5>
    <p>Thumbnail caption right here...</p>
   </div>
  </li>
  <li class="span6">
   <div class="thumbnail">
     <img src="/image/resize/<?php echo $right; ?>/460/4000" alt="Right wins!" onclick="document.right.submit();">
    <h5>Tags</h5>
    <p>Thumbnail caption right here...</p>
   </div>
  </li>
</ul>

<?php 
//Left image
echo form_open('rate', array('name' => 'left'));
echo form_hidden(array(
					'winner' => $left,
					'loser' => $right
					));
echo form_close();

//Right image
echo form_open('rate', array('name' => 'right'));
echo form_hidden(array(
					'winner' => $right,
					'loser' => $left
					));
echo form_close();
?>