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
        
        function getEntities($classes,$yago,$categories,$properties,$second_language, $boolean, $d_host, $d_user, $d_pasw, $d_database){
            $terminals_language1 = array();
            $terminals_language2 = array();
            
            if (strpos($second_language,'none') != false){
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
            
            $query ="SELECT DISTINCT en,".$second_language." FROM `resourcelabel`".$from_part." WHERE ".$where_part." LIMIT 0, 10 ;";
            #echo $query;
            
            if (strpos($boolean,'AND') != false){
                $query = str_replace(" AND "," OR ",$query);
                echo "OR CASE";
            }
            
            #SELECT en FROM `resourcelabel`, (SELECT resourceid FROM `classrelation` WHERE classid = (SELECT id FROM `classes` WHERE uri='http://dbpedia.org/ontology/PopulatedPlace'))as test WHERE resourcelabel.id = test.resourceid;
            
            
            
            
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
        
        
        $entities = getEntities($classes,$yago,$categories,$properties,$second_language, $boolean, $d_host, $d_user, $d_pasw, $d_database);

    	
    	echo "<body> <div id=\"terminal-start\">";
        echo "<form action=\"getTerminals.php\" method=\"post\">";
        echo "<input type=\"submit\" name=\"Download\" value=\"Submit\"><br>";

        
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
	