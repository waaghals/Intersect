<?php
$this->load->helper('form');

//Left image
echo form_open('rate');
echo form_hidden(array(
					'winner' => $left['id'],
					'loser' => $right['id']
					));
$data = array(
			'name'	=> 'submit',
			'type'	=> 'image',
			'src'	=> $left['path'],
			'alt'	=> 'Left wins!'
             );
echo form_input($data);
echo form_close();

//Right image
echo form_open('rate');
echo form_hidden(array(
					'winner' => $right['id'],
					'loser' => $left['id']
					));
$data = array(
			'name'	=> 'submit',
			'type'	=> 'image',
			'src'	=> $right['path'],
			'alt'	=> 'Right wins!'
             );
echo form_input($data);
echo form_close();
?>