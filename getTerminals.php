<?php
header('Content-Type: text/html; charset=utf-8');

?>

<html>
<head>
  <title> Linked Data based Terminal Enhancement </title>
  <meta http-equiv="content-type" content="text/html; charset=utf-8">
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
	if (isset($_POST['setClass'])){
		echo "<body>";
		echo "<p>";
		echo "<a href=\"terminals.txt\">Download Terminal-ABNF</a>";
		echo "</p>";
		echo "</body>";
    	$classes = $_POST['setClass'];
    	$properties = [];
    	if (isset($_POST['setProperty'])){
    		$properties = $_POST['setProperty'];
    	}
    	else{
    		$properties = [];
    	}
    	$second_language = ($_POST['language']);
    	$boolean = ($_POST['boolean']);
    	if($second_language != "none"){
    		echo "<body>";
    		echo "<p>";
    		echo "<a href=\"terminals".$second_language.".txt\">Download Terminal-ABNF for the additional language</a>";
    		echo "</p>";
    		echo "</body>";
    	}
    	
    	echo "<body> <div id=\"terminal-start\">";
    	
		$result = shell_exec('python getTerminals.py ' . escapeshellarg(json_encode($classes)) .' '. escapeshellarg(json_encode($properties)).' '. escapeshellarg($second_language).' '. escapeshellarg($boolean));
		#echo $result;
		$json_output = json_decode($result, true);
		$entities = array_values($json_output)[0];
		echo "<br>";
		echo "Retrieved ".count($entities)." terminals";
		echo "<br>";
		echo "<br>";
		$abnf = "#ABNF 1.0 UTF-8; \n Terminals = ";
		$abnf2 = "#ABNF 1.0 UTF-8; \n Terminals = ";
		foreach($entities as $entry){
			$name= "";
			$name2= "";
			if (count(array_values($entry))==2){
				$name = array_values($entry)[1];
				echo $name;
				$tmp = array_values($entry)[0];
				if (count($tmp)>0 && $tmp!=""){
					echo " -- ".$tmp;
					$name2 = $tmp;
				}
			}
			else{
				$name = array_values($entry)[0];
				echo $name;
			}
			echo "<br>";
			$abnf ="{$abnf} {$name} | ";
			if (count($name2)>0){
				$abnf2 ="{$abnf2} {$name2} | ";
			}
				
		}
		$abnf ="{$abnf};";
		$abnf = str_replace("| ;",";",$abnf);
		$abnf = str_replace("|   ","",$abnf);
		$out = fopen('terminals.txt', 'w') or die("can't open file");
		fwrite($out, $abnf);
		fclose($out);
		
		if($second_language != "none"){
			$abnf2 ="{$abnf2};";
			$abnf2 = str_replace("| ;",";",$abnf2);
			$abnf2 = str_replace("|   ","",$abnf2);
			$out = fopen("terminals".$second_language.".txt", 'w') or die("can't open file");
			fwrite($out, $abnf2);
			fclose($out);
		}
		
		
		
		
	}
	else{
		echo "Choose a class";
	}
	
	?>
	
  </div>
</body>
</html>
	