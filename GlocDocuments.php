<?php
    include("testheader.php");
	foreach($_POST as $key=>$value) ${$key}=$value;
	foreach($_GET as $key=>$value) ${$key}=$value;

    $strOrderBy = "order by document_records.document_id";

    // sql statement for retrieving Document Information(reel, frame, Documment Type/Order, Document description and date
    // apply Gloc match criteria then desired sorting order and set the limit to first 500 with start offset then execute the query
    $sqlStmtDocument = "select document_records.document_id, document_records.reel, document_records.frame, doc_types.doc_type, doc_types.doc_order,(if(date_status.date_status = 'U', \"<I>[No Date]</I>\", if(date_status.date_status = 'M' or date_status.date_status = '8', concat(left(dr1.doc_date, 4), \"-\", right(dr1.doc_date, 4)), if (substring(dr1.doc_date,5,2) = '00' and substring(dr1.doc_date,7,2) = '00', left(dr1.doc_date,4), concat(substring(dr1.doc_date,5,2), \"/\", substring(dr1.doc_date,7,2), \"/\", left(dr1.doc_date, 4)))))) as doc_datesymbol, doc_types.description, dr1.doc_date, document_records.gloc, clippings.title, clippings.source, clippings.author, images.ordinal, language.meaning, document_status.meaning  from doc_types, document_records LEFT JOIN clippings on document_records.document_id = clippings.document_id, document_records as dr1 LEFT JOIN date_status ON dr1.date_status = date_status.date_status, document_records as dr2 LEFT JOIN images on dr2.document_id = images.document_id, document_records as dr3 LEFT JOIN language on dr3.language = language.language, document_records as dr4 LEFT JOIN document_status on dr4.document_status = document_status.document_status where dr4.document_id = document_records.document_id and dr3.document_id = document_records.document_id and document_records.document_id = dr2.document_id and (images.ordinal is null or images.ordinal = 1) and doc_types.doc_type = document_records.doc_type and document_records.document_id = dr1.document_id and document_records.gloc = '" ;
    $sqlStmtDocument = $sqlStmtDocument . $glocNum . "' " .  $strOrderBy . " limit $start_offset, 500";
    $resultDocuments = mysqli_query($db,$sqlStmtDocument);
    // echo $sqlStmtDocument . "<BR><BR>";

    $rowCount = mysqli_num_rows($resultDocuments);  // number of documents for this gloc(maximum of 500)

    // Gloc are displayed in pages of 500 documents,  the offset indicates the starting position of that particulat page
    // check the document id so that subject and name search will selected document info for document starting with doc id
    if ( $start_offset > 0 ) {
        $myrow = mysqli_fetch_row($resultDocuments);
        $DocId = $myrow[0];        // set the document id which starts the report on
        mysqli_data_seek($resultDocuments,0);  // reset the cursor pointer
    }

    // sql statement for retreiving subjects information based on Gloc match
    // apply the descired sorting order then execute the query
    $sqlStmtDocument = "select document_records.document_id, reel, frame, doc_types.doc_order, doc_date, subjects.description from document_records STRAIGHT_JOIN subjects_in_documents STRAIGHT_JOIN subjects, doc_types  where doc_types.doc_type = document_records.doc_type and subjects.subject = subjects_in_documents.subject and subjects_in_documents.document_id  = document_records.document_id and document_records.gloc = '";
    $sqlStmtDocument = $sqlStmtDocument . $glocNum . "' ";
    // select subjects whose doc id greater or equal - used as part of offset when more than 500 documents
    if ($DocId) {
        $sqlStmtDocument = $sqlStmtDocument . " and document_records.document_id >= '" . $DocId . "' ";
    }
    $sqlStmtDocument = $sqlStmtDocument . $strOrderBy;
    $resultSubject = mysqli_query($db,$sqlStmtDocument);
    // echo $sqlStmtDocument . "<BR><BR>";


    // sql statement for retreiving names information based on Gloc match
    // apply the descired sorting order then execute the query
    $sqlStmtDocument= " select document_records.document_id, reel, frame, doc_types.doc_order , doc_date, names_in_documents.role, names.name, names.code, names_in_documents.marginalia, null  from  document_records STRAIGHT_JOIN names_in_documents STRAIGHT_JOIN names, doc_types where doc_types.doc_type = document_records.doc_type and document_records.document_id =  names_in_documents.document_id and names_in_documents.name_code = names.code and names.code <> 'XXX' and document_records.gloc = '";
    $sqlStmtDocument = $sqlStmtDocument . $glocNum . "' ";
    // select names whose doc id greater or equal - used as part of offset when more than 500 documents
    if ($DocId) {
        $sqlStmtDocument = $sqlStmtDocument . " and document_records.document_id >= '" . $DocId . "' ";
    }
    $sqlStmtDocument = $sqlStmtDocument . $strOrderBy;
    $resultNames = mysqli_query($db,$sqlStmtDocument);
    // echo $sqlStmtDocument . "<BR><BR>";
 ?>

<html>
<head>
  <script language="JavaScript">
    <?php
    include("GlocDocs.js");
    ?>
  </script>
<title>Document List - The Edison Papers</title>
</head>
<style type="text/css" media="all">
@import url("new-style.css");
@import url("namesearchstyle.css");
</style>
<body>


