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
        
        function getEntities($classes,$yago,$categories,$properties,$second_language, $boolean, $numberterminals, $d_host, $d_user, $d_pasw, $d_database){
            
            $mysqli = new mysqli($d_host, $d_user, $d_pasw, $d_database);
            if ($mysqli->connect_errno) {
                printf("Connect failed: %s\n", $mysqli->connect_error);
                exit();
            }
            else{
            
            $terminals_language1 = array();
            $terminals_language2 = array();
            
                #echo $second_language;
                #echo "<br>";
            if ($second_language =='none'){
                $second_language = "en";
            }
            
            
            $from_part = "";
            $where_part = "";
            $counter = 0;
            
            foreach($classes as $res){
                $from_part = $from_part . ",(SELECT resourceid FROM `classrelation` WHERE classid = (SELECT id FROM `classes` WHERE uri='".$res."')) as test{$counter}";
                $where_part = $where_part . "resourcelabel.id = test{$counter}.resourceid AND ";
                $counter += 1;
            }

            
            foreach($yago as $res){
                $from_part = $from_part . ",(SELECT resourceid FROM `yagorelation` WHERE classid = (SELECT id FROM `yago` WHERE uri='".$res."')) as test{$counter}";
                $where_part = $where_part . "resourcelabel.id = test{$counter}.resourceid AND ";
                $counter += 1;
            }

            
            foreach($categories as $res){
                $from_part = $from_part . ",(SELECT resourceid FROM `categoryrelation` WHERE categoryid = (SELECT id FROM `categorylabel` WHERE uri='".$res."')) as test{$counter}";
                $where_part = $where_part . "resourcelabel.id = test{$counter}.resourceid AND ";
                $counter += 1;
            }
            
            
            $where_part = substr($where_part, 0, -4);
            
            $query ="SELECT DISTINCT en,".$second_language." FROM `resourcelabel`".$from_part." WHERE ".$where_part." LIMIT 0, {$numberterminals} ;";
                #echo "generated query";
            
            if ($boolean== 'OR'){
                $query = str_replace(" AND "," OR ",$query);
                #echo "OR CASE";
            }
            
                #echo $query;
                #echo "<br>";
            
            if ($result = $mysqli->query($query)) {
                foreach($result as $r){
                    $test = array_values($r)[0];
                    array_push($terminals_language1, $test);
                    #echo $test;
                    #echo "<br>";
                    $test2 = array_values($r)[1];
                    array_push($terminals_language2, $test2);
                }
                    /* free result set */
                $result->close();
                    
            }
                
            mysqli_close($mysqli);
            }
            
            return array($terminals_language1,$terminals_language2);
        }
        
        
        
        $d_host = "localhost";
        $d_user = "dbpedia";
        $d_pasw = "";
        $d_database = "dbpedia";
        
        
        
        
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
        
	elseif (isset($_POST['setClass']) or isset($_POST['setProperty']) or isset($_POST['setYago']) or isset($_POST['setCategory'])){
        $numberterminals = 100;
        if (isset($_POST['numberterminals'])){
    		$numberterminals = $_POST['numberterminals'];
    	}
        
    	$properties = array();
    	if (isset($_POST['setProperty'])){
    		$properties = $_POST['setProperty'];
    	}
        
        $classes = array();
    	if (isset($_POST['setClass'])){
    		$classes = $_POST['setClass'];
    	}
        
        $yago = array();
    	if (isset($_POST['setYago'])){
    		$yago = $_POST['setYago'];
    	}
        
        $categories = array();
    	if (isset($_POST['setCategory'])){
    		$categories = $_POST['setCategory'];
    	}

    	$second_language = ($_POST['language']);
    	$boolean = ($_POST['boolean']);
        
        
        $entities = getEntities($classes,$yago,$categories,$properties,$second_language, $boolean, $numberterminals, $d_host, $d_user, $d_pasw, $d_database);

    	
    	echo "<body> <div id=\"terminal-start\">";
        echo "<form action=\"getTerminals.php\" method=\"post\">";
        echo "<input type=\"submit\" name=\"Download\" value=\"Submit\"><br>";

        
		echo "<br>";
		#echo "Retrieved ".count(array_values($entities)[1])." terminals";
		echo "<br>";
		echo "<br>";
		$abnf = "#ABNF 1.0 UTF-8; \n Terminals = ";
		$abnf2 = "#ABNF 1.0 UTF-8; \n Terminals = ";
        echo "<table border=\"0\">";
        echo "<tr>";
        echo "<td><h2>en</h2></td>";
        echo "<td><h2>$second_language</h2></td>";
        echo "</tr>";
        $entities1 = $entities[0];
        $entities2 = $entities[1];
		while (($element1 = next($entities1)) !== false) {
            $element2 = next($entities2);
			$name= "";
			$name2= "";
            $name = $element1;
            #echo $name;
            echo "<td><input type=\"checkbox\" name=\"setTerminals[]\" value=\"$name\" id=\"id{$name}\" \"/>";
            echo "<label for=\"id{$name}\"> $name</label></td>";
            $name2 = $element2;
            if($name2 == ""){
                echo "<td></td>";
            }
            else{
                echo "<td><input type=\"checkbox\" name=\"setTerminals2[]\" value=\"$name2\" id=\"id{$name2}\" \"/>";
                echo "<label for=\"id{$name2}\"> $name2</label></td>";
            }
            

            echo "</tr>";
            
			$abnf ="{$abnf} {$name} | ";
			if (count($name2)>0){
            		$abnf2 ="{$abnf2} {$name2} | ";
			}
            #echo "<br>";
				
		}
        echo "</table>";
        echo "</form>";
		
		
		
		
	}
	
        else{
            echo "You need to select something!!!!!";
        }
    echo "</div></body>";
	
	?>
	
  </div>
</body>
</html>
	