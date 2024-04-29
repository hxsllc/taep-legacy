<?php

error_reporting(0);

include("testheader.php");
foreach($_POST as $key=>$value) ${$key}=$value;
foreach($_GET as $key=>$value) ${$key}=$value;

$numNames = 0;                          // number of name codes

// propogate through HTTP_POST_VARS checking all post values and arrays
// use the values to build the where criteria for the document selection
//while ( list( $key, $value) = each ( $_POST))
foreach($_POST as $key=>$value) ${$key}=$value;
foreach($_POST as $key=>$value)
{
	//echo $key;
  // if the value is array(document type list array)
  if (is_array($value))
  {
    // build Document type criteria from document type array
    $strDocType = " in ( ";
    //while ( list( $key2, $value2) = each ( $value))
	foreach($value as $key2=>$value2)
    {
      if ( $value2 == "01020304") {     // check if Correspondence, then set to 01- 04 Document Types
            $strDocType .= "'01','02','03','04',";
      }
      else
      {
            $strDocType .= "'$value2',";
      }
    }
    $commaPos = strrpos($strDocType, ",");
    $strDocType = substr($strDocType, 0, $commaPos) . ")";
  }
  // non array values like name code and date range and Document Sort Type
  else
  {
    // check if namecode name/value pair actually has a value
    if ($value)
    {
      // if the key is name code, use name code value to build the name criteria array
      if (strstr($key, "namecode"))
      {
        $arrNameCode[] = "'$value'";
        $numNames++;
      }
      else
      {
        // if the key is actual name , use name value to build the names array
        if (strstr($key, "name"))
        {
            $arrNames[] = "$value";
        }
        else
        // if the key is start date, use the start date value to build the start date criteria (extra logic to insert missing zero)
        {
            if (strstr($key, "start"))
            {
                $FirstDate = $value;
                $firstSlash = strpos($value, "/");
                $secondSlash = strrpos($value, "/");
                $strStartDate = (substr(trim($value) . "00", $secondSlash + 1,2) > "50" ? "18" : "19") . substr(trim($value) . "00", $secondSlash + 1,2) . substr("0" . substr($value, 0, $firstSlash ),-2, 2) . substr("0" . substr($value, $firstSlash + 1 , $secondSlash - $firstSlash - 1),-2,2);
                $strStartDate = " document_records.doc_date = '$strStartDate'";
            }
            else
            // if the key is end date, use the end date value to build the end date criteria (extra logic to insert missing zero)
            {
                if (strstr($key, "end"))
                {
                    $SecondDate = $value;
                    $strStartDate = str_replace("=", ">=", $strStartDate);
                    $firstSlash = strpos($value, "/");
                    $secondSlash = strrpos($value, "/");
                    $strEndDate = (substr(trim($value) . "00", $secondSlash + 1,2) > "50" ? "18" : "19") . substr(trim($value) . "00", $secondSlash + 1,2) . substr("0" . substr($value, 0, $firstSlash  ), -2, 2) . substr("0" . substr($value, $firstSlash + 1 , $secondSlash - $firstSlash - 1),-2 ,2);
                    $strEndDate = " document_records.doc_date <= '$strEndDate'";
                }
                else
                // the default key,  will set the document sorting order
                {
                    switch ($rpttype) {
                        case "RecordsByFilm":
                            $strOrderBy = "order by reel, frame, document_records.document_id";
                            break;
                        case "RecordsByType":
                            $strOrderBy = "order by doc_types.doc_order, document_records.doc_date, document_records.document_id";
                            $noCorrType = true;
                            break;
                        default:
                            $strOrderBy = "order by document_records.doc_date, document_records.document_id";
                            break;
                    }
                }
            }
        }
      }
    }
  }
}

