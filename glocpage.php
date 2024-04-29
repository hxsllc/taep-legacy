<html>
<head>
<title>Location Text and List of Documents - The Edison Papers </title>
  <script language="JavaScript">
    <?php
    include("GlocDocs.js");
    ?>
  </script>
</head>
<style type="text/css" media="all">
@import url("/new-style.css");
@import url("/namesearchstyle.css");
</style>

<body>


<div id="header"> <a class="header-ru" href="#"><span class="offscreen">Rutgers, The State University of New Jersey</span></a> <?php
    include("navigation.inc");
    ?>

<table width="750" cellpadding="0" cellspacing="0" border="0" class="searchtable"> 
    <tr valign="top"> 
        <td id="content">
<?php

foreach($_POST as $key=>$value) ${$key}=$value;
foreach($_GET as $key=>$value) ${$key}=$value;

if ($gloc)
{
    // Display Gloc information with Gloc and Gloc Filename in hiddens and Gloc location order in Hidden
    include("GlocInfoScreen.php");
    GlocInfoScreen($gloc, true);
    // Display List of Documents and Close Window buttons

    printf("<center><input type=submit value=\"Close Window\" size=10 onClick=\"javascript: self.close()\">  ");
    printf("<input type=submit value=\"List Documents\" size=10></center><br>");
    echo "</form>";
}

//display name search page
	echo"</td>";
	echo "</tr>";
	echo "</table>";
	echo "<script type='text/javascript'>function Go(){return}</script>";
	include("testfooter.php");

?>
