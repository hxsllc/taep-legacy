<html>

<head>
  <script language="JavaScript">
    <?php
    include("ImageControl.js");
    ?>
  </script>
<style type="text/css" media="all">
@import url("/new-style.css");
@import url("namesearchstyle.css");
</style>
</head>
<body background="/webimages/background.gif" onLoad="DispPicture(1)">
<?php
 if ( $noName ) {                        // if single document display then show return icon
// echo "<a href=\"http://edison.rutgers.edu/taep.htm\" target=_parent ><IMG SRC=\"http://edison.rutgers.edu/NamesSearch/graphics/homelogo.gif\" ALT=\"Home Page\" ALIGN=\"right\"></a><a href=\"http://edison.rutgers.edu/srchdocs.htm\" target=_parent ><IMG SRC=\"http://edison.rutgers.edu/NamesSearch/graphics/search.gif\" ALT=\"Name/Date/Type Search\" ALIGN=\"right\"></a><BR>";
 }

//include("../NamesSearch/testheader.inc");
include("testheader.php");
foreach($_POST as $key=>$value) ${$key}=$value;
foreach($_GET as $key=>$value) ${$key}=$value;

	echo "<div id=\"header\"> <a class=\"header-ru\" href=\"#\"><span class=\"offscreen\">Rutgers, The State University of New Jersey</span></a>";
	include("navigation.inc");
	echo "</div>";
	
?>
<div id="imagecount"></div>
<br>
<?php
// extract from cookie name/value pair and build name code array
// noName indicator on - means no name search - single document display
if ( !($noName) ) {                                                        // document.imageform.indexPicture.value
    for ( $i=0; 4<>$i; ++$i){
        eval( "\$strName = \"$" . "namecode" . $i ."\";" );  // dynamic execution
        if ( $strName ) {
            $arrNameCode[] = "'" . stripSlashes($strName) ."'" ;
        }
    }
}

// sql statement for retreiving subjects information based on Document ids match
$sqlStmtDocument = "select document_records.document_id, reel, frame, doc_types.doc_order, doc_date, subjects.description from doc_types, subjects_in_documents STRAIGHT_JOIN subjects,document_records where doc_types.doc_type = document_records.doc_type and subjects.subject = subjects_in_documents.subject and subjects_in_documents.document_id  = document_records.document_id and subjects_in_documents.document_id = ";
$sqlStmtDocument = $sqlStmtDocument . "'" . $DocId . "' ";
$resultSubject = mysqli_query($db,$sqlStmtDocument);
// echo $sqlStmtDocument . "<BR><BR>";

// sql statement for retreiving names information based on Document ids match
$sqlStmtDocument= " select document_records.document_id, reel, frame, doc_types.doc_order , doc_date, names_in_documents.role, names.name, names.code, names_in_documents.marginalia, names_in_documents.conjectured  from doc_types, names, document_records, names_in_documents where doc_types.doc_type = document_records.doc_type and document_records.document_id =  names_in_documents.document_id and names_in_documents.name_code = names.code and names.code <> 'XXX' and names_in_documents.document_id = ";
$sqlStmtDocument = $sqlStmtDocument . "'" . $DocId . "' ";
$resultNames = mysqli_query($db,$sqlStmtDocument);
// echo $sqlStmtDocument . "<BR><BR>";

// sql statement for retrieving Document Information(reel, frame, Documment Type/Order, Document description and date
// apply document ID match criteria  execute the query
$sqlStmtDocument = "select dr1.document_id, dr1.reel, dr1.frame, doc_types.doc_type, doc_types.doc_order,concat(if(date_status.date_status = 'U', \" \", if(date_status.date_status = 'M' or date_status.date_status = '8', concat(left(dr1.doc_date, 4), \"-\", right(dr1.doc_date, 4)), if (substring(dr1.doc_date,5,2) = '00' and substring(dr1.doc_date,7,2) = '00', left(dr1.doc_date,4), concat(substring(dr1.doc_date,5,2), \"/\", substring(dr1.doc_date,7,2), \"/\", left(dr1.doc_date, 4))) )), \" \", if(isnull(date_status.meaning) or (date_status.date_status = 'M'),\" \", concat(\"<I>[\", date_status.meaning, \"]</I>\")))  as doc_datesymbol, doc_types.description, dr1.doc_date, dr1.gloc, clippings.title, clippings.source, clippings.author, null, language.meaning, document_status.meaning from doc_types, document_records as dr1 LEFT JOIN date_status ON dr1.date_status = date_status.date_status, document_records as dr2 LEFT JOIN clippings on dr2.document_id = clippings.document_id, document_records as dr3 LEFT JOIN language on dr3.language = language.language, document_records as dr4 LEFT JOIN document_status on dr4.document_status = document_status.document_status where dr4.document_id = dr1.document_id and dr3.document_id = dr1.document_id and doc_types.doc_type = dr1.doc_type and dr1.document_id = dr2.document_id and dr1.document_id = " ;
$sqlStmtDocument = $sqlStmtDocument . "'" . $DocId . "' ";
$resultDocuments = mysqli_query($db,$sqlStmtDocument);
//echo $sqlStmtDocument . "<BR><BR>";

// check if document exist otherwise return
if ( mysqli_num_rows($resultDocuments) == 0) {
    echo " <center><B>no Document found</B></center> " ;
    echo " <center>Press back button to return</center> " ;
	//include("../NamesSearch/testfooter.inc");
	include("testfooter.php");
    return;
}

//Display Document using the Document Date display name/subjects/Document resultset and Names selected
//use Chronological/reelFrame Display format except display only date
include("DocumentDateForm.php");
DocumentDateForm($resultNames, $resultSubject, $resultDocuments, $arrNameCode, "DocImage");

// re-query document SQL
$resultDocuments = mysqli_query($db,$sqlStmtDocument);
$myDoc = mysqli_fetch_row($resultDocuments);

//retrieve gloc information based on gloc
$SqlStatement = "SELECT gloc, group_name, item_name, target, credit_line from locations where gloc = '$myDoc[8]' ";
$result = mysqli_query($db,$SqlStatement);
// Display message if no Location series info
if ( mysqli_num_rows($result) == 0) {
    printf(" no File Series ");
}
else
{
    // Display Gloc information  with Document ID and REEL/FRAME
    $myrow = mysqli_fetch_array($result);
    printf("<DIV STYLE=\"margin-left:4%%;margin-right:5%%;\" ><IMG ALIGN=bottom BORDER=0 SRC=\"../NamesSearch/graphics/OpenFolder.gif\" > <A target=_blank href=\"glocpage.php?gloc=%s&\">[%s]&#160;&#160;%s: %s</a><BR>", urlencode($myrow[0]), $myrow["gloc"], $myrow["group_name"], $myrow["item_name"]);
    printf("[%s; TAEM %s:%s] ", $myDoc[0], $myDoc[1], $myDoc[2]);
    if ( $myrow["credit_line"] ) {
     printf("<BR>%s  ", $myrow["credit_line"]);
    }
    echo "</DIV>";
}

//retrieve images file names for specific Document from image table
$SqlStatement = "SELECT image_id from images where document_id = '$myDoc[0]' order by ordinal ";
$result = mysqli_query($db,$SqlStatement);
// Display Message if no images for this Document
if ( mysqli_num_rows($result) == 0) {
    printf(" no Images for this Document ");
}
else
{
    //retrieve images file names from Notebooks table using Gloc
    $SqlStatement = "SELECT image_id from notebooks where gloc = '$myDoc[8]' order by ordinal ";
    $resultNoteBook = mysqli_query($db,$SqlStatement);
    //check if images exist in Notebooks table otherwise handle images normally(using image table)
    if ( mysqli_num_rows($resultNoteBook) != 0)
    {
       $myrow = mysqli_fetch_row($result);
       $NoteImageFilename = $myrow[0];           // Save Notebook Image file name from image table name search
       $ImagePtr = 0;              // initialize position of first image for display to 0
       // Retrieve all Image file names in Image array
       while ($myrow = mysqli_fetch_row($resultNoteBook))
       {
           $ImagedsArray[] = $myrow[0];           // Image file name array
           // Set the image pointer to display, when Notebook image file name match
           if ( $myrow[0] == $NoteImageFilename ) {
                   $ImagePtr = count($ImagedsArray) - 1;
           }
        }
    }
    else
    {
        $ImagePtr = 0;              // initialize position of first image for display to 0
        // Retrieve all Image file names in Image array
        while ($myrow = mysqli_fetch_row($result))
        {
                $ImagedsArray[] = $myrow[0];           // Image file name array
        }
    }
}
// setup hidden elements
echo "<FORM name=imageform method=post target=ImagePicture ACTION=\"DocPicture.php\">";
// Display Add to List and Show List buttons
printf("<center><input name=addDoc type=button value=\"Add to List\" size=10 onClick=\"setAddVal('$DocId')\">&nbsp;&nbsp;&nbsp;");
printf("<input type=button value=\"Show List\" size=10 onClick=\"showList()\">");
// ID key and Document ID hidden fields
echo "<input type=hidden name=uniqKey value='' >";
echo "<input type=hidden name=DocId value='' >";
//Personal List indicator hidden field
echo "<input type=hidden name=lstPerson value='' >";
// imagefilename is image file name currently displayed
echo "<input type=hidden name=imagefilename >";
// index position, this is relative position(within hidden) of displayed image
echo "<input type=hidden name=indexPicture value=$ImagePtr >";
// maximum number of hidden elements
echo "<input type=hidden name=numpicture value=" . count($ImagedsArray) .  ">";
// place all image file name in Hidden html elements
for ( $i=0; count($ImagedsArray)<>$i; ++$i) {
    echo "<input type=hidden name=name" . $i . " value=" . $ImagedsArray[$i] . ">";
}
echo "</FORM>";

// echo "complete";
//include("../NamesSearch/testfooter.inc");
include("testfooter.php");
?>