// if the number of names greater 1, then use a more effecient SQL using Name_in_documents as alias
// Sql used to select name matches in Documents
switch ($numNames) {
    case 0:
        // Use a simple SQL statement, If no names selected (1=1 simplifies building sql statement with and expression
        $sqlStmtDocument = "select document_records.document_id from document_records where 1=1 ";
        break;
    case 1:
        // SQL statement to retrieve document ids based on single name code criteria
        $sqlStmtDocument = "select document_records.document_id from  names_in_documents as NAMES1, document_records where document_records.document_id = NAMES1.document_id and NAMES1.name_code = " . $arrNameCode[0];
        break;
    default:
        // SQL statement to retrieve document ids based on name code criteria for Multiple names code
        $sqlStmtDocument = "select document_records.document_id from  names_in_documents as NAMES1, ";

        // build a dynamic From clause
        for ( $i=1; $numNames>$i; ++$i) {
            $sqlStmtDocument = $sqlStmtDocument . " names_in_documents as NAMES" . sprintf("%d", $i+1) . ",";
        }
        $commaPos = strrpos($sqlStmtDocument, ",");
        $sqlStmtDocument = substr($sqlStmtDocument, 0, $commaPos) . ", document_records where document_records.document_id = NAMES1.document_id and NAMES1.name_code = " . $arrNameCode[0];

        // build a dynamic where clause
        for ( $i=1; $numNames>$i; ++$i) {
            $sqlStmtDocument = $sqlStmtDocument . " and NAMES" . sprintf("%d", $i+1) . ".name_code = " . $arrNameCode[$i] . " and NAMES1.document_id = NAMES" . sprintf("%d", $i+1) . ".document_id ";
        }
        break;
}

// assign Document type criteria if it exist
if ($strDocType)
{
    $sqlStmtDocument = $sqlStmtDocument . " and document_records.doc_type " .  $strDocType;
}

// assign Document Start date criteria if it exist
if ($strStartDate)
{
    $sqlStmtDocument = $sqlStmtDocument . " and " .  $strStartDate;
}

// assign document End date criteria if it exist
if ($strEndDate)
{
$sqlStmtDocument = $sqlStmtDocument . " and " .  $strEndDate;
}

// retrieve first 5000
$sqlStmtDocument = $sqlStmtDocument .  " limit 1500";

// echo $sqlStmtDocument . "<BR><BR>";
// Example: select document_records.document_id from names_in_documents as NAMES1, document_records where document_records.document_id = NAMES1.document_id and NAMES1.name_code = 'EJR' limit 1500

//run the query, retrieve document ids
$result = mysqli_query($db,$sqlStmtDocument);
$rowCount = mysqli_num_rows($result);

//echo $rowCount;

// Produce a message if more than 1500 documents match criteria
if ( $rowCount == 1500) {
    echo "<html>";
	echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
	echo "</head>";
	echo "<link rel=stylesheet href=\"/new-style.css\" type=\"text/css\">";
	echo "<link rel=stylesheet href=\"namesearchstyle.css\" type=\"text/css\">";	
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
	echo "<div id=\"header\"> <a class=\"header-ru\" href=\"#\"><span class=\"offscreen\">Rutgers, The State University of New Jersey</span></a>";
	include("navigation.inc");
	echo "</div>";
    echo "<p class=\"searchmessage\"><span class=\"important\">There are more than 1500 matches.<br>Please refine your search.<br>";
	//print_r($arrNameCode);
	//echo "<br>Searched for: $arrNameCode[0]<br>";
    echo "</span>Press the back button to return to the Search page.</center></p>";
	echo "<!-- $sqlStmtDocument -->";
    echo "</body>";
    echo "</html>";
    return;
}

