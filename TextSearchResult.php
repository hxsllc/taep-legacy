<?php
include("testheader.php");
foreach($_POST as $key=>$value) ${$key}=$value;
foreach($_GET as $key=>$value) ${$key}=$value;

$sqlStmtLocation = "select gloc, group_name, item_name from locations where ";
if ($name1) {
    $sqlStmtLocation = $sqlStmtLocation . " target like '%$name1%' ";
}

if ( $name2 ) {
    switch ( $joinopr ) {
        case "AND":
            if ( $name1 ) {
                $sqlStmtLocation = $sqlStmtLocation . " and ";
            }
            $sqlStmtLocation = $sqlStmtLocation . " target like '%$name2%' ";
            break;
        case "OR":
            if ( $name1 ) {
                $sqlStmtLocation = $sqlStmtLocation . " or ";
            }
            $sqlStmtLocation = $sqlStmtLocation . " target like '%$name2%' ";
            break;
        case "NOT":
            if ( $name1 ) {
                $sqlStmtLocation = $sqlStmtLocation . " and ";
            }
            $sqlStmtLocation = $sqlStmtLocation . " target not like '%$name2%' ";
            break;
    }
}

// set the sorted order
$sqlStmtLocation = $sqlStmtLocation . " order by loc_order limit 500";

//echo $sqlStmtLocation . "<BR><BR>";

//run the query, retrieve locations that match criteria
$result = mysqli_query($db,$sqlStmtLocation);
$rowCount = mysqli_num_rows($result);

// Produce a message if more than 500 locations match criteria
if ( $rowCount == 500) {
    echo "<html>";
	echo "<head>";
	echo "<title>Too Many Search Results - The Edison Papers</title>";
	echo "</head>";
	echo "<link rel=stylesheet href=\"/new-style.css\" type=\"text/css\">";
	echo "<link rel=stylesheet href=\"/namesearchstyle.css\" type=\"text/css\">";	
	echo "<body>";	
	echo "<div id=\"header\"> <a class=\"header-ru\" href=\"#\"><span class=\"offscreen\">Rutgers, The State University of New Jersey</span></a></div>";
include("navigation.php");	
 echo "<p class=\"searchmessage\"><span class=\"important\">There are more than 500 matches.<br>Please refine your search.</span><BR>";
    echo "Press the back button to return to the Search page.</center></p>";
	echo "</body>";
    echo "</html>";
    return;
}

// Produce a message if no documents match criteria
if ( $rowCount == 0 )
{
    echo "<html>";
	echo "<head>";
	echo "<title>No Search Results - The Edison Papers</title>"; 
	echo "</head>";
	echo "<link rel=stylesheet href=\"/new-style.css\" type=\"text/css\">";
	echo "<link rel=stylesheet href=\"/namesearchstyle.css\" type=\"text/css\">";		
	echo "<script type=\"text/javascript\">
var gaJsHost = ((\"https:\" == document.location.protocol) ? \"https://ssl.\" : \"http://www.\");
document.write(unescape(\"%3Cscript src='\" + gaJsHost + \"google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E\"));
</script>
<script type=\"text/javascript\">
try {
var pageTracker = _gat._getTracker(\"UA-8495862-1\");
pageTracker._trackPageview();
} catch(err) {}</script>";
    echo "<body>";
	echo "<div id=\"header\"> <a class=\"header-ru\" href=\"#\"><span class=\"offscreen\">Rutgers, The State University of New Jersey</span></a></div>";
include("navigation.php");	

    echo "<p class=\"searchmessage\"><span class=\"important\">No Documents found.</span> <BR>";
    echo "Press the back button to return to the Search page.</center></p>";
	echo "</body>";
    echo "</html>";  
    return;
}

 ?>
<html>
<head>
<title>Descriptive Text Search Results - The Thomas Edison Papers</title><script language="JavaScript">
// check if any Location checkbox was checked, if not set - default is set all checkboxes
function chkLocs() {
var LocSelected = false;
    // check if any checkboxes are set
    for ( i = 2; i < document.SearchLocations.length ;i++ ) {
       if (document.SearchLocations.elements[i].checked)
       {
           LocSelected = true;
           break;
       }
    }
    // if no checkboxes are set, then set all checkboxes
     if (!(LocSelected))
     {
       for ( i = 2; i < document.SearchLocations.length ;i++ )
       {
          document.SearchLocations.elements[i].checked = true;
          LocSelected = true;
        }
     }
     return LocSelected;
}
// clear all checkboxes
function clrLocs() {
       for ( i = 2; i < document.SearchLocations.length ;i++ )
       {
          document.SearchLocations.elements[i].checked = false;
        }
 }
</script></head>
<style type="text/css" media="all">
@import url("../new-style.css");
@import url("namesearchstyle.css");
</style>
<body>


<div id="header"> <a class="header-ru" href="#"><span class="offscreen">Rutgers, The State University of New Jersey</span></a> </div>
<?php
include("../NamesSearch/navigation.inc");
?>


<table width="750" cellpadding="0" cellspacing="0" border="0" class="searchtable"> 
    <tr valign="top"> 
        <td id="content">
<form name=SearchLocations method=POST action="../NamesSearch/LocDetail.php"  target=_parent>
<?php

// Display the criteria
echo "<H3>Search for \"";
$criteria = "";
if ( $name1 ) {
    $criteria = $name1 . " " ;
}
if ( $joinopr && $name2 ) {
    $criteria = $criteria . $joinopr . " " . $name2;
}
echo htmlspecialchars(urldecode($criteria)) . "\"";
echo "<input name=criteria type=\"hidden\" value=\"$criteria\" >";

// Display Location count
echo "</H3><H5>" . $rowCount . " Locations found </H5>";

echo "<I><FONT SIZE=-1><center>Use the checkboxes to select the locations whose editorial text you would like to see, then click the \"Show Text\" button,";
echo "to select them all, just click the button. Clicking an individual location's name will retrieve that location's text and list of documents.</center></FONT></I>";
echo "<BR><center><input type=\"button\" name=clrDocs value=\"Clear Checkboxes\" onClick=\"parent.clrLocs()\" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=submit name=showLoc value=\"Show Text\" onClick=\"return chkLocs()\" ></center><BR>";

include("../NamesSearch/LocationForm.php");
LocationForm($result);

// end form
echo "</form>";

// echo "complete";
	echo"</td>";
	echo "</tr>";
	echo "</table>";
	include("../NamesSearch/testfooter.inc");
?>
