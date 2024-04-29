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
include("DocumentCriteria.html");
?>

<?php
// retrieve all Document type in Document order
$sqlStmtNameMatch = "SELECT doc_type, description from doc_types where doc_order is not null order by doc_order";
$result = mysqli_query($db,$sqlStmtNameMatch);

//display all Document types with a checkbox
printf("<Table cellpadding=0 cellspacing=0 border=0><TR><TD align=left valign=top><p>");
$row = 0;
$setFlag = 0;                           // set Correspondence flag
$splitColumn = floor(mysqli_num_rows($result)/2) + 1;
while ($myrow = mysqli_fetch_row($result))
{
    $row++;
    if ($row == $splitColumn)
    {
        $row = 0;
        echo "</p></td><td align=left valign=top><p>";
    }
    // embed Correspondence into the Document Type list
    if ( $myrow[1] > "Correspondence" && $setFlag == 0) {
        $setFlag = 1;                   // reset the Correspondence flag
        printf("&nbsp;&nbsp;<input type=checkbox name=\"type[]\" value=%s>%s<BR>\n", "01020304", "Correspondence");
    }
    if ( $myrow[0] != "04" ) {         //do not display phonograms(doc_order is not nullbut still skip)
        printf("&nbsp;&nbsp;<input type=checkbox name=\"type[]\" value=%s>%s<BR>\n", $myrow[0], $myrow[1]);
    }
}
printf("</p></td></tr></table></center><BR><center><input type=submit value=\"Find Documents\"></center></form> ");
include("testfooter.php");
?>
