<html>
<head>
<title>Location Text and List of Documents - The Edison Papers </title>
  <script LANGUAGE="JavaScript">
    <?php
    include("../NamesSearch/GlocDocs.js");
    ?>
  </script>

<style type="text/css" media="all">
@import url("../new-style.css");
@import url("namesearchstyle.css");
</style>

</head>
<body>


<div id="header"> <a class="header-ru" href="#"><span class="offscreen">Rutgers, The State University of New Jersey</span></a> <a class="header-taep" href="#"><span class="offscreen">The Thomas Edison Papers</span></a> </div>


<table width="750" cellpadding="0" cellspacing="0" border="0" class="searchtable"> 
    <tr valign="top"> 
        <td id="content">
<?php
if ($gloc)
{
    // Display Gloc information with Gloc and Gloc Filename in hiddens and Gloc location order in Hidden
    include("../NamesSearch/GlocInfoScreen.php");
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
	include("../NamesSearch/testfooter.inc");

?>