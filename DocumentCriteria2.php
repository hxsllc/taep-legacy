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

</head>



<?php
// Document Search HTML page
include("DocumentCriteria2.html");
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
        printf("&nbsp;&nbsp;<input type=checkbox name=\"type[]\" value=%s>%s <span style='font-size:0.75em;'>(%s)</span><BR>\n", "01020304", "Correspondence","94,213");
    }
    if ( $myrow[0] != "04" ) {         //do not display phonograms(doc_order is not null but still skip)
        printf("&nbsp;&nbsp;<input type=checkbox name=\"type[]\" value=%s>%s <span style='font-size:0.75em;'>(%s)</span><BR>\n", $myrow[0], $myrow[1], $myrow[2]);
    }
}
?>
</table>
</center>
<input type=submit value="Find Documents" style="padding:15px;font-size:1.4em;">
<br><br>
<input type="button" name=clrDocsType value="Clear Document Types" onClick="clrDocs()" style="padding:10px;" >

<?php
include("testfooter.php");
?>
