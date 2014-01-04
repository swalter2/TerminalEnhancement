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
<body>
	<?php
	if (isset($_POST['setClass'])){
		echo "<p>";
		echo "<a href=\"terminals.txt\">Download Terminal-ABNF</a>";
		echo "</p>";
		}
	?>
</body>
		
<body> <div id="terminal-start">

	<?php
	if (isset($_POST['setClass'])){
    	$classes = $_POST['setClass'];
    	$properties = [];
    	if (isset($_POST['setProperty'])){
    		$properties = $_POST['setProperty'];
    	}
    	else{
    		$properties = [];
    	}
		$result = shell_exec('python getTerminals.py ' . escapeshellarg(json_encode($classes)) .' '. escapeshellarg(json_encode($properties)));
		$json_output = json_decode($result, true);
		$entities = array_values($json_output)[0];
		echo "Retrieved ".count($entities)." terminals";
		echo "<br>";
		echo "<br>";
		$abnf = "#ABNF 1.0 UTF-8; \n Terminals = ";
		foreach($entities as $entry){
			$name = array_values($entry)[0];
			echo $name;
			echo "<br>";
			$abnf ="{$abnf} {$name} | ";
				
		}
		$abnf ="{$abnf};";
		$abnf = str_replace("| ;",";",$abnf);
		$abnf = str_replace("|   ","",$abnf);
		$out = fopen('terminals.txt', 'w') or die("can't open file");
		fwrite($out, $abnf);
		fclose($out);
		
		
		
		
	}
	else{
		echo "Choose a class";
	}
	
	?>
	
  </div>
</body>
</html>
	