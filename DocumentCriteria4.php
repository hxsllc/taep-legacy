<html>
<?php
include("testheader.php");
foreach($_POST as $key=>$value) ${$key}=$value;
foreach($_GET as $key=>$value) ${$key}=$value;

?>

<head>
  <script language="JavaScript">
    <?php
    include("DocumentCriteria.js");
    ?>
  </script>
 <meta name="keywords" content="Thomas, Alva, Edison, Papers, Inventor, biography, invention, picture, light bulb, phonograph, new jersey, telegraph operator, tomas ,electric, movie, biography, menlo park, media research, invention, quote, time line, photo, photograph, electric company, patent">
<meta name="description" content="The Edison Papers - Making Thomas Edison Accessible to Young and Lifetime Learners">
<style type="text/css" media="all">
@import url("../new-style.css");
@import url("namesearchstyle.css");
</style>
<style>
#container {
  width: 600px;
  margin: 0 auto;
}
.drop-shadow {
  position: relative;
  float: left;
  width: 94%;
  padding: 1em;
  margin: 0.9em;
  background: #fff;
  -webkit-box-shadow: 0 1px 4px rgba(0, 0, 0, 0.3) , 0 0 40px rgba(0, 0, 0, 0.1) inset;
  -mox-box-shadow: 0 1px 4px rgba(0, 0, 0, 0.3) , 0 0 40px rgba(0, 0, 0, 0.1) inset;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.3) , 0 0 40px rgba(0, 0, 0, 0.1) inset;
}
.drop-shadow:before,
.drop-shadow:after {
  content: "";
  position: absolute;
  z-index: -2;
}
.drop-shadow p {
  font-size: 16px;
  font-weight: bold;
}
.lifted {
  -moz-border-radius: 4px;
  border-radius: 4px;
}
.lifted:before,
.lifted:after {
  bottom: 15px;
  left: 10px;
  width: 50%;
  height: 20%;
  max-width: 300px;
  max-height: 100px;
  -webkit-box-shadow: 0 15px 10px rgba(0, 0, 0, 0.7);
  -mox-box-shadow: 0 15px 10px rgba(0, 0, 0, 0.7);
  box-shadow: 0 15px 10px rgba(0, 0, 0, 0.7);
  -webkit-transform: rotate(-3deg);
  -moz-transform: rotate(-3deg);
  -ms-transform: rotate(-3deg);
  -o-transform: rotate(-3deg);
  transform: rotate(-3deg);
}
.lifted:after {
  right: 10px;
  left: auto;
  -webkit-transform: rotate(3deg);
  -moz-transform: rotate(3deg);
  -ms-transform: rotate(3deg);
  -o-transform: rotate(3deg);
  transform: rotate(3deg);
}
.curled {
  border: 1px solid #efefef;
  -moz-border-radius: 0 0 120px 120px / 0 0 6px 6px;
  border-radius: 0 0 120px 120px / 0 0 6px 6px;
}
.curled:before,
.curled:after {
  bottom: 12px;
  left: 10px;
  width: 50%;
  height: 55%;
  max-width: 200px;
  max-height: 100px;
  -webkit-box-shadow: 0 8px 12px rgba(0, 0, 0, 0.5);
  -mox-box-shadow: 0 8px 12px rgba(0, 0, 0, 0.5);
  box-shadow: 0 8px 12px rgba(0, 0, 0, 0.5);
  -webkit-transform: skew(-8deg) rotate(-3deg);
  -moz-transform: skew(-8deg) rotate(-3deg);
  -ms-transform: skew(-8deg) rotate(-3deg);
  -o-transform: skew(-8deg) rotate(-3deg);
  transform: skew(-8deg) rotate(-3deg);
}
.curled:after {
  right: 10px;
  left: auto;
  -webkit-transform: skew(8deg) rotate(3deg);
  -moz-transform: skew(8deg) rotate(3deg);
  -ms-transform: skew(8deg) rotate(3deg);
  -o-transform: skew(8deg) rotate(3deg);
  transform: skew(8deg) rotate(3deg);
}
.perspective:before {
  left: 80px;
  bottom: 5px;
  width: 50%;
  height: 35%;
  max-width: 200px;
  max-height: 50px;
  -webkit-box-shadow: -80px 0 8px rgba(0, 0, 0, 0.4);
  -mox-box-shadow: -80px 0 8px rgba(0, 0, 0, 0.4);
  box-shadow: -80px 0 8px rgba(0, 0, 0, 0.4);
  -webkit-transform: skew(50deg);
  -moz-transform: skew(50deg);
  -ms-transform: skew(50deg);
  -o-transform: skew(50deg);
  transform: skew(50deg);
  -webkit-transform-origin: 0 100%;
  -moz-transform-origin: 0 100%;
  -ms-transform-origin: 0 100%;
  -o-transform-origin: 0 100%;
  transform-origin: 0 100%;
}
.perspective:after {
  display: none;
}
.raised {
  -webkit-box-shadow: 0 15px 10px -10px rgba(0, 0, 0, 0.5) , 0 1px 4px rgba(0, 0, 0, 0.3) , 0 0 40px rgba(0, 0, 0, 0.1) inset;
  -mox-box-shadow: 0 15px 10px -10px rgba(0, 0, 0, 0.5) , 0 1px 4px rgba(0, 0, 0, 0.3) , 0 0 40px rgba(0, 0, 0, 0.1) inset;
  box-shadow: 0 15px 10px -10px rgba(0, 0, 0, 0.5) , 0 1px 4px rgba(0, 0, 0, 0.3) , 0 0 40px rgba(0, 0, 0, 0.1) inset;
}
.curved:before {
  top: 10px;
  bottom: 10px;
  left: 0;
  right: 50%;
  -webkit-box-shadow: 0 0 15px rgba(0, 0, 0, 0.6);
  -mox-box-shadow: 0 0 15px rgba(0, 0, 0, 0.6);
  box-shadow: 0 0 15px rgba(0, 0, 0, 0.6);
  -moz-border-radius: 10px / 100px;
  border-radius: 10px / 100px;
}
.curved.v2:before {
  right: 0;
}
.curved.h1:before {
  top: 50%;
  bottom: 0;
  left: 10px;
  right: 10px;
  -moz-border-radius: 100px / 10px;
  border-radius: 100px / 10px;
}
.curved.h2:before {
  top: 0;
  bottom: 0;
  left: 10px;
  right: 10px;
  -moz-border-radius: 100px / 10px;
  border-radius: 100px / 10px;
}
.rotated {
  -webkit-box-shadow: none;
  -mox-box-shadow: none;
  box-shadow: none;
  -webkit-transform: rotate(-3deg);
  -moz-transform: rotate(-3deg);
  -ms-transform: rotate(-3deg);
  -o-transform: rotate(-3deg);
  transform: rotate(-3deg);
}
.rotated > :first-child:before {
  content: "";
  position: absolute;
  z-index: -1;
  top: 0;
  bottom: 0;
  left: 0;
  right: 0;
  background: #fff;
  -webkit-box-shadow: 0 1px 4px rgba(0, 0, 0, 0.3) , 0 0 40px rgba(0, 0, 0, 0.1) inset;
  -mox-box-shadow: 0 1px 4px rgba(0, 0, 0, 0.3) , 0 0 40px rgba(0, 0, 0, 0.1) inset;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.3) , 0 0 40px rgba(0, 0, 0, 0.1) inset;
}
</style>
<link rel="stylesheet" type="text/css" href="http://edison.rutgers.edu/new-style2010.css">
</head>
<body onLoad="setName('<?php echo $namecode;?>', '<?php echo addslashes($name);?>', -2);">

