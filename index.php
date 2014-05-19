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

<body>
<div id="terminal-start">
Please add at least one or more resources using DBpedia label and seperated by a comma, into the textbox below!<br><br>
Examples are:<br>
Thessaloniki,Athens<br>
Hong Kong dollar,Euro<br>
Hong Kong dollar,Liberty Dollar,Austrian krone,Euro<br>
The Matrix,Keanu Reeves<br>
Max Planck Society,Bielefeld University<br>
BMW,Volkswagen<br>
Facebook,Yahoo!<br>
University of Paderborn,Bielefeld University<br>
etc.<br><br><br>

<span class="titel">Linked Data based Terminal Enhancement</span><br /><br />
<form action="result.php" method="GET">
<input id="start" type="text" name="resourcelist" size="80" maxlength="80" />
<input type="submit" value="Start" />
</form>
</div>
</body>