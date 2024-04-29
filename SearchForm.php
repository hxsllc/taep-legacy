<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="Thomas, Alva, Edison, Papers, Inventor, biography, invention, picture, light bulb, phonograph, new jersey, telegraph operator, tomas ,electric, movie, biography, menlo park, media research, invention, quote, time line, photo, photograph, electric company, patent" />
<meta name="description" content="The Edison Papers - Making Thomas Edison Accessible to Young and Lifetime Learners" />

<?php

error_reporting(0);

//echo 'test';
include("testheader.php");
//echo 'test';
foreach($_POST as $key=>$value) ${$key}=$value;
foreach($_GET as $key=>$value) ${$key}=$value;

if ($key1)
{
    //display all names and accent name(if exist) and document count with a match of the selected name
    $SqlStatement = "SELECT names.code as code, names.name as name, names_accent.name as name_accent, count(names_in_documents.name_code) as name_count FROM names_in_documents, names LEFT JOIN names_accent on names.code = names_accent.code where names_in_documents.name_code = names.code and names.name like '%". htmlentities(addslashes($key1)) . "%' group by names.name, names.code, name_accent ";
    $result = mysqli_query($db,$SqlStatement);
    // echo $SqlStatement;
    //set failed flag if no rows returned
    if ( mysqli_num_rows($result) == 0) {
        $failed = true;
    }
    else
    {
        echo "<body><div class='sidesearchcolumn'>Names Found</div>";
		// names should be wrapped with urlencoding to prevent quote or any other literal from being interpreted
        while ($myrow = mysqli_fetch_array($result))
        {
            // Use the accent name if exist
            if ( $myrow["name_accent"] ) {
                $name = $myrow["name_accent"];
            }
            else
            {
                $name = $myrow["name"];
            }
            printf("<p class=\"footnote\"><a href=\"%s\" target=\"NameSearchForm\" onClick=\"top.frames[1].setName('%s', '%s', -1)\"> %s </a>&nbsp;[%s]</p>\n", "SearchForm.php", $myrow["code"], rawurlencode($name),$name,$myrow["name_count"]);
        }
    }

    //display cross references with document count with a match of the selected name
    $SqlStatement = "SELECT names_2.code as code, names_2.name as name, count(names_in_documents.name_code) as name_count FROM names_in_documents, names, cross_references, names as names_2 where names.code like 'Z%' and names.code = cross_references.code and cross_references.xref = names_2.code and names_in_documents.name_code = names_2.code and names.name like '%". htmlentities(addslashes($key1)) . "%' group by names_2.name, names_2.code ";
    $result = mysqli_query($db,$SqlStatement);
    //echo $SqlStatement;
    // if rows found display Cross References
    if ( mysqli_num_rows($result) != 0)
    {
        // if main name search failed display body and reset flag
        if ($failed) {
                printf("<body><div class=\"sidesearchcolumn\">Names Found</div>");

                $failed = false;
        }
        // names should be wrapped with urlencoding to prevent quote or any other literal from being interpreted
        while ($myrow = mysqli_fetch_array($result))
        {
            // Use the accent name if exist
            if ( $myrow["name_accent"] ) {
                $name = $myrow["name_accent"];
            }
            else
            {
                $name = $myrow["name"];
            }
            printf("<p class=\"footnote\">See <a href=\"%s\" target=\"NameSearchForm\" onClick=\"top.frames[1].setName('%s', '%s', -1)\"> %s </a>&nbsp;[%s]</p>\n", "SearchForm.php", $myrow["code"], rawurlencode($name),$name,$myrow["name_count"]);
	    echo '<script type="text/javascript"> try { var pageTracker = _gat._getTracker("UA-8754894-1"); pageTracker._trackPageview(); } catch(err) {}</script>';
	}
    }
}
// if failed flag was set or starting the name search page
if (!($key1) | $failed) {
?>

<script type="text/javascript">
<!--
       // check if maximum number of names reached
       function chkMaximum()
       {
            if ( top.frames[1].nextPosition() == -1)
            {
                alert(" a maximum of four names ");
                return false;
            }
            return true;
       }
//--> 
  </script>
  <style type="text/css" media="all">
@import url("../new-style.css");
@import url("namesearchstyle.css");
</style>
</head>


<?php

    //display name search page
    printf("<body><div class=\"sidesearchcolumn\">Name Search</div>");

    printf("<form METHOD=POST onSubmit=\"return chkMaximum()\" ACTION=\"$PHP_SELF\" name=theform >");
    printf("<center><input type=text name=key1 value=\"\" size=18><br>");
    printf("<input type=submit value=\"Find Name\" ></center></form>");
	printf("<div class=\"footnote\"><p>Search names with &amp; as \"Gold\" or \"Stock\" not \"Gold &amp; Stock\".</p><p>To find an individual, enter the last name but not the first name (i.e.,  \"Anthony\" will retrieve all the names containing \"Anthony\" but \"Anthony, Frank\" will not retrieve \"Anthony, F A\").</p><p>To find a company enter principal word in the name. (i.e.,  \"mechanic\" will find \"American Institute Mechanical Engineers,\" \"American Society of Mechanical Engineers,\" and \"Mechanical News\").</p></div>");	
}
include("testfooter.php");
?>