<div id="header"> <a class="header-ru" href="#"><span class="offscreen">Rutgers, The State University of New Jersey</span></a> </div>
<?php
include("navigation.inc");
?>

<table width="750" cellpadding="0" cellspacing="0" border="0" class="searchtable"> 
    <tr valign="top"> 
        <td id="content">
<?php
    include("GlocInfoScreen.php");

    // set the Next and Previous Location order
    $NextOrder = $glocOrder + 1;
    $PrevOrder = $glocOrder - 1;
    // retrieve the previous location order
    $SqlStatement = "SELECT gloc, loc_order from locations where loc_order <= $PrevOrder order by loc_order desc limit 1";
    // echo $SqlStatement;
    $resultGloc = mysqli_query($db,$SqlStatement);
    // if a previous location order exist then record gloc as an anchor
    if ( mysqli_num_rows($resultGloc) == 1) {
        $myrow = mysqli_fetch_row($resultGloc);
        // get the Series file name
        $fileName = GetSeriesFileName($myrow[1]);
        echo "<a href=\"../NamesSearch/GlocDocuments.php?glocNum=$myrow[0]&glocOrder=$myrow[1]&GlocFileName=$fileName&start_offset=0&\" ><IMG BORDER=0 SRC=\"/webimages/prevtext.gif\" height=\"47\" width=\"41\" ALT=\"Previous Text\" ></a>&nbsp;&nbsp;";
    }

    //Display location in series Notes
    echo "<a href=\"../" . $GlocFileName. ".htm#" . $glocNum . "\"><IMG BORDER=0 SRC=\"/webimages/whichnote.gif\" height=\"47\" width=\"41\" ALT=\"Where am I?\" ></a>&nbsp;&nbsp;";

    

    // retrieve the next location order
    $SqlStatement = "SELECT gloc, loc_order from locations where loc_order >= $NextOrder order by loc_order asc limit 1";
    // echo $SqlStatement;
    $resultGloc = mysqli_query($db,$SqlStatement);
    // if a Next location order exist then record gloc as an anchor
    if ( mysqli_num_rows($resultGloc) == 1) {
        $myrow = mysqli_fetch_row($resultGloc);
        // get the Series file name
        $fileName = GetSeriesFileName($myrow[1]);
        echo "<a href=\"../NamesSearch/GlocDocuments.php?glocNum=$myrow[0]&glocOrder=$myrow[1]&GlocFileName=$fileName&start_offset=0&\" ><IMG BORDER=0 SRC=\"/webimages/nexttext.gif\" height=\"47\" width=\"41\" ALT=\"Next Text\" ></a>";
    }

    echo "</DIV><BR><BR>";

    printf("<form name=GlocInfo >");

    //Display gloc information on screen for this gloc with no hidden fields
    GlocInfoScreen($glocNum, false);

    echo "</form>";

    // skip(do not display Document info)  if no documents to display
    if ( $rowCount != 0 )
    {
        echo "<form name=GlocDocuments method=POST action=\"DocDetImage.php\" target=_parent >";
        echo "<H5>Document Count: " . $rowCount;
        if ( $start_offset != 0 ) {
            $start_offset = $start_offset - 500;       // the offset for previous group
            echo "&nbsp;&nbsp;<a href=\"GlocDocuments.php?glocNum=$glocNum&glocOrder=$glocOrder&GlocFileName=$GlocFileName&start_offset=$start_offset&\" ><IMG BORDER=1 SRC=\"/graphics/prvpage.jpg\" ALT=\"Previous Page\" ></a>&nbsp;&nbsp;";
            $start_offset = $start_offset + 500;       // restore the original offset
        }
        if ( $rowCount == 500 ) {
            $start_offset = 500 + $start_offset;       // the offset for next group
            echo "&nbsp;&nbsp;<a href=\"GlocDocuments.php?glocNum=$glocNum&glocOrder=$glocOrder&GlocFileName=$GlocFileName&start_offset=$start_offset&\" ><IMG BORDER=1 SRC=\"/graphics/nxtpage.jpg\" ALT=\"Next Page\" ></a>";
        }
        echo "<BR></H5><I>Check the documents you would like to see, then click the \"Show Documents\" button; to select them all, just click the button.</I>";
        echo "<BR><center><input type=\"button\" name=clrDocs value=\"Clear Checkboxes\" onClick=\"parent.clrDocs()\" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=submit name=showDoc value=\"Show Documents\" onClick=\"return chkDocs()\" ></center><BR>";
        echo "<input type=hidden name=norpttype value=RecordsByTime>";

        //build Chronological Order display - pass name/subjects/Document resultset and Names selected
        include("DocumentDateForm.php");
        DocumentDateForm($resultNames, $resultSubject, $resultDocuments, "", "GlocDocuments");

        // display show document button if more than 9 documents otherwise display hidden as a spaceholder
        if ($rowCount > 9)
        {
                echo "<BR><center><input type=submit name=showDoc value=\"Show Documents\" onClick=\"return chkDocs()\" ></center><BR>";
        }
        else
        {
                echo "<BR><center><input type=hidden name=spaceholder ><BR>";
        }
        // end form
        echo "</form>";
    }
    else
    {
        echo "<BR><CENTER><B> no Documents found </B><BR>";
    }

// echo "complete";
	echo"</td>";
	echo "</tr>";
	echo "</table>";
	echo "<script type='text/javascript'>function Go(){return}</script>";
include("testfooter.php");
?>
