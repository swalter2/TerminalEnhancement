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
            mysqli_set_charset($mysqli, "utf8");
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
                $second_language = "zh";
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
        
        
        session_start();
        
        $d_host = "localhost";
        $d_user = "dbpedia";
        $d_pasw = "";
        $d_database = "dbpedia";
        
        #echo "u0391";
        #echo "<br>";
        #echo "&#0391;";
        #echo "<br>";
        
        if(isset($_POST['booleanAll'])){
            echo "<body>";
            echo "<p>";
            $second_language = ($_SESSION['language']);
            
            $abnf = "#ABNF 1.0 UTF-8; \n Language = english; \n Terminals = ";
            $abnf2 = "#ABNF 1.0 UTF-8; \n Terminals = ";
            
            if($second_language=="es"){
                $second_language = "spanish";
                $abnf2 = "#ABNF 1.0 UTF-8; \n Language = spanish; \n Terminals = ";
            }
            elseif($second_language=="el"){
                $second_language = "greek";
                $abnf2 = "#ABNF 1.0 UTF-8; \n Language = greek; \n Terminals = ";
            }
            elseif($second_language=="ru"){
                $second_language = "russian";
                $abnf2 = "#ABNF 1.0 UTF-8; \n Language = russian; \n Terminals = ";
            }
            elseif($second_language=="zh"){
                $second_language = "chinese";
                $abnf2 = "#ABNF 1.0 UTF-8; \n Language = chinese; \n Terminals = ";
            }
            elseif($second_language=="de"){
                $second_language = "german";
                $abnf2 = "#ABNF 1.0 UTF-8; \n Language = german; \n Terminals = ";
            }
            elseif ($second_language != "none"){
                $second_language = "none";
                $abnf2 = "#ABNF 1.0 UTF-8; \nTerminals = ";
            }
            
            if(isset($_SESSION['terminalsAll1'])){
                
                echo "<a href=\"terminals.txt\">Download Terminal-ABNF (english)</a>";
                
                
                $terminals = $_SESSION['terminalsAll1'];
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
            
            if(isset($_SESSION['terminalsAll2'])){
                
                echo "<a href=\"terminals2.txt\">Download Terminal-ABNF (".$second_language.")</a>";
                
                
                $terminals = $_SESSION['terminalsAll2'];
                foreach($terminals as $entry){
                    $abnf2 ="{$abnf2} {$entry} | ";
                    
                    
                }
                
                $abnf2 ="{$abnf2};";
                $abnf2 = str_replace("| ;",";",$abnf2);
                $abnf2 = str_replace("|   ","",$abnf2);
                $out = fopen('terminals2.txt', 'w') or die("can't open file");
                fwrite($out, $abnf2);
                fclose($out);
                
                
            }
            
            
            echo "</p>";
            echo "</body>";
        }
        
        
        elseif(isset($_POST['setTerminals'])|| isset($_POST['setTerminals2'])){
            
            echo "<body>";
            echo "<p>";
            $second_language = ($_SESSION['language']);
            
            $abnf = "#ABNF 1.0 UTF-8; \n Language = english; \n Terminals = ";
            $abnf2 = "#ABNF 1.0 UTF-8; \n Terminals = ";
            
            if($second_language=="es"){
                $second_language = "spanish";
                $abnf2 = "#ABNF 1.0 UTF-8; \n Language = spanish; \n Terminals = ";
            }
            elseif($second_language=="el"){
                $second_language = "greek";
                $abnf2 = "#ABNF 1.0 UTF-8; \n Language = greek; \n Terminals = ";
            }
            elseif($second_language=="ru"){
                $second_language = "russian";
                $abnf2 = "#ABNF 1.0 UTF-8; \n Language = russian; \n Terminals = ";
            }
            elseif($second_language=="zh"){
                $second_language = "chinese";
                $abnf2 = "#ABNF 1.0 UTF-8; \n Language = chinese; \n Terminals = ";
            }
            elseif($second_language=="de"){
                $second_language = "german";
                $abnf2 = "#ABNF 1.0 UTF-8; \n Language = german; \n Terminals = ";
            }
            elseif ($second_language != "none"){
                $second_language = "none";
                $abnf2 = "#ABNF 1.0 UTF-8; \nTerminals = ";
            }
            
            if(isset($_POST['setTerminals'])){
                
                echo "<a href=\"terminals.txt\">Download Terminal-ABNF (english)</a>";
                
                
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
                
                echo "<a href=\"terminals2.txt\">Download Terminal-ABNF (".$second_language.")</a>";
                
                
                $terminals = $_POST['setTerminals2'];
                foreach($terminals as $entry){
                    $abnf2 ="{$abnf2} {$entry} | ";
                    
                    
                }
                
                $abnf2 ="{$abnf2};";
                $abnf2 = str_replace("| ;",";",$abnf2);
                $abnf2 = str_replace("|   ","",$abnf2);
                $out = fopen('terminals2.txt', 'w') or die("can't open file");
                fwrite($out, $abnf2);
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
        $_SESSION['language'] = $second_language;
    	$boolean = ($_POST['boolean']);
        
        
        $entities = getEntities($classes,$yago,$categories,$properties,$second_language, $boolean, $numberterminals, $d_host, $d_user, $d_pasw, $d_database);

    	
    	echo "<body> <div id=\"terminal-start\">";
        echo "<form action=\"terminals.php\" method=\"post\">";
        echo "<input type=\"submit\" name=\"Download\" value=\"Download\"><br>";
		echo "<br>";
        echo "<input type=\"radio\" name=\"booleanAll\" value=\"YES\" id=\"yes\"/>";
        echo "<label for=\"id{and}\">Get all Terminals!     </label>";
        echo "<input type=\"radio\" name=\"booleantmp\" value=\"NO\" id=\"no\" checked=\"checked\" />";
        echo "<label for=\"id{or}\">Get only selected Terminals!</label><br>";
        echo "<br>";
		$abnf = "#ABNF 1.0 UTF-8; \n language = english; \n Terminals = ";
		$abnf2 = "#ABNF 1.0 UTF-8; \n Terminals = ";
        echo "<table border=\"0\">";
        echo "<tr>";
        echo "<td><h2>English</h2></td>";
        
        #This ignores none, and prints correct language
        if($second_language=="es"){
            echo "<td><h2>Spanish</h2></td>";
        }
        elseif($second_language=="el"){
            echo "<td><h2>Greek</h2></td>";
        }
        elseif($second_language=="ru"){
            echo "<td><h2>Russian</h2></td>";
        }
        elseif($second_language=="zh"){
            echo "<td><h2>Chinese</h2></td>";
        }
        elseif($second_language=="de"){
            echo "<td><h2>German</h2></td>";
        }
        elseif ($second_language != "none"){
            echo "<td><h2>$second_language</h2></td>";
        }
        
        echo "</tr>";
        $entities1 = $entities[0];
        $entities2 = $entities[1];
        $_SESSION['terminalsAll1'] = $entities1;
        $_SESSION['terminalsAll2'] = $entities2;
		while (($element1 = next($entities1)) !== false) {
            $element2 = next($entities2);
			$name= "";
			$name2= "";
            $name = $element1;
            #echo $name;
            echo "<td><input type=\"checkbox\" name=\"setTerminals[]\" value=\"$name\" id=\"id{$name}\" \"/>";
            echo "<label for=\"id{$name}\"> $name</label></td>";
            if ($second_language != "none"){
                $name2 = $element2;
                #if (strpos($name2,"u0")){
                #    #echo "in if";
                #    $tmp_name2 = split("u0",$name2);
                #    $name2 = "";
                #    foreach($tmp_name2 as $s){
                #        #echo $s;
                #        #echo "<br>";
                #        if (count($s)==3) $name2 = $name2."&#".$s.";";
                #        else $name2 = $name2.$s;
                #    }
                #}
                #$name2 = str_replace("u0","&#",$name2);
                
                if($name2 == ""){
                    echo "<td></td>";
                }
                else{
                    echo "<td><input type=\"checkbox\" name=\"setTerminals2[]\" value=\"$name2\" id=\"id{$name2}\" \"/>";
                    echo "<label for=\"id{$name2}\"> $name2</label></td>";
                }
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
	