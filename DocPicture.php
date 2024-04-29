<?php

include("testheader.php");
foreach($_POST as $key=>$value) ${$key}=$value;
foreach($_GET as $key=>$value) ${$key}=$value;

// when the Document ID key value is set then save the Document ID along with Key and current datetime stamp
if ( $DocId ) {
	echo 'docid';
	die();
    $sqlInsDocument= " insert into documents_saved(client_time, document_id, processed) values ('$uniqKey', '$DocId', sysdate())";
    // echo $sqlInsDocument;
    $insDoc = mysqli_query($db,$sqlInsDocument);
}

// when the lstPerson is set then display Personal Document List
if ( $lstPerson ) {
    // first delete expired documents first - documents more than 12 hours old
    $sqlDelDocument = " delete from documents_saved where sysdate() - processed > 120000";
    // echo $sqlDelDocument;
    $insDoc = mysqli_query($db,$sqlDelDocument);

    // retrieve the Documents in Personal List
    $sqlSelDocument = " select document_id from documents_saved where client_time = $uniqKey";
    // echo $sqlSelDocument;
    $selDoc = mysqli_query($db,$sqlSelDocument);

    // extract Document Id from documents_saved rows which match key into Document array
    while ($myrow = mysqli_fetch_row($selDoc))
    {
        $DocIdsArray[] = "'$myrow[0]'";           // document ids array
    }
    mysqli_free_result($selDoc);        // release mysql resultset resources

    $numDocIds = count($DocIdsArray);

    // continue only if document ID found
    if ( $numDocIds != 0  )
    {
        $DocIds = implode(",",$DocIdsArray) ;        // string to hold all document ids to maximum of 500( set by limit SQL )

        unset($DocIdsArray);       //release Document id Array

        // sql statement for retrieving Document Information(reel, frame, Documment Type/Order, Document description and date
        // apply document ID match criteria then descired sorting order and set the limit to first 500 then execute the query
        $sqlStmtDocument = "select document_records.document_id, document_records.reel, document_records.frame, doc_types.doc_type, doc_types.doc_order,(if(date_status.date_status = 'U', \"<I>[No Date]</I>\", if(date_status.date_status = 'M' or date_status.date_status = '8', concat(left(document_records.doc_date, 4), \"-\", right(document_records.doc_date, 4)), if (substring(document_records.doc_date,5,2) = '00' and substring(document_records.doc_date,7,2) = '00', left(document_records.doc_date,4), concat(substring(document_records.doc_date,5,2), \"/\", substring(document_records.doc_date,7,2), \"/\", left(document_records.doc_date, 4)))))) as doc_datesymbol, doc_types.description, document_records.doc_date, document_records.gloc, clippings.title, clippings.source, clippings.author, images.ordinal, language.meaning, document_status.meaning from doc_types, document_records LEFT JOIN clippings on document_records.document_id = clippings.document_id, document_records as dr1 LEFT JOIN date_status ON dr1.date_status = date_status.date_status, document_records as dr2 LEFT JOIN images on dr2.document_id = images.document_id, document_records as dr3 LEFT JOIN language on dr3.language = language.language, document_records as dr4 LEFT JOIN document_status on dr4.document_status = document_status.document_status where dr4.document_id = document_records.document_id and dr3.document_id = document_records.document_id and doc_types.doc_type = document_records.doc_type and document_records.document_id = dr1.document_id and document_records.document_id = dr2.document_id and (images.ordinal is null or images.ordinal = 1) and document_records.document_id in (" ;
        $sqlStmtDocument = $sqlStmtDocument . $DocIds . ") order by document_records.document_id ";
        $resultDocuments = mysqli_query($db,$sqlStmtDocument);
        // echo $sqlStmtDocument . "<BR><BR>";

        // sql statement for retreiving subjects information based on Document ids match
        // apply the descired sorting order then execute the query
        $sqlStmtDocument = "select document_records.document_id, reel, frame, doc_types.doc_order, doc_date, subjects.description from doc_types, subjects_in_documents STRAIGHT_JOIN subjects,document_records where doc_types.doc_type = document_records.doc_type and subjects.subject = subjects_in_documents.subject and subjects_in_documents.document_id  = document_records.document_id and subjects_in_documents.document_id in (";
        $sqlStmtDocument = $sqlStmtDocument . $DocIds . ") order by document_records.document_id ";
        $resultSubject = mysqli_query($db,$sqlStmtDocument);
        //  echo $sqlStmtDocument . "<BR><BR>";

        // sql statement for retreiving names information based on Document ids match exclude "NO ONE" name
        // apply the descired sorting order then execute the query
        $sqlStmtDocument= " select document_records.document_id, reel, frame, doc_types.doc_order , doc_date, names_in_documents.role, names.name, names.code, names_in_documents.marginalia, null from doc_types, names, document_records, names_in_documents where doc_types.doc_type = document_records.doc_type and document_records.document_id =  names_in_documents.document_id and names_in_documents.name_code = names.code and names.code <> 'XXX' and names_in_documents.document_id  in (";
        $sqlStmtDocument = $sqlStmtDocument . $DocIds . ") order by document_records.document_id ";
        $resultNames = mysqli_query($db,$sqlStmtDocument);
        // echo $sqlStmtDocument . "<BR><BR>";
?>
<html>
<head><script language="JavaScript">
// check if any Document Type checkbox was checked, if not set - default is set all checkboxes
function chkDocs() {
var DocSelected = false;
    // check if any checkboxes are set
    for ( i = 2; i < document.SearchDocuments.length ;i++ ) {
       if (document.SearchDocuments.elements[i].checked)
       {
           DocSelected = true;
           break;
       }
    }
    // if no checkboxes are set, then set all checkboxes
     if (!(DocSelected))
     {
       for ( i = 2; i < document.SearchDocuments.length ;i++ )
       {
          document.SearchDocuments.elements[i].checked = true;
          DocSelected = true;
        }
     }
     return DocSelected;
}
// clear all checkboxes
function clrDocs() {
       for ( i = 2; i < document.SearchDocuments.length ;i++ )
       {
          document.SearchDocuments.elements[i].checked = false;
        }
 }
</script>
<style type="text/css" media="all">
@import url("../new-style.css");
@import url("namesearchstyle.css");
</style></head>
<body onLoad="setName('<?php echo $namecode;?>', '<?php echo addslashes($name);?>', -2)" >
<div id="header"> <a class="header-ru" href="#"><span class="offscreen">Rutgers, The State University of New Jersey</span></a> <a class="header-taep" href="#"><span class="offscreen">The Thomas Edison Papers</span></a> </div>

<table width="750" cellpadding="0" cellspacing="0" border="0" class="searchtable"> 
    <tr valign="top"> 
        <td id="content">


<form name=SearchDocuments method=POST action="http://edison.rutgers.edu/NamesSearch/DocDetImage.php"  target=_parent>
<?php
        echo "<BR><BR></H5><I>Check the documents you would like to see, then click the \"Show Documents\" button; to select them all, just click the button.</I>";
        echo "<BR><center><input type=\"button\" name=clrDocs value=\"Clear Checkboxes\" onClick=\"parent.clrDocs()\" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=submit name=showDoc value=\"Show Documents\" onClick=\"return chkDocs()\" ></center><BR>";
        echo "<input type=hidden name=norpttype value=RecordsByTime>";

        //build reel/frame display - pass name/subjects/Document resultset and Names selected
        include("DocumentDateForm.php");
        DocumentDateForm($resultNames, $resultSubject, $resultDocuments, "", "RecordsByFilm");
        echo "</form></td></tr></table>";

        //include("../NamesSearch/testfooter.inc");
        include("testfooter.php");
    }
    else
    {
        SetCookie("keyDoc", "");        // clear the unique key since rows no longer exist
   
        echo "<BR><CENTER><B> no Documents in Personal List </B></CENTER><BR></td></tr></table>";
        

        //include("../NamesSearch/testfooter.inc");
        include("testfooter.php");
    }
    return;
}
// if no images, no need for image navigation
if ($numpicture != 0)
{
    // Display Left Arrow image used to navigate through the images to the left
    echo "<html><body><center><FORM name=Imageform method=post action=\"javascript:parent.frames[1].DispPicture(0,Imageform.imagenum.value)\">";
    //  display the image controls in a table format
    echo "<TABLE><TR><TD>";
    // If image index is at the start hidden html element, do not show left arrow image
    if ( $indexPicture != 1) {
        echo "<a href=\"javascript:parent.frames[1].DispPicture(-1,0)\"><IMG BORDER=0 SRC=\"/webimages/previmage.gif\"ALT=\"Previous Image\" ></a> &nbsp;&nbsp;";
    }
    echo "</TD><TD align=center>";
    if ( $numpicture > 1) {
        // Display image number entry box and button
        echo "<INPUT TYPE=\"button\" SIZE=3 VALUE=\"Go to Image\" onClick=\"parent.frames[1].DispPicture(0,imagenum.value)\" ><BR><INPUT TYPE=\"text\" NAME=\"imagenum\" SIZE=3 VALUE=\"1\" > &nbsp;&nbsp;";
    }
    echo "</TD><TD>";

    //Display Right Arrow image used to navigate through the images to the right
    // If image index is at end hidden html element, do not show right arrow image
    if ( $indexPicture != $numpicture) {
        echo "&nbsp;&nbsp;<a href=\"javascript:parent.frames[1].DispPicture(1,0)\" ><IMG BORDER=0 SRC=\"/webimages/nextimage.gif\" ALT=\"Next Image\"></a>";
    }
    echo "</TD></TR></TABLE>";
    echo "<IMG BORDER=0 SRC=\"https://edisonlegacy.reclaim.hosting/images/" . substr($imagefilename,0,2) . "/" . $imagefilename . ".jpg\" >";

    //  display the image controls in a table format
    echo "<TABLE><TR><TD>";
    // If image index is at the start hidden html element, do not show left arrow image
    if ( $indexPicture != 1) {
        echo "<a href=\"javascript:parent.frames[1].DispPicture(-1,0)\"><IMG BORDER=0 SRC=\"/webimages/previmage.gif\"ALT=\"Previous Image\" ></a> &nbsp;&nbsp;";
    }
    echo "</TD>";
    echo "<TD>";

    //Display Right Arrow image used to navigate through the images to the right
    // If image index is at end hidden html element, do not show right arrow image
    if ( $indexPicture != $numpicture) {
        echo "&nbsp;&nbsp;<a href=\"javascript:parent.frames[1].DispPicture(1,0)\" ><IMG BORDER=0 SRC=\"/webimages/nextimage.gif\" ALT=\"Next Image\"></a>";
    }
    echo "</TD></TR></TABLE>";

    echo "</FORM></center>";
}

?></td></tr></table>

     </body>
</html>
