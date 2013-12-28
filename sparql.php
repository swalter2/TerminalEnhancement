<?php
//http://stackoverflow.com/questions/14047979/executing-python-script-in-php-and-exchanging-data-between-the-two
//data to pass to python
$data = array('http://dbpedia.org/resource/Berlin', 'http://dbpedia.org/resource/Thessaloniki');

// Execute the python script with the JSON data
$result = shell_exec('python work_with_classes.py ' . escapeshellarg(json_encode($data)));

// Decode the result
$resultData = json_decode($result, true);

var_dump($resultData);
?> 


