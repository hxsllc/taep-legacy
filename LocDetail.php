<html>
<head>
<title>Editorial Text - The Thomas Edison Papers</title>
<?php

include("testheader.php");
foreach($_POST as $key=>$value) ${$key}=$value;
foreach($_GET as $key=>$value) ${$key}=$value;

// propogate through HTTP_POST_VARS extracting location array
// use the values to build the where criteria for the location selection
while ( list( $key, $value) = each ( $HTTP_POST_VARS ))
{
  // if the value is location gloc array
  if (is_array($value))
  {
    // build Location Gloc criteria from Gloc array
    $strLocList = " in ( ";
    while ( list( $key2, $value2) = each ( $value))
    {
        $strLocList .= "'$value2',";
    }
    $commaPos = strrpos($strLocList , ",");
    $strLocList  = substr($strLocList , 0, $commaPos) . ")";
  }
}

$sqlStmtLocation = "select gloc, group_name, item_name, target, credit_line from locations where gloc $strLocList order by loc_order ";

// echo $sqlStmtLocation . "<BR><BR>";

//run the query, retrieve locations that match criteria
$result = mysqli_query($db,$sqlStmtLocation);
?>

<body background="/webimages/background.gif">
<style type="text/css" media="all">
@import url("../new-style.css");
@import url("namesearchstyle.css");
</style>
<body>


<div id="header"> <a class="header-ru" href="#"><span class="offscreen">Rutgers, The State University of New Jersey</span></a> </div>
		
        

<?php
    include("navigation.inc");
// display criteria
echo "<H3>Search for \"";
echo $criteria . "\"</H3>";

echo "<p>Clicking on a location name will show that location's text and list of Documents.</p>";

// loop through in Location Info
while ($myrow = mysqli_fetch_row($result))
{
    echo "  <p><a target=_blank href=\"http://edison.rutgers.edu/NamesSearch/glocpage.php?gloc=" . urlencode($myrow[0]) . "&\"><IMG ALIGN=bottom BORDER=0 SRC=\"http://edison.rutgers.edu/NamesSearch/graphics/OpenFolder.gif\" >($myrow[0])&nbsp;" . $myrow[1] . "&nbsp;" . $myrow[2] . "</a></p> ";

    echo $myrow[3];
}


	include("../NamesSearch/testfooter.inc");
?>
