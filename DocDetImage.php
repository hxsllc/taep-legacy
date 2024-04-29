<?php
error_reporting(0);
session_start();
?>
<html>
<?php

$_SESSION['getString']='';
$_SESSION['strDocType']='';
$getString = "";                        // initialize the GET string

// retrieve the following values from HTTP_POST_VARS
// build Document ID list string and if called from List of Documents( no Reporting Sorting order) set getstring
//while ( list( $key, $value) = each ( $HTTP_POST_VARS))
//print_r($_POST);
//eg: Array ( [showDoc] => Show Documents [type] => Array ( [0] => SB1677131A ) [spaceholder] => ) 

foreach($_POST as $key=>$value) ${$key}=$value;
foreach($_GET as $key=>$value) ${$key}=$value;

foreach($_POST as $key=>$value)
{
  // if the value is array(document Id array)
  if (is_array($value))
  {
    // build Document ID string from document ID array
    //$strDocType = " in ( ";
    //while ( list( $key2, $value2) = each ( $value))
	foreach($value as $key2=>$value2)
    {
      $strDocType .= "'$value2',";
    }
    $commaPos = strrpos($strDocType, ",");
    //$strDocType = substr($strDocType, 0, $commaPos) . ")";
    $strDocType = substr($strDocType, 0, strlen($strDocType)-1);
  }
  else
  {
    // Check if from List of Documents( no Reporting Sorting order)
    if ( $key == "norpttype") {
        $getString = $getString . $key . "=" . urlencode($value) . "&";
    }
   }
 }
 
global $DocIds;
$DocIds = $strDocType;
//$strDocType=base64_encode($strDocType);

$_SESSION['getString']=$getString;
$_SESSION['strDocType']=$strDocType;
session_write_close();
?>
 
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <script language="JavaScript">
    <!--
    // Placed the Document ID String List into JavaScript global Document Id String
    var strDocIds = <?php echo "\"" . $strDocType . "\""; ?>;
    //-->
    </script>
</head>
  <title title="Document Images"></title> 
  <frameset cols="200,*" frameborder="yes" framespacing=1>
    <frame name="SearchResult" src="CondensedSearchResult.php" marginheight=10 marginwidth=5 scrolling=auto>    
    <frameset rows="300,*" frameborder="yes" framespacing=1>
        <frame name="ImageResult" src="blank.php" marginheight=20 marginwidth=20 scrolling=auto>
        <frame name="ImagePicture" src="blank.html" marginheight=20 marginwidth=20 scrolling=auto>
    </frameset>
  </frameset><noframes></noframes>
</html>
