<ul class="thumbnails">
  <li class="span6">
   <div class="thumbnail">
     <img src="/image/resize/<?php echo $left['id']; ?>/460/4000" alt="Left Wins!" onclick="document.left.submit();">
    <h5>Tags</h5>
    <p>Thumbnail caption right here...</p>
    <ul>
     <li><strong>Uploaded: </strong><?php echo $left['uploaded']; ?></li>
     <li><strong>Uploader: </strong><?php echo $left['title'] . ' ' . $left['username']; ?></li>
    </ul>
   </div>
  </li>
  <li class="span6">
   <div class="thumbnail">
     <img src="/image/resize/<?php echo $right['id']; ?>/460/4000" alt="Right wins!" onclick="document.right.submit();">
    <h5>Tags</h5>
    <p>Thumbnail caption right here...</p>
     <ul>
     <li><strong>Uploaded: </strong><?php echo $right['uploaded']; ?></li>
     <li><strong>Uploader: </strong><?php echo $right['title'] . ' ' . $right['username']; ?></li>
    </ul>
   </div>
  </li>
</ul>

<?php 
//Left image
echo form_open('rate', array('name' => 'left'));
echo form_hidden(array(
					'winner' => $left['id'],
					'loser' => $right['id']
					));
echo form_close();

//Right image
echo form_open('rate', array('name' => 'right'));
echo form_hidden(array(
					'winner' => $right['id'],
					'loser' => $left['id']
					));
echo form_close();
?>