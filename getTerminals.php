	<?php
	echo "Please wait - this might take a moment!";
	echo "<p>";
	echo "<a href=\"terminals.txt\">Download ABNF</a>";
	echo "</p>";
	$classes = explode(",", classes);
	$properties = explode(",", $properties);
	$result = shell_exec('getTerminals.py ' . escapeshellarg(json_encode($classes)) . escapeshellarg(json_encode($properties)));
	$json_output = json_decode($result, true);
	print_r($json_output)
	
	?>