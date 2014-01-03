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

	
	#classes
	echo "<body> <div id=\"terminal-start\">";
	echo "<form action=\"getTerminals.php\" method=\"post\">";
	echo "Common classes:";
	echo "<br>";
			
	$classes = array_values($json_output)[0];
	foreach($classes as $entry){
		$uri = array_values($entry)[0];
		$out = "{$uri}";
		$replaceOntology = "http://dbpedia.org/ontology/";
		$replaceProperty = "http://dbpedia.org/property/";
		$out = str_replace($replaceProperty,"dbp:",$out);
		$out = str_replace($replaceOntology,"dbo:",$out);
		echo "<input type=\"checkbox\" name=\"setClass\" value=\"$uri\" id=\"id{ $uri}\" checked=\"checked\"/>";
		echo "<label for=\"id{$uri}\"> $out</label><br>";
				
	}
		
	echo "<br>";
	echo "<br>";
	echo "Common properties:";
	echo "<br>";
	$properties = array_values($json_output)[1];
	foreach($properties as $entry){
		$uri = array_values($entry)[0];
		$value = array_values($entry)[1];
		$out = "{$uri} with {$value}";
		$replaceOntology = "http://dbpedia.org/ontology/";
		$replaceProperty = "http://dbpedia.org/property/";
		$out = str_replace($replaceProperty,"dbp:",$out);
		$out = str_replace($replaceOntology,"dbo:",$out);
		echo "<input type=\"checkbox\" name=\"setProperty\" value=\"$uri\" id=\"id{ $uri}\" checked=\"checked\"/>";
		echo "<label for=\"id{$uri}\"> $out</label><br>";
	
	}
	
	echo "<br>";
	echo "<br>";
	
	echo "<input type=\"submit\" name=\"submit\" value=\"Submit\"><br>";
	echo "Returning the entities in the prefered language, might need some time!";
	echo "</form>";
	
	echo "</div></body>";
	
	}
?> 



<?php if ($startpage)
      {
?>


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


<?php    
       }
?>


</html>
