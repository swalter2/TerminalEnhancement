<?php
header('Content-Type: text/html; charset=utf-8');

$startpage = false;

if (!isset($_GET['resourcelist']))
{
	$startpage = true ;
}
if (! $startpage) {
	$res = $_GET['resourcelist'];
			
	//http://stackoverflow.com/questions/14047979/executing-python-script-in-php-and-exchanging-data-between-the-two
	//data to pass to python

	$data = explode(",", $res);

	// Execute the python script with the JSON data
	$result = shell_exec('python work_with_classes.py ' . escapeshellarg(json_encode($data)));
	

	// Decode the result
	$resultData = json_decode($result, true);

	var_dump($resultData);
}
?> 





<?php if ($startpage)
      {
?>
<html>
<head>
<title>
Linked Data based Terminal Enhancement
</title>

</head>

<body>
Please add at least 2 resources using DBpedia notation and seperated by a comma, into the textbox below!<br><br>
Examples are:<br>
Thessaloniki,Crete,Athens<br>
Hong_Kong_dollar,Euro<br>
Hong_Kong_dollar,Liberty_Dollar,Austrian_krone,Euro<br>
The_Matrix,Keanu_Reeves<br>
Max_Planck_Society,Bielefeld_University<br>
BMW,Volkswagen<br>
University_of_Paderborn,Bielefeld_University<br>
etc.
<form action= "?" method= "GET">

<p>Resources:<br><input name="resourcelist" type="textbox" size="60" maxlength="60"></p>

<input type= "submit" value= "Start">
</form>
</body>

</html>

<?php    
       }
?>