// Produce a message if no documents match criteria
if ( $rowCount == 0 )
{
    echo "<html>";
	echo "<head>";
	echo "</head>";
	echo "<link rel=stylesheet href=\"/new-style.css\" type=\"text/css\">";
	echo "<link rel=stylesheet href=\"namesearchstyle.css\" type=\"text/css\">";	
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
	echo "<div id=\"header\"> <a class=\"header-ru\" href=\"#\"><span class=\"offscreen\">Rutgers, The State University of New Jersey</span></a>";
	include("navigation.inc");
    echo "<p class=\"searchmessage\"><span class=\"important\">No Documents found.</span> <BR>";
    echo "Press the back button to return to the Search page.</center></p>";
	echo "<script type='text/javascript'>function Go(){return}</script>";
    echo "</body>";
    echo "</html>";    return;
}

// if document type sorting,  exclude Correspondence, Telegrams and Telephone messages and Name Mentions
if ( $noCorrType) {
    $sqlStmtDocument = substr($sqlStmtDocument, 0, strrpos($sqlStmtDocument, "limit 1500") );
    // check if name included in search
    if ( $numNames != 0 ) {
          $sqlStmtDocument = $sqlStmtDocument . " and NAMES1.role <> 'N' and document_records.doc_type not in ('01','02','03','04')";
    }
    else
    {
          $sqlStmtDocument = $sqlStmtDocument . " and document_records.doc_type not in ('01','02','03','04')";
    }
    // retrieve first 5000
    $sqlStmtDocument = $sqlStmtDocument .  " limit 1500";
    // echo $sqlStmtDocument . "<BR><BR>";
    //run the query, retrieve document ids
    $result = mysqli_query($db,$sqlStmtDocument);
}

// extract rows from document_records which represent the document id to report on
while ($myrow = mysqli_fetch_row($result))
{
  $DocIdsArray[] = "'$myrow[0]'";           // document ids array
}
mysqli_free_result($result);        // release mysql resultset resources

$numDocIds = count($DocIdsArray);

