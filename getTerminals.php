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
        if(isset($_POST['setTerminals'])|| isset($_POST['setTerminals2'])){
            
            echo "<body>";
            echo "<p>";
            if(isset($_POST['setTerminals'])){
                
                echo "<a href=\"terminals.txt\">Download Terminal-ABNF</a>";
                
                $abnf = "#ABNF 1.0 UTF-8; \n Terminals = ";
                $terminals = $_POST['setTerminals'];
                foreach($terminals as $entry){
                    $abnf ="{$abnf} {$entry} | ";
                    
                    
                }
                
                $abnf ="{$abnf};";
                $abnf = str_replace("| ;",";",$abnf);
                $abnf = str_replace("|   ","",$abnf);
                $out = fopen('terminals.txt', 'w') or die("can't open file");
                fwrite($out, $abnf);
                fclose($out);
                
                
            }
            echo "<br>";
            echo "<br>";
            
            if(isset($_POST['setTerminals2'])){
                
                echo "<a href=\"terminals2.txt\">Download Terminal-ABNF for second language</a>";
                
                $abnf = "#ABNF 1.0 UTF-8; \n Terminals = ";
                $terminals = $_POST['setTerminals2'];
                foreach($terminals as $entry){
                    $abnf ="{$abnf} {$entry} | ";
                    
                    
                }
                
                $abnf ="{$abnf};";
                $abnf = str_replace("| ;",";",$abnf);
                $abnf = str_replace("|   ","",$abnf);
                $out = fopen('terminals2.txt', 'w') or die("can't open file");
                fwrite($out, $abnf);
                fclose($out);
                
                
            }
            
            
            echo "</p>";
            echo "</body>";
            
            
        }
    elseif (isset($_POST['setClass']) && isset($_POST['setCategory'])){
        
        echo "Please choose either Classes or Categories.";
    }
    elseif (isset($_POST['setCategory'])){
        
        $numberterminals = 100;
        if (isset($_POST['numberterminals'])){
    		$numberterminals = $_POST['numberterminals'];
    	}
        
    	$categories = $_POST['setCategory'];
    	$second_language = ($_POST['language']);
    	
    	echo "<body> <div id=\"terminal-start\">";
        echo "<form action=\"getTerminals.php\" method=\"post\">";
        #echo 'python getTerminals.py category'.' '. escapeshellarg(json_encode($categories)).' '. escapeshellarg($second_language);
    	
		$result = shell_exec('python getTerminals.py category'.' '. escapeshellarg(json_encode($categories)).' '. escapeshellarg($second_language).' '. escapeshellarg($numberterminals));
		#echo $result;
		$json_output = json_decode($result, true);
		$entities = array_values($json_output)[0];
		echo "<br>";
		echo "Retrieved ".count($entities)." terminals";
		echo "<br>";
		echo "<br>";
        echo "<table border=\"0\">";
        echo "<tr>";
        echo "<td><h2>en</h2></td>";
        echo "<td><h2>$second_language</h2></td>";
        echo "</tr>";
		$abnf = "#ABNF 1.0 UTF-8; \n Terminals = ";
		$abnf2 = "#ABNF 1.0 UTF-8; \n Terminals = ";
		foreach($entities as $entry){
            echo "<tr>";
			$name= "";
			$name2= "";
			if (count(array_values($entry))==2){
				$name = array_values($entry)[1];
				#echo $name;
                echo "<td><input type=\"checkbox\" name=\"setTerminals[]\" value=\"$name\" id=\"id{$name}\" \"/>";
                echo "<label for=\"id{$name}\"> $name</label></td>";
                $tmp = array_values($entry)[0];
				if (count($tmp)>0 && $tmp!=""){
					#echo " -- ".$tmp;
					$name2 = $tmp;
                    #$name = $name." -- ".$tmp;
                    echo "<td><input type=\"checkbox\" name=\"setTerminals2[]\" value=\"$name2\" id=\"id{$name2}\" \"/>";
                    echo "<label for=\"id{$name2}\"> $name2</label></td>";
				}
                
                
			}
			else{
				$name = array_values($entry)[0];
				#echo $name;
                echo "<td><input type=\"checkbox\" name=\"setTerminals[]\" value=\"$name\" id=\"id{$name}\" \"/>";
                echo "<label for=\"id{$name}\"> $name</label></td>";
                echo "<td></td>";
			}
			#echo "<br>";
            echo "</tr>";
			$abnf ="{$abnf} {$name} | ";
			if (count($name2)>0){
				$abnf2 ="{$abnf2} {$name2} | ";
			}
            #echo "<br>";
		}
        echo "</table>";
        echo "<input type=\"submit\" name=\"Download\" value=\"Submit\"><br>";
        echo "</form>";
		
		
		
		
	}
        
	elseif (isset($_POST['setClass'])){
        $numberterminals = 100;
        if (isset($_POST['numberterminals'])){
    		$numberterminals = $_POST['numberterminals'];
    	}
        
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

    	
    	echo "<body> <div id=\"terminal-start\">";
        echo "<form action=\"getTerminals.php\" method=\"post\">";
    	
		$result = shell_exec('python getTerminals.py ' . escapeshellarg(json_encode($classes)) .' '. escapeshellarg(json_encode($properties)).' '. escapeshellarg($second_language).' '. escapeshellarg($boolean).' '. escapeshellarg($numberterminals));
		#echo $result;
		$json_output = json_decode($result, true);
		$entities = array_values($json_output)[0];
		echo "<br>";
		echo "Retrieved ".count($entities)." terminals";
		echo "<br>";
		echo "<br>";
		$abnf = "#ABNF 1.0 UTF-8; \n Terminals = ";
		$abnf2 = "#ABNF 1.0 UTF-8; \n Terminals = ";
        echo "<table border=\"0\">";
        echo "<tr>";
        echo "<td><h2>en</h2></td>";
        echo "<td><h2>$second_language</h2></td>";
        echo "</tr>";
		foreach($entities as $entry){
            echo "<tr>";
			$name= "";
			$name2= "";
			if (count(array_values($entry))==2){
				$name = array_values($entry)[1];
				#echo $name;
                echo "<td><input type=\"checkbox\" name=\"setTerminals[]\" value=\"$name\" id=\"id{$name}\" \"/>";
                echo "<label for=\"id{$name}\"> $name</label></td>";
                $tmp = array_values($entry)[0];
				if (count($tmp)>0 && $tmp!=""){
					#echo " -- ".$tmp;
					$name2 = $tmp;
                    #$name = $name." -- ".$tmp;
                    echo "<td><input type=\"checkbox\" name=\"setTerminals2[]\" value=\"$name2\" id=\"id{$name2}\" \"/>";
                    echo "<label for=\"id{$name2}\"> $name2</label></td>";
				}
                
                
			}
			else{
				$name = array_values($entry)[0];
				#echo $name;
                echo "<td><input type=\"checkbox\" name=\"setTerminals[]\" value=\"$name\" id=\"id{$name}\" \"/>";
                echo "<label for=\"id{$name}\"> $name</label></td>";
                echo "<td></td>";
			}
			#echo "<br>";
            echo "</tr>";

			$abnf ="{$abnf} {$name} | ";
			if (count($name2)>0){
				$abnf2 ="{$abnf2} {$name2} | ";
			}
            #echo "<br>";
				
		}
        echo "</table>";
        echo "<input type=\"submit\" name=\"Download\" value=\"Submit\"><br>";
        echo "</form>";
		
		
		
		
	}
	
	//here plugin if only a property is choosen
	elseif (isset($_POST['setProperty'])){
        $numberterminals = 100;
        if (isset($_POST['numberterminals'])){
    		$numberterminals = $_POST['numberterminals'];
    	}
		
		$properties = $_POST['setProperty'];
		$second_language = ($_POST['language']);

		 
		echo "<body> <div id=\"terminal-start\">";
        echo "<form action=\"getTerminals.php\" method=\"post\">";
		
		
		$result = shell_exec('python getTerminals.py ' . escapeshellarg(json_encode($properties)).' '. escapeshellarg($second_language).' '. escapeshellarg($numberterminals));
		
		$json_output = json_decode($result, true);
		$entities = array_values($json_output)[0];
		echo "<br>";
		echo "Retrieved ".count($entities)." terminals";
		echo "<br>";
		echo "<br>";
		$abnf = "#ABNF 1.0 UTF-8; \n Terminals = ";
		$abnf2 = "#ABNF 1.0 UTF-8; \n Terminals = ";
        echo "<table border=\"0\">";
        echo "<tr>";
        echo "<td><h2>en</h2></td>";
        echo "<td><h2>$second_language</h2></td>";
        echo "</tr>";
		foreach($entities as $entry){
            echo "<tr>";
			$name= "";
			$name2= "";
			if (count(array_values($entry))==2){
				$name = array_values($entry)[1];
				#echo $name;
                echo "<td><input type=\"checkbox\" name=\"setTerminals[]\" value=\"$name\" id=\"id{$name}\" \"/>";
                echo "<label for=\"id{$name}\"> $name</label></td>";
                $tmp = array_values($entry)[0];
				if (count($tmp)>0 && $tmp!=""){
					#echo " -- ".$tmp;
					$name2 = $tmp;
                    #$name = $name." -- ".$tmp;
                    echo "<td><input type=\"checkbox\" name=\"setTerminals2[]\" value=\"$name2\" id=\"id{$name2}\" \"/>";
                    echo "<label for=\"id{$name2}\"> $name2</label></td>";
				}
                
                
			}
			else{
				$name = array_values($entry)[0];
				echo "<td><input type=\"checkbox\" name=\"setTerminals[]\" value=\"$name\" id=\"id{$name}\" \"/>";
                echo "<label for=\"id{$name}\"> $name</label></td>";
                echo "<td></td>";
			}
			#echo "<br>";
            echo "</tr>";
			$abnf ="{$abnf} {$name} | ";
			if (count($name2)>0){
				$abnf2 ="{$abnf2} {$name2} | ";
			}
            #echo "<br>";
		}
        echo "</table>";
        echo "<input type=\"submit\" name=\"Download\" value=\"Submit\"><br>";
        echo "</form>";
		
		
		
	}
	
	else{
		echo "Choose a class";
	}
    
    echo "</div></body>";
	
	?>
	
  </div>
</body>
</html>
	