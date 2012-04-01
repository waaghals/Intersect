<?php
$this->load->helper('form');

//Left image
echo form_open('rate');
echo form_hidden(array(
					'winner' => $left_id,
					'loser' => $right_id
					));
$data = array(
			'name'	=> 'submit',
			'type'	=> 'image',
			'src'	=> $left,
			'alt'	=> 'Left wins!'
             );
echo form_input($data);
echo form_close();

//Right image
echo form_open('rate');
echo form_hidden(array(
					'winner' => $right_id,
					'loser' => $left_id
					));
$data = array(
			'name'	=> 'submit',
			'type'	=> 'image',
			'src'	=> $right,
			'alt'	=> 'Right wins!'
             );
echo form_input($data);
echo form_close();
?>