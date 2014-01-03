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
	$json_output = json_decode($result, true);
	foreach($json_output as $entry)
		{
			
			foreach($entry as $key => $value)
			{
				echo "<br>";
				echo "count:";
				echo count($value);
				echo "<br>";
				foreach($value as $x)
				{
					echo $x;
					echo "<br>";
				}
				echo "<br>";
				echo "<br>";
			}

		}
	}
?> 

<html>
<head>
  <title> Linked Data based Terminal Enhancement </title>
  <style>
    a { color: #000000;
        text-decoration:none; }
    
    #terminal-start { border-top: solid 1px;
                    padding:15px;
                    position:absolute;
                    left: 300px;
                    top:100px; }
  </style>
</head>



<?php if ($startpage)
      {
?>
<html>


<body>
  <div id="terminal-start">
  Please add at least 2 resources using DBpedia notation and seperated by a comma, into the textbox below!<br><br>
Examples are:<br>
Thessaloniki,Crete,Athens<br>
Hong_Kong_dollar,Euro<br>
Hong_Kong_dollar,Liberty_Dollar,Austrian_krone,Euro<br>
The_Matrix,Keanu_Reeves<br>
Max_Planck_Society,Bielefeld_University<br>
BMW,Volkswagen<br>
University_of_Paderborn,Bielefeld_University<br>
etc.<br><br><br>

    <span class="titel">Linked Data based Terminal Enhancement</span><br /><br />
    <form action="?" method="GET">
      <input id="start" type="text" name="resourcelist" size="80" maxlength="80" />
      <input type="submit" value="Start" />
    </form>
  </div>
</body>

</html>

<?php    
       }
?>

