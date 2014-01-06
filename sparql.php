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
	echo "<br>";
	echo "<br>";
	echo "Common dbpedia classes:";
	echo "<br>";
	echo "(The most general class is on the top, the most specialized class is on the bottom)";
	echo "<br>";
	$classes = array_values($json_output)[1];
	foreach($classes as $entry){
		$uri = array_values($entry)[0];
		$value = array_values($entry)[1];
		$out = "{$uri}";
		$replaceOntology = "http://dbpedia.org/ontology/";
		$out = str_replace($replaceOntology,"",$out);
		echo "<input type=\"checkbox\" name=\"setClass[]\" value=\"$uri\" id=\"id{ $uri}\"\"/>";
		echo "<label for=\"id{$uri}\"> $out </label> <font color=\"grey\">( $value )</font> <br>";
	
	}
	echo "<br>";
	echo "<br>";
	echo "Common yago classes:";
	echo "<br>";
	$yago = array_values($json_output)[0];
	foreach($yago as $entry){
		$uri = array_values($entry)[0];
		$value = array_values($entry)[1];
		$out = "{$uri}";
		$replaceYago = "http://dbpedia.org/class/yago/";
		$out = str_replace($replaceYago,"",$out);
		echo "<input type=\"checkbox\" name=\"setClass[]\" value=\"$uri\" id=\"id{ $uri}\"\"/>";
		echo "<label for=\"id{$uri}\"> $out</label> <font color=\"grey\">( $value )</font> <br>";
	
	}
	
		
	echo "<br>";
	echo "<br>";
	echo "Common properties:";
	echo "<br>";
	$properties = array_values($json_output)[2];
	foreach($properties as $entry){
		$uri = array_values($entry)[0];
		$value = array_values($entry)[1];
		$out = "{$uri} with {$value}";
		$out_python = $out;
		$replaceOntology = "http://dbpedia.org/ontology/";
		$replaceProperty = "http://dbpedia.org/property/";
		$replaceResoruce= "http://dbpedia.org/resource/";
		$out = str_replace($replaceProperty,"",$out);
		$out = str_replace($replaceOntology,"",$out);
		$out = str_replace($replaceResoruce,"",$out);
		
		echo "<input type=\"checkbox\" name=\"setProperty[]\" value=\"$out_python\" id=\"id{ $uri}\" \"/>";
		echo "<label for=\"id{$uri}\"> $out</label><br>";
	
	}
	
	echo "<br>";
	echo "<br>";
	
	#echo "The name of the entities will be in English,<br>";
	echo "Additional language for terminals (default is English):<br>";
	#make english default and with optional one other language possible
	echo "<input type=\"radio\" name=\"language\" value=\"none\" id=\"none\" checked=\"checked\"/>";
	echo "<label for=\"id{en}\">None</label><br>";
	echo "<input type=\"radio\" name=\"language\" value=\"de\" id=\"de\"/>";
	echo "<label for=\"id{de}\">German</label><br>";
	echo "<input type=\"radio\" name=\"language\" value=\"fr\" id=\"fr\"/>";
	echo "<label for=\"id{fr}\">French</label><br>";
	echo "<input type=\"radio\" name=\"language\" value=\"es\" id=\"es\"/>";
	echo "<label for=\"id{es}\">Spanish</label><br>";
	echo "<input type=\"radio\" name=\"language\" value=\"zh\" id=\"zh\"\"/>";
	echo "<label for=\"id{zh}\">Chinese</label><br>";
	echo "<input type=\"radio\" name=\"language\" value=\"ru\" id=\"ru\"/>";
	echo "<label for=\"id{ru}\">Russian</label><br>";
	echo "<br>";
	echo "<br>";
	
	echo "<input type=\"submit\" name=\"submit\" value=\"Submit\"><br>";
	#echo "Returning the entities in the prefered language, might need some time!";
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
Thessaloniki,Athens<br>
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
