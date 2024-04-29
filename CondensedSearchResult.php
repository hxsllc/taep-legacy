<?php
error_reporting(0);
session_start();
?>
<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script type="text/javascript" language="javascript">
function getCookie(name)
{
    var dc = document.cookie;
    var prefix = name + "=";
    var begin = dc.indexOf("; " + prefix);
    if (begin == -1)
    {
        begin = dc.indexOf(prefix);
        if (begin != 0) return null;
    }
    else
    {
        begin += 2;
    }
    var end = document.cookie.indexOf(";", begin);
    if (end == -1)
    {
        end = dc.length;
    }
    return unescape(dc.substring(begin + prefix.length, end));
}
</script>

<?php
include("testheader.php");
//echo "testheader";

$getString=$_SESSION['getString'];
$DocIds=$_SESSION['strDocType'];

//print_r($_SESSION);

//CondensedSearchResult.php?getString=<?php echo $getString;
//&DocIds=<?php echo $strDocType;

foreach($_GET as $key=>$value) ${$key}=$value;
//$DocIds=" in (".base64_decode($DocIds).")";
$DocIds=" in ($DocIds)";

if ( $DocIds) {
    // list of selected Document Ids
    $DocIds = stripslashes($DocIds);
    //echo $DocIds;
    // gather no name parameters if coming from a list of Documents,  which is by default a no name search
    if ( !($norpttype)) {
        // extract from cookie name/value pair and build name code array
        for ( $i=0; 4<>$i; ++$i){
            eval( "\$strName = \"$" . "namecode" . $i ."\";" );  // dynamic execution
            if ( $strName ) {
                $arrNameCode[] = "'" . $strName . "'";
            }
        }
        $numNames = @count($arrNameCode);

        // retrieve names based on name code array, if name exist
        //display all selected names, if names exist
        if ( $numNames != 0) {
            $Names = "";
            // gather names to display names as page header
            for ( $i=0; $i < count($arrNameCode); $i++) {
                $SqlStatement = "SELECT names.name, names_accent.name FROM names LEFT JOIN names_accent on names.code = names_accent.code where names.code = " . str_replace("\"","\'",$arrNameCode[$i]);
                // echo $SqlStatement;
                // $SqlStatement = "SELECT name FROM names where code = " . str_replace("\"","\'",$arrNameCode[$i]) ;
                $result = mysqli_query($db,$SqlStatement);
                $myrow = mysqli_fetch_row($result);
                if ( $myrow[1] ) {
                    $Names .= $myrow[1] . " and ";
                }
                else
                {
                    $Names .= $myrow[0] . " and ";
                }
            }
            $Names = substr($Names, 0, strrpos($Names, "and") );  // eliminate trailing "and"
            mysqli_free_result($result);        // release mysql resultset resources
        }
    }
    else
    {
        $rpttype = $norpttype;          //  List of Documents - display condensed form (use Chronological display form)
        $numNames = 0;                  //default is no names
    }

    // set the condensed SQL order by the same as main SQL order by
    switch ($rpttype) {
        case "RecordsByFilm":
            // Reel : Frame Order
            $strOrderBy = "order by reel, frame, document_records.document_id";
            break;
        case "RecordsByType":
            // Document Type Order
            $strOrderBy = "order by doc_types.doc_order, doc_date, document_records.document_id";
            break;
        default:
            // Chronological Order
            // if coming from List of Documents - then sort order is just Document id
            if ( $norpttype ) {
                $strOrderBy = "order by document_records.document_id";
            } else
            {
                $strOrderBy = "order by doc_date, document_records.document_id";
            }
            break;
    }
    // condensed SQL statement for retrieving Document Information(reel, frame, Documment Type/Order, Document description and date
    // apply document ID match criteria then desired sorting order
    $sqlStmtDocument =  "select document_records.document_id, reel, frame, doc_types.doc_type, doc_types.doc_order,(if(date_status.date_status = 'U', \"<I>[No Date]</I>\", if(date_status.date_status = 'M' or date_status.date_status = '8', concat(left(document_records.doc_date, 4), \"-\", right(document_records.doc_date, 4)), if (substring(document_records.doc_date,5,2) = '00' and substring(document_records.doc_date,7,2) = '00', left(document_records.doc_date,4), concat(substring(document_records.doc_date,5,2), \"/\", substring(document_records.doc_date,7,2), \"/\", left(document_records.doc_date, 4)))))) as doc_datesymbol, doc_types.description, doc_date ";
    // if document type sorting,  exclude Correspondence, Telegrams and Telephone messages
    if ($rpttype == "RecordsByType") {
        // if name search search on first name only and ignore Name mentions
        if ( $numNames != 0) {
            $sqlStmtDocument = $sqlStmtDocument . " from doc_types, document_records LEFT JOIN date_status ON document_records.date_status = date_status.date_status,  names_in_documents where document_records.document_id = names_in_documents.document_id and doc_types.doc_type = document_records.doc_type and names_in_documents.document_id " . $DocIds . " and names_in_documents.role <> 'N' and names_in_documents.name_code = " . $arrNameCode[0] . " and document_records.doc_type not in ('01','02','03','04')";
        }
        else
        {                               // if no name search, ignore name lookup
            $sqlStmtDocument = $sqlStmtDocument . " from doc_types, document_records LEFT JOIN date_status ON document_records.date_status = date_status.date_status where doc_types.doc_type = document_records.doc_type and document_records.document_id " . $DocIds . "  and document_records.doc_type not in ('01','02','03','04')";
        }
    }
    else
    {
        // if not Document Type Order set no exclusions ( all doc types and Name Mentions )
        if ( $numNames != 0) {          // key of first name only
            $sqlStmtDocument = $sqlStmtDocument . " from doc_types, document_records LEFT JOIN date_status ON document_records.date_status = date_status.date_status,  names_in_documents where document_records.document_id = names_in_documents.document_id and doc_types.doc_type = document_records.doc_type and names_in_documents.document_id " . $DocIds . " and names_in_documents.name_code = " . $arrNameCode[0] ;
        }
        else  {                         // if no name search, ignore name lookup
            $sqlStmtDocument = $sqlStmtDocument . " from doc_types, document_records LEFT JOIN date_status ON document_records.date_status = date_status.date_status where doc_types.doc_type = document_records.doc_type and document_records.document_id " . $DocIds;
        }

    }

    $sqlStmtDocument = $sqlStmtDocument . " " .  $strOrderBy ;
    // echo $sqlStmtDocument . "<BR><BR>";
    $resultDocuments = mysqli_query($db,$sqlStmtDocument);

    // Condensed sql statement for retreiving authors & recipients only,  do not pull name mentions,  based on Document ids match
    // apply the descired sorting order then execute the query
    //$sqlStmtDocument = " select document_records.document_id, reel, frame, doc_types.doc_order , doc_date, names_in_documents.role, names.name, names.code, doc_types.doc_type from doc_types, names, document_records, names_in_documents where doc_types.doc_type = document_records.doc_type and document_records.document_id =  names_in_documents.document_id and names_in_documents.role <> 'N' and names_in_documents.name_code = names.code and names_in_documents.document_id ";
    $sqlStmtDocument = " select document_records.document_id, reel, frame, doc_types.doc_order , doc_date, names_in_documents.role, names.name, 
	names.code, doc_types.doc_type 
	from document_records
	LEFT JOIN doc_types ON document_records.doc_type = doc_types.doc_type    
	LEFT JOIN names_in_documents ON document_records.document_id =  names_in_documents.document_id
	LEFT JOIN names ON names_in_documents.name_code = names.code
	WHERE names_in_documents.role <> 'N'
	and names_in_documents.document_id ";
    $sqlStmtDocument = $sqlStmtDocument . $DocIds . " ";
    // if document type sorting,  exclude Correspondence, Telegrams and Telephone messages
    if ( $rpttype == "RecordsByType") {
       $sqlStmtDocument = $sqlStmtDocument .  "  and document_records.doc_type not in ('01','02','03','04')";
    }
    $sqlStmtDocument = $sqlStmtDocument . " " .  $strOrderBy;
    //  echo $sqlStmtDocument . "<BR><BR>";
    $resultNames = mysqli_query($db,$sqlStmtDocument);

    // start the form
    echo "<html><link rel=stylesheet href=\"/style.css\" type=\"text/css\"><link rel=stylesheet href=\"namesearchstyle.css\" type=\"text/css\"><body><form>";

    // Display Names selected <input type=\"button\" SIZE=5 name=bckResult value=\"Back to Search Result\" onClick=\"var l = getCookie('lastSLoc'); if( l ) { top.history.go(l) }\">
    echo "<div class=\"sidesearchcolumn\"><br /><br />" . rawurldecode($Names) . "</div>";
	echo "<p class=\"footnote\">Click link of desired image to view.</p>";

    switch ($rpttype) {
        // if Document Type order,
        // first display Document all type except Correspondence and Name Mentions
        // Second display Correspondence
        // third display Name Mentions
        case "RecordsByType":
            //build Document Type Order display - pass name/Document resultset and Names/Document Ids selected
            include("CondensedTypeForm.php");
            CondensedTypeForm($resultNames, $resultDocuments, $DocIds, $arrNameCode, "TYPE");
            include("CondensedCorrMention.php");
            // build Document Type display for Correcpondence (Authors and Receipients)
            CondensedCorrMention('AR', $DocIds, $arrNameCode);
            // if name search, then build Document Type display for Name Mentions
            if ( count($arrNameCode) != 0) {  // no need for name mentions if no name supplied
                // documents where names appear as names mention
                CondensedCorrMention('N', $DocIds, $arrNameCode);
            }
            break;
        default:
            //build Document Date Order display or reel/frame Order display- pass name/Document resultset and Names
            include("CondensedDateForm.php");
            CondensedDateForm($resultNames, $resultDocuments, $rpttype, $arrNameCode);
            break;
    }
    // end form
    echo "</form></font>";
}
else
{
    echo "<link rel=stylesheet href=\"/style.css\" type=\"text/css\"><link rel=stylesheet href=\"namesearchstyle.css\" type=\"text/css\">";
    // This javascript function handles interrogating browser history in order to return to Search Result page
	//		document.tempform.DocIds.value = parent.strDocIds; document.tempform.submit();
    echo "<SCRIPT LANGUAGE=\"JavaScript\"> 
		function formhandle() { 
			document.tempform.DocIds.value = parent.strDocIds; 
			document.tempform1.submit();
		} 
		</script>";
    //echo "</head><body onLoad=\"formhandle()\">";
    echo "</head><body>";
    echo "<form name='tempform'  method='POST' action=\"CondensedSearchResult.php\" >";
    echo "<input type=hidden name='DocIds' value=''>";
    echo "<input type=hidden name='norpttype' value=\"" . $norpttype ."\">";
	echo "<input type=submit>";
    echo "</form>";
}

// echo "complete";
echo "<script type='text/javascript'>function Go(){return}</script>";


include("testfooter.php");
?>