// continue only if document ID found
if ( $numDocIds != 0  )
{
    $DocIds = implode(",",$DocIdsArray) ;        // string to hold all document ids to maximum of 5000( set by limit SQL )

    unset($DocIdsArray);       //release Document id Array

    // sql statement for retrieving Document Information(reel, frame, Documment Type/Order, Document description and date
    // apply document ID match criteria then descired sorting order and set the limit to first 5000 then execute the query
    $sqlStmtDocument = "select document_records.document_id, document_records.reel, document_records.frame, doc_types.doc_type, doc_types.doc_order,(if(date_status.date_status = 'U', \"<I>[No Date]</I>\", if(date_status.date_status = 'M' or date_status.date_status = '8', concat(left(document_records.doc_date, 4), \"-\", right(document_records.doc_date, 4)), if (substring(document_records.doc_date,5,2) = '00' and substring(document_records.doc_date,7,2) = '00', left(document_records.doc_date,4), concat(substring(document_records.doc_date,5,2), \"/\", substring(document_records.doc_date,7,2), \"/\", left(document_records.doc_date, 4)))))) as doc_datesymbol, doc_types.description, document_records.doc_date, document_records.gloc, clippings.title, clippings.source, clippings.author, images.ordinal, language.meaning, document_status.meaning from doc_types, document_records LEFT JOIN clippings on document_records.document_id = clippings.document_id, document_records as dr1 LEFT JOIN date_status ON dr1.date_status = date_status.date_status, document_records as dr2 LEFT JOIN images on dr2.document_id = images.document_id, document_records as dr3 LEFT JOIN language on dr3.language = language.language, document_records as dr4 LEFT JOIN document_status on dr4.document_status = document_status.document_status where dr4.document_id = document_records.document_id and dr3.document_id = document_records.document_id and doc_types.doc_type = document_records.doc_type and document_records.document_id = dr1.document_id and document_records.document_id = dr2.document_id and (images.ordinal is null or images.ordinal = 1) and document_records.document_id in (" ;
    $sqlStmtDocument = $sqlStmtDocument . $DocIds . ") " .  $strOrderBy . " limit 1500";
    $resultDocuments = mysqli_query($db,$sqlStmtDocument);
    //echo $sqlStmtDocument . "<BR><BR>";

    // sql statement for retreiving subjects information based on Document ids match
    // apply the descired sorting order then execute the query
    $sqlStmtDocument = "select document_records.document_id, reel, frame, doc_types.doc_order, doc_date, subjects.description from doc_types, subjects_in_documents STRAIGHT_JOIN subjects,document_records where doc_types.doc_type = document_records.doc_type and subjects.subject = subjects_in_documents.subject and subjects_in_documents.document_id  = document_records.document_id and subjects_in_documents.document_id in (";
    $sqlStmtDocument = $sqlStmtDocument . $DocIds . ") " .  $strOrderBy;
    $resultSubject = mysqli_query($db,$sqlStmtDocument);
    //echo $sqlStmtDocument . "<BR><BR>";

    // sql statement for retreiving names information based on Document ids match exclude "NO ONE" name
    // apply the descired sorting order then execute the query
    //$sqlStmtDocument= " select document_records.document_id, reel, frame, doc_types.doc_order , doc_date, names_in_documents.role, names.name, names.code, 
    //  names_in_documents.marginalia, null from doc_types, names, document_records, names_in_documents 
    //  where doc_types.doc_type = document_records.doc_type 
    //  and document_records.document_id =  names_in_documents.document_id 
    //  and names_in_documents.name_code = names.code 
    //  and names.code <> 'XXX' and names_in_documents.document_id  in (";
    $sqlStmtDocument= " select document_records.document_id, reel, frame, doc_types.doc_order , doc_date, names_in_documents.role, names.name, names.code, 
      names_in_documents.marginalia, null 
      from document_records 
      LEFT JOIN doc_types ON document_records.doc_type = doc_types.doc_type 
      LEFT JOIN names_in_documents ON document_records.document_id =  names_in_documents.document_id 
      LEFT JOIN names ON names_in_documents.name_code = names.code 
      WHERE names.code <> 'XXX' and names_in_documents.document_id  in (";
    $sqlStmtDocument = $sqlStmtDocument . $DocIds . ") " .  $strOrderBy;
    $resultNames = mysqli_query($db,$sqlStmtDocument);
    //echo $sqlStmtDocument . "<BR><BR>";
}

//display all selected names, if names exist
if ( count($arrNameCode) != 0) {
    $Names = "";
    // display names as page header
    for ( $i=0; $i < 4; $i++) {
        if ($arrNames[$i] != " " && $arrNames[$i]) {     // if name exist or is not a single space, set up for display
                $Names .= $arrNames[$i] . " and ";
        }
    }
    $Names = substr($Names, 0, strrpos($Names, "and") );
}

 ?>
<html>
<head><script language="JavaScript">
// check if any Document Type checkbox was checked, if not set - default is set all checkboxes
function chkDocs() {
var DocSelected = false;
    // check if any checkboxes are set
    for ( i = 2; i < document.SearchDocuments.length -1  ;i++ ) {
       if (document.SearchDocuments.elements[i].checked)
       {
           DocSelected = true;
           break;
       }
    }
    // if no checkboxes are set, then set all checkboxes
     if (!(DocSelected))
     {
       for ( i = 2; i < document.SearchDocuments.length -1  ;i++ )
       {
          document.SearchDocuments.elements[i].checked = true;
          DocSelected = true;
        }
     }
     return DocSelected;
}
// clear all checkboxes
function clrDocs() {
       for ( i = 2; i < document.SearchDocuments.length - 1;i++ )
       {
          document.SearchDocuments.elements[i].checked = false;
        }
 }
</script></head>
<style type="text/css" media="all">
@import url("../new-style.css");
@import url("namesearchstyle.css");
</style>
<body>

++++++

<div id="header"> <a class="header-ru" href="#"><span class="offscreen">Rutgers, The State University of New Jersey</span></a> </div>

<?php

	include("navigation.inc");
	echo "</div>";
	
?>




<form name="SearchDocuments" method="POST" action="/DocDetImage.php"  target="_parent">

<?php

// Display Names selected, Document count and Start and end date
if ( $Names) {
    echo "<B><H2>Search Results for: " . rawurldecode($Names) . "</H2></B>";
}
else
{
    echo "<BR><BR>";
}
echo "<H5>Document Count: " . $rowCount;
if ( $FirstDate ) {
    echo "  Start Date: " . $FirstDate;
}
if ( $SecondDate ) {
    echo "  End Date: " . $SecondDate;
}

echo "</H5><p>Check the documents you would like to see, then click the \"Show Documents\" button; to select them all, just click the button.</p>";
echo "<center><input type=submit name=showDoc value=\"Show Documents\" onClick=\"return chkDocs()\" >&nbsp;<input type=\"button\" name=clrDocs value=\"Clear Checkboxes\" onClick=\"parent.clrDocs()\" ></center><BR>";
// display based on Report Type
switch ($rpttype) {
    case "RecordsByFilm":
        //build reel/frame display - pass name/subjects/Document resultset and Names selected
        include("DocumentDateForm.php");
        DocumentDateForm($resultNames, $resultSubject, $resultDocuments, $arrNameCode, $rpttype);
        break;
    case "RecordsByType":
        //build Document Type Order display - pass name/subjects/Document resultset, Names and start and end dates selected if Doc IDs exist
        if ( $numDocIds != 0  )
        {
            include("DocumentTypeForm.php");
            DocumentTypeForm($resultNames, $resultSubject, $resultDocuments, $arrNameCode,   $strStartDate, $strEndDate);
        }

        include("DocumentCorrMention.php");
        include("DocumentCorrForm.php");
        include("DocumentDateForm.php");
        // do not display Correspondence if a Document Type Selected or if Document Type selected is not Correspondence
        if ( !($strDocType)  ||  strstr($strDocType,"'01','02','03','04'") ) {
            // documents where names appear as authors or recepients
            DocumentCorrMention($arrNameCode, $strStartDate, $strEndDate, 'AR', $strDocType);
        }
        if ( $numNames != 0) {          // no need for name mentions, if no name supplied
            // documents where names appear as names mention
            DocumentCorrMention($arrNameCode, $strStartDate, $strEndDate, 'N', $strDocType);
        }
        break;
    case "RecordsByTime":
        //build Document Date Order display - pass name/subjects/Document resultset and Names selected
        include("DocumentDateForm.php");
        DocumentDateForm($resultNames, $resultSubject, $resultDocuments, $arrNameCode, $rpttype);
        break;
}
// display show document button if more than 9 documents otherwise display hidden as a spaceholder
if ($numDocIds > 9)
{
    echo "<BR><center><input type=submit name=showDoc value=\"Show Documents\" onClick=\"return chkDocs()\" ></center><BR>";
}
else
{
    echo "<BR><center><input type=hidden name=spaceholder ><BR>";
}
// end form
echo "</form>";

// echo "complete";



?>

<!-- DTK: WHY NO-OP FUNCTION -->
<script type='text/javascript'>
function Go(){return}/**
 * Sets a Cookie with the given name and value.
 *
 * name       Name of the cookie
 * value      Value of the cookie
 * [expires]  Expiration date of the cookie (default: end of current session)
 * [path]     Path where the cookie is valid (default: path of calling document)
 * [domain]   Domain where the cookie is valid
 *              (default: domain of calling document)
 * [secure]   Boolean value indicating if the cookie transmission requires a
 *              secure transmission
 */
function setCookie(name, value, expires, path, domain, secure)
{
    document.cookie= name + "=" + escape(value) +
        ((expires) ? "; expires=" + expires.toGMTString() : "") +
        ((path) ? "; path=" + path : "") +
        ((domain) ? "; domain=" + domain : "") +
        ((secure) ? "; secure" : "");
}


setCookie("lastSLoc", document.location );
</script> 

<?php
include("testfooter.php");
?>
