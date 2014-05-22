<?php
    header('Content-Type: text/html; charset=utf-8');
    
    ?>

<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
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
    

    
    function getClasses($resource, $d_host, $d_user, $d_pasw, $d_database){
        $return_value = array();
        $return_number = array();
        $mysqli = new mysqli($d_host, $d_user, $d_pasw, $d_database);
        mysqli_set_charset($mysqli, "utf8");
        /* check connection */
        if ($mysqli->connect_errno) {
            printf("Connect failed: %s\n", $mysqli->connect_error);
            exit();
        }
        else{
            $from_part = "";
            $where_part = "";
            $counter = 0;
            foreach($resource as $res){
                $from_part = $from_part . ",(SELECT classid FROM `classrelation` WHERE resourceid = (SELECT id FROM `resourcelabel` WHERE en='".$res."')) as test{$counter}";
                $where_part = $where_part . "classes.id = test{$counter}.classid AND ";
                $counter += 1;
                
            }
            $where_part = substr($where_part, 0, -4);
            
            $query ="SELECT DISTINCT uri FROM `classes`".$from_part." WHERE ".$where_part.";";
            #echo $query;
            #echo "<br>";
            if ($result = $mysqli->query($query)) {
                foreach($result as $r){
                    $uri = implode('',$r);
                    if (strpos($uri,'Thing') == false and strpos($uri,'schema') == false and strpos($uri,'foaf') == false and strpos($uri,'ontology') == true){
                        array_push($return_value, $r);
                    }
                    
                }
                /* free result set */
                $result->close();
            
            }
            foreach($return_value as $r){
                $uri = implode('',$r);
                $query = "SELECT DISTINCT COUNT(resourceid) FROM `classrelation`,(SELECT id FROM `classes` WHERE uri='".$uri."') as test WHERE classrelation.classid = test.id;";
                if ($result = $mysqli->query($query)) {
                    foreach($result as $z){
                        array_push($return_number, $z);
                        #echo implode('',$z);
                        #echo "<br>";
                    }
                    $result->close();
                    
                }
                else{
                   array_push($return_number, 0);
                }
                
            }
            
            
        }
        
        mysqli_close($mysqli);
        return array($return_value,$return_number);
    }
    
    
    function getYago($resource, $d_host, $d_user, $d_pasw, $d_database){
        $return_value = array();
        $return_number = array();
        $mysqli = new mysqli($d_host, $d_user, $d_pasw, $d_database);
        mysqli_set_charset($mysqli, "utf8");
        /* check connection */
        if ($mysqli->connect_errno) {
            printf("Connect failed: %s\n", $mysqli->connect_error);
            exit();
        }
        else{
            $from_part = "";
            $where_part = "";
            $counter = 0;
            foreach($resource as $res){
                $from_part = $from_part . ",(SELECT classid FROM `yagorelation` WHERE resourceid = (SELECT id FROM `resourcelabel` WHERE en='".$res."')) as test{$counter}";
                $where_part = $where_part . "yago.id = test{$counter}.classid AND ";
                $counter += 1;
            
            }
            $where_part = substr($where_part, 0, -4);
        
            $query ="SELECT DISTINCT uri FROM `yago`".$from_part." WHERE ".$where_part.";";
            #echo $query;
            #echo "<br>";
            if ($result = $mysqli->query($query)) {
                foreach($result as $r){
                    $uri = implode('',$r);
                    if (strpos($uri,'PhysicalEntity') == false and strpos($uri,'Object') == false and strpos($uri,'YagoLegalActorGeo') == false and strpos($uri,'YagoGeoEntity') == false and strpos($uri,'YagoPermanentlyLocatedEntity') == false and strpos($uri,'10') == false){
                        array_push($return_value, $r);
                    }
            }
            /* free result set */
            $result->close();
            
        }
            foreach($return_value as $r){
                $uri = implode('',$r);
                $query = "SELECT DISTINCT COUNT(resourceid) FROM `yagorelation`,(SELECT id FROM `yago` WHERE uri='".$uri."') as test WHERE yagorelation.classid = test.id;";
                #echo $query;
                #echo "<br>";
                if ($result = $mysqli->query($query)) {
                    foreach($result as $z){
                        array_push($return_number, $z);
                    }
                    $result->close();
                    
                }
                else{
                    array_push($return_number, 0);
                }
                
            }
            
            
        }
        
        mysqli_close($mysqli);
        return array($return_value,$return_number);
    }
    
    
    function getCategory($resource, $d_host, $d_user, $d_pasw, $d_database){
        $return_value = array();
        $return_number = array();
        $mysqli = new mysqli($d_host, $d_user, $d_pasw, $d_database);
        mysqli_set_charset($mysqli, "utf8");
        /* check connection */
        if ($mysqli->connect_errno) {
            printf("Connect failed: %s\n", $mysqli->connect_error);
            exit();
        }
        else{
            $from_part = "";
            $where_part = "";
            $counter = 0;
            foreach($resource as $res){
                $from_part = $from_part . ",(SELECT categoryid FROM `categoryrelation` WHERE resourceid = (SELECT id FROM `resourcelabel` WHERE en='".$res."')) as test{$counter}";
                $where_part = $where_part . "categorylabel.id = test{$counter}.categoryid AND ";
                $counter += 1;
                
            }
            $where_part = substr($where_part, 0, -4);
            
            $query ="SELECT DISTINCT uri FROM `categorylabel`".$from_part." WHERE ".$where_part.";";
            #echo $query;
            #echo "<br>";
            if ($result = $mysqli->query($query)) {
                foreach($result as $r){
                    array_push($return_value, $r);
                }
                /* free result set */
                $result->close();
                
            }
            foreach($return_value as $r){
                $uri = implode('',$r);
                #echo $uri;
                #echo "<br>";
                $query = "SELECT DISTINCT COUNT(categoryid) FROM `categoryrelation`,(SELECT id FROM `categorylabel` WHERE uri='".$uri."') as test WHERE categoryrelation.categoryid = test.id;";
                #echo $query;
                #echo "<br>";
                if ($result = $mysqli->query($query)) {
                    foreach($result as $z){
                        array_push($return_number, $z);
                    }
                    $result->close();
                    
                }
                else{
                    array_push($return_number, 0);
                }
                
            }
            
            
        }
        
        mysqli_close($mysqli);
        return array($return_value,$return_number);
    }
    
    function getProperty($resource, $d_host, $d_user, $d_pasw, $d_database){
        $return_value = array();
        $mysqli = new mysqli($d_host, $d_user, $d_pasw, $d_database);
        mysqli_set_charset($mysqli, "utf8");
        /* check connection */
        if ($mysqli->connect_errno) {
            printf("Connect failed: %s\n", $mysqli->connect_error);
            exit();
        }
        else{
            $from_part = "";
            $where_part = "";
            $counter = 0;
            foreach($resource as $res){
                $from_part = $from_part . ",(SELECT propertyid FROM `propertyrelation` WHERE subjectid = (SELECT id FROM `resourcelabel` WHERE en='".$res."')) as test{$counter}";
                $where_part = $where_part . "property.id = test{$counter}.propertyid AND ";
                $counter += 1;
                
            }
            $where_part = substr($where_part, 0, -4);
            
            $query ="SELECT DISTINCT uri FROM `property`".$from_part." WHERE ".$where_part.";";
            #echo $query;
            #echo "<br>";
            if ($result = $mysqli->query($query)) {
                foreach($result as $r){
                    $uri = implode('',$r);
                    if (strpos($uri,'ontology') == true ){
                        array_push($return_value, $r);
                    }
                }
                /* free result set */
                $result->close();
                
            }
        }
        
        mysqli_close($mysqli);
        return $return_value;
    }
    
    

    
    
    $startpage = false;
    $d_host = "localhost";
    $d_user = "dbpedia";
    $d_pasw = "";
    $d_database = "dbpedia";

    
    
    if (! $startpage) {
        $res = $_GET['resourcelist'];
        
        $res = str_replace(", ",",",$res);
        
        $data = explode(",", $res);
        
        #run SQL
        $classes = getClasses($data, $d_host, $d_user, $d_pasw, $d_database);
        $yago = getYago($data, $d_host, $d_user, $d_pasw, $d_database);
        $categories = getCategory($data, $d_host, $d_user, $d_pasw, $d_database);
        $properties = getProperty($data, $d_host, $d_user, $d_pasw, $d_database);
        
        
        #### Output ####
        
        #classes
        echo "<body> <div id=\"terminal-start\">";
        echo "<form action=\"terminals.php\" method=\"post\">";
        echo "<br>";
        echo "<br>";
        echo "Common dbpedia classes:";
        echo "<br>";
        
        
        $class = $classes[0];
        $class_number = $classes[1];
        #echo count($class);
        echo "<br>";
        if(empty($class)){
            echo "No DBbedia classes were found.";
        }
        

            for($a = 0; $a < count($class); $a++){
                #while (($element1 = next($class)) !== false) {
                #$element2 = next($class_number);
                $element1 = $class[$a];
                $element2 = $class_number[$a];
                $uri = implode('',$element1);
                $value = implode('',$element2);
                $out = $uri;
                #echo $uri;
                $replaceOntology = "http://dbpedia.org/ontology/";
                $out = str_replace($replaceOntology,"",$out);
                echo "<input type=\"checkbox\" name=\"setClass[]\" value=\"$uri\" id=\"id{ $uri}\"\"/>";
                echo "<label for=\"id{$uri}\"> $out </label><font color=\"grey\">(".$value.")</font><br>";
                
            }
        
		
        
            
        echo "<br>";
        echo "<br>";
        echo "Common yago classes:";
        echo "<br>";
        $yagoclass = $yago[0];
        $yago_number = $yago[1];
        if(empty($yagoclass)){
            echo "No YAGO classes were found.";
        }
        
            
        for($a = 0; $a < count($yagoclass); $a++){
            #while (($element1 = next($class)) !== false) {
            #$element2 = next($class_number);
            $element1 = $yagoclass[$a];
            $element2 = $yago_number[$a];
            $uri = implode('',$element1);
            $value = implode('',$element2);
            $out = $uri;
            $replaceYago = "http://dbpedia.org/class/yago/";
            $out = str_replace($replaceYago,"",$out);
            echo "<input type=\"checkbox\" name=\"setYago[]\" value=\"$uri\" id=\"id{ $uri}\"\"/>";
            echo "<label for=\"id{$uri}\">$out </label><font color=\"grey\">(".$value.")</font><br>";
            
        }
        
        
        echo "<br>";
        echo "<br>";
        echo "Common categories:";
        echo "<br>";
        $categoryclass = $categories[0];
        $category_number = $categories[1];
        if(empty($categoryclass)){
            echo "No categories were found.";
        }

        for($a = 0; $a < count($categoryclass); $a++){
            #while (($element1 = next($class)) !== false) {
            #$element2 = next($class_number);
            $element1 = $categoryclass[$a];
            $element2 = $category_number[$a];
            $uri = implode('',$element1);
            $value = implode('',$element2);
            $out = $uri;
            $replaceOntology = "http://dbpedia.org/ontology/";
            $replaceProperty = "http://dbpedia.org/property/";
            $replaceResource= "http://dbpedia.org/resource/";
            $out = str_replace($replaceProperty,"",$out);
            $out = str_replace($replaceOntology,"",$out);
            $out = str_replace($replaceResource,"",$out);
            $out = str_replace("_"," ",$out);
            
            echo "<input type=\"checkbox\" name=\"setCategory[]\" value=\"$uri\" id=\"id{ $uri}\" \"/>";
            echo "<label for=\"id{$uri}\"> $out</label><font color=\"grey\">(".$value.")</font><br>";
            
        }
        
        
        echo "<br>";
        echo "<br>";
        echo "<br>";
        echo "<br>";
        
        
        
        echo "Common properties:";
        echo "<br>";
        if(empty($properties)){
            echo "No properties were found.";
        }
        foreach($properties as $entry){
            $uri = implode('',$entry);
            $out = $uri;
            if (strpos($uri,'ontology') != false){
                $replaceOntology = "http://dbpedia.org/ontology/";
                $replaceProperty = "http://dbpedia.org/property/";
                $replaceResource= "http://dbpedia.org/resource/";
                $out = str_replace($replaceProperty,"",$out);
                $out = str_replace($replaceOntology,"",$out);
                $out = str_replace($replaceResource,"",$out);
                $out = str_replace("with","",$out);
                
                echo "<input type=\"checkbox\" name=\"setProperty[]\" value=\"$uri\" id=\"id{ $uri}\" \"/>";
                echo "<label for=\"id{$uri}\"> $out</label><br>";
            }
            
            
        }
        
        echo "<br>";
        echo "<br>";
        echo "<br>";
        echo "<br>";
        
        echo "<br>";
        echo "Get all terminals, which <br>";
        echo "<input type=\"radio\" name=\"boolean\" value=\"AND\" id=\"and\" checked=\"checked\"/>";
        echo "<label for=\"id{and}\">fulfill all above constraints</label><br>";
        echo "<input type=\"radio\" name=\"boolean\" value=\"OR\" id=\"or\"/>";
        echo "<label for=\"id{or}\">are at least in one of the properties, classes or categories (long runtime)</label><br>";
        echo "<br>";
        echo "Note that one terminal can be part of different classes and categories.";
        echo "<br>";
        echo "<br>";
        echo "<br>";
        
       
        
        
        
       
        
        #echo "The name of the entities will be in English,<br>";
        echo "Additional language for terminals (default is English):<br>";
        #make english default and with optional one other language possible
        echo "<input type=\"radio\" name=\"language\" value=\"none\" id=\"none\" checked=\"checked\"/>";
        echo "<label for=\"id{en}\">None</label><br>";
        echo "<input type=\"radio\" name=\"language\" value=\"de\" id=\"de\"/>";
        echo "<label for=\"id{de}\">German</label><br>";
        echo "<input type=\"radio\" name=\"language\" value=\"el\" id=\"fr\"/>";
        echo "<label for=\"id{el}\">Greek</label><br>";
        echo "<input type=\"radio\" name=\"language\" value=\"es\" id=\"es\"/>";
        echo "<label for=\"id{es}\">Spanish</label><br>";
        echo "<input type=\"radio\" name=\"language\" value=\"zh\" id=\"zh\"\"/>";
        echo "<label for=\"id{zh}\">Chinese</label><br>";
        echo "<input type=\"radio\" name=\"language\" value=\"ru\" id=\"ru\"/>";
        echo "<label for=\"id{ru}\">Russian</label><br>";
        echo "<br>";
        echo "<br>";
        echo "Maximal number of terminals: ";
        echo "<input id=\"numberterminals\" type=\"text\" name=\"numberterminals\" size=\"10\" value=\"100\" maxlength=\"10\" />";
        echo "<br>";
        echo "(default is 100)";
        echo "<br>";
        echo "<br>";
        echo "<input type=\"submit\" name=\"submit\" value=\"Submit\"><br>";
        #echo "Returning the entities in the prefered language, might need some time!";
        echo "</form>";
        
        echo "</div></body>";
        
	}
    ?> 


</html>