<!--
<div id="header"> <a class="header-ru" href="#"><span class="offscreen">Rutgers, The State University of New Jersey</span></a> 
<div class="header-taep"> 
	<a href="/index.htm" target="_top">Project Home</a> |
	 | <a href="/NamesSearch/NamesSearch.php" target="_top">Name/Date/Doc</a> |
	 | <a href="/srchtext.htm" target="_top">Folder/Volume</a> |
	 | <a href="/srchsn.htm" target="_top">Series Notes</a> |
	 | <a href="/singldoc.htm" target="_top">Single Doc</a> |
	 | <a href="http://rutgers.edu" target="_top">Rutgers Home</a></div>
-->

<?php
include ('header.2017.php');
// Document Search HTML page
include("DocumentCriteria4.html");
?>

<?php
// retrieve all Document type in Document order
$sqlStmtNameMatch = " SELECT doc_type, description, calc_count from doc_types where doc_order is not null order by doc_order";
$result = mysqli_query($db,$sqlStmtNameMatch);

//display all Document types with a checkbox
printf("<Table cellpadding=0 cellspacing=0 border=0 style='font-size:1em;'><TR><TD align=left valign=top>");
$row = 0;
$setFlag = 0;                           // set Correspondence flag
$splitColumn = floor(mysqli_num_rows($result)/3) + 1;
while ($myrow = mysqli_fetch_row($result))
{
    $row++;
    if ($row == $splitColumn)
    {
        $row = 0;
        echo "</p></td><td align=left valign=top><p>";
    }
    // embed Correspondence into the Document Type list ()
    if ( $myrow[1] > "Correspondence" && $setFlag == 0) {
        $setFlag = 1;                   // reset the Correspondence flag
        printf("&nbsp;&nbsp;<input type=checkbox name=\"type[]\" value=%s><span style='font-size:0.8em;font-weight:100;'>%s <span style='font-size:0.75em;'>(%s)</span></span><br>\n", "01020304", "Correspondence","94,213");
    }
    if ( $myrow[0] != "04" ) {         //do not display phonograms(doc_order is not null but still skip)
        printf("&nbsp;&nbsp;<input type=checkbox name=\"type[]\" value=%s><span style='font-size:0.8em;font-weight:100;'>%s <span style='font-size:0.75em;'>(%s)</span></span><br>\n", $myrow[0], $myrow[1], $myrow[2]);
    }
}
?>
</table>
</center>
</div>
<div style="clear:both;"></div>
<input type=submit value="Find Documents" style="margin-left:1em;padding:10px;font-size:1.2em;">
<input type="button" name=clrDocsType value="Clear Document Types" onClick="clrDocs()" style="margin-left:1em;padding:10px;font-size:1.2em;" >
<p>
<?php
include("testfooter.php");
?>
