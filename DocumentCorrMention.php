<?php
    // This routine is called twice, once for Correspondence then again for Name mentions
    // Organize Data for Display of Correpondence and Name Mentions when sorting order by Document Type
    function DocumentCorrMention($arrNameCode, $strStartDate, $strEndDate, $roleType, $strDocType) {

    $numNames = count($arrNameCode);

    // build dynamic SQL for selecting Document IDs based on given Name code array
    // dynamic SQL leads to efficient SQL based alias of name_in_documents table
    // for correspondence - select only where name appears as author or receipient
    // for name mention - select only where name appear as Name mention
    // for no name search - select based on Doc id
    switch ($numNames) {
        case 0:
            // Use a simple SQL statement, If no names selected (1=1 simplifies building sql statement with and expression
            $sqlStmtDocument = "select document_records.document_id from document_records where 1=1 ";
            break;
        case 1:
            // SQL statement to retrieve document ids based on single name code criteria
            $sqlStmtDocument = "select document_records.document_id from  names_in_documents as NAMES1, document_records where document_records.document_id = NAMES1.document_id and NAMES1.name_code = " . $arrNameCode[0];

            // if AR then restrict only to Author and Recepients otherwise restrict only to name mention
            if ( $roleType == 'AR' ) {
                $sqlStmtDocument = $sqlStmtDocument . " and NAMES1.role in ('A','R') ";
                }
            else
            {
                $sqlStmtDocument = $sqlStmtDocument . " and NAMES1.role = '" . $roleType . "'";
                }
            break;
        default:
            // SQL statement to retrieve document ids based on name code criteria for Multiple names code
            $sqlStmtDocument = "select document_records.document_id from  names_in_documents as NAMES1, ";

            // build a dynamic From clause
            for ( $i=1; $numNames>$i; ++$i) {
                $sqlStmtDocument = $sqlStmtDocument . " names_in_documents as NAMES" . sprintf("%d", $i+1) . ",";
            }
            $commaPos = strrpos($sqlStmtDocument, ",");

            // if AR then restrict only to Author and Recepients otherwise restrict only to name mention
            if ( $roleType == 'AR' ) {
                $sqlStmtDocument = substr($sqlStmtDocument, 0, $commaPos) . ", document_records where document_records.document_id = NAMES1.document_id and NAMES1.role in ('A','R') and NAMES1.name_code = " . $arrNameCode[0];
                }
            else
            {
                $sqlStmtDocument = substr($sqlStmtDocument, 0, $commaPos) . ", document_records where document_records.document_id = NAMES1.document_id and NAMES1.role = '" . $roleType . "' and NAMES1.name_code = " . $arrNameCode[0];
                }

                    // build a dynamic where clause
            for ( $i=1; $numNames>$i; ++$i) {
                $sqlStmtDocument = $sqlStmtDocument . " and NAMES" . sprintf("%d", $i+1) . ".name_code = " . $arrNameCode[$i] . " and NAMES1.document_id = NAMES" . sprintf("%d", $i+1) . ".document_id ";
                // if AR then restrict only to Author and Recepients otherwise restrict only to name mention
                if ( $roleType == 'AR' ) {
                    $sqlStmtDocument = $sqlStmtDocument . " and NAMES" . sprintf("%d", $i+1) .  ".role in ('A','R')" ;
                }
                else
                {
                    $sqlStmtDocument = $sqlStmtDocument . " and NAMES" . sprintf("%d", $i+1) .  ".role = '" . $roleType . "'";
                }
            }
            break;
     }

    // assign Document type criteria of Correspondence only(1,2,3,4) if not Names mention
    if ($roleType != 'N')
    {
        $sqlStmtDocument = $sqlStmtDocument . " and document_records.doc_type in ('01', '02', '03', '04') ";
    }
    else
    {
        // Name mention can be any Document Type - apply doc Type criteria if it exist
        // assign Document type criteria if it exist
        if ($strDocType)
        {
            $sqlStmtDocument = $sqlStmtDocument . " and document_records.doc_type " .  $strDocType;
        }
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

    // retrieve first 500
    $sqlStmtDocument = $sqlStmtDocument .  "  limit 500";

    //run the query, retrieve document ids
    $result = mysqli_query($db,$sqlStmtDocument);

    // echo $sqlStmtDocument . "<BR><BR>";

    // extract rows from document_records which represent the document id to report on
    while ($myrow = mysqli_fetch_row($result))
    {
    $DocIdsArray[] = "'$myrow[0]'";           // document ids array
    }
    mysqli_free_result($result);        // release mysql resultset resources
    // if no document ID found, return
    if ( count($DocIdsArray) == 0) {
        return;
    }
    $DocIds = implode(",",$DocIdsArray) ;        // string to hold all document ids to maximum of 500( set by limit SQL )

    unset($DocIdsArray);       //release Document id Array

    // sql statement for retrieving Document Information(name, name_code and  date
    // apply document ID match criteria then descired sorting order and set the limit to first 500 then execute the query
    $sqlStmtDocument = "select names.name,  names.code, document_records.document_id,  (if(date_status.date_status = 'U', \"<I>[No Date]</I>\", if(date_status.date_status = 'M' or date_status.date_status = '8', concat(left(document_records.doc_date, 4), \"-\", right(document_records.doc_date, 4)), if (substring(document_records.doc_date,5,2) = '00' and substring(document_records.doc_date,7,2) = '00', left(document_records.doc_date,4), concat(substring(document_records.doc_date,5,2), \"/\", substring(document_records.doc_date,7,2), \"/\", left(document_records.doc_date, 4)))))) as doc_datesymbol, document_records.gloc, document_records.doc_date, N1.role, document_records.doc_type, images.ordinal, document_records.reel, document_records.frame, language.meaning, document_status.meaning ";
    switch ($roleType) {
        case 'AR':
            // retrieve only Document information for authors or recepients and whose name code does not match first entered name
            if ( $numNames != 0) {
                $strOrderBy = "order by names.name, N1.role, doc_date, N1.document_id";
                $strNameCode = " in ( " . implode(",",$arrNameCode) . ")";        //used to build name code criteria
                $sqlStmtDocument = $sqlStmtDocument . " from names,document_records LEFT JOIN date_status ON document_records.date_status = date_status.date_status, document_records as dr2 LEFT JOIN images on dr2.document_id = images.document_id, names_in_documents as N1, names_in_documents as N2, document_records as dr3 LEFT JOIN language on dr3.language = language.language, document_records as dr4 LEFT JOIN document_status on dr4.document_status = document_status.document_status where dr4.document_id = document_records.document_id and dr3.document_id = document_records.document_id and document_records.document_id = dr2.document_id and (images.ordinal is null or images.ordinal = 1) and N1.document_id = N2.document_id and names.code = N1.name_code and document_records.document_id = N1.document_id and N1.document_id in (" . $DocIds . ") and N1.role <> N2.role and N2.name_code = " . $arrNameCode[0] . " and names.code <> " . $arrNameCode[0] . " and N1.role in ('A','R') " .  $strOrderBy . " limit 500";
            }
            else
            {                           // no name search - select based on Doc id
                $strOrderBy = "order by doc_date, document_records.document_id";
                $sqlStmtDocument = "select ' ', ' ', document_records.document_id,  (if(date_status.date_status = 'U', \"<I>[No Date]</I>\", if(date_status.date_status = 'M' or date_status.date_status = '8', concat(left(document_records.doc_date, 4), \"-\", right(document_records.doc_date, 4)), if (substring(document_records.doc_date,5,2) = '00' and substring(document_records.doc_date,7,2) = '00', left(document_records.doc_date,4), concat(substring(document_records.doc_date,5,2), \"/\", substring(document_records.doc_date,7,2), \"/\", left(document_records.doc_date, 4)))))) as doc_datesymbol, document_records.gloc, document_records.doc_date, ' ', document_records.doc_type, images.ordinal, document_records.reel, document_records.frame, language.meaning, document_status.meaning ";
                $sqlStmtDocument = $sqlStmtDocument . " from document_records LEFT JOIN date_status ON document_records.date_status = date_status.date_status, document_records as dr2 LEFT JOIN images on dr2.document_id = images.document_id, document_records as dr3 LEFT JOIN language on dr3.language = language.language, document_records as dr4 LEFT JOIN document_status on dr4.document_status = document_status.document_status where dr4.document_id = document_records.document_id and dr3.document_id = document_records.document_id and document_records.document_id = dr2.document_id and (images.ordinal is null or images.ordinal = 1) and document_records.document_id in (" . $DocIds . ")  " .  $strOrderBy . " limit 500";
            }
            break;
        default:
            // retrieve all document information who match names codes who are name mentions
            $sqlStmtDocument = "select document_records.document_id, document_records.reel, document_records.frame, doc_types.doc_type, doc_types.doc_order,(if(date_status.date_status = 'U', \"<I>[No Date]</I>\", if(date_status.date_status = 'M' or date_status.date_status = '8', concat(left(document_records.doc_date, 4), \"-\", right(document_records.doc_date, 4)), if (substring(document_records.doc_date,5,2) = '00' and substring(document_records.doc_date,7,2) = '00', left(document_records.doc_date,4), concat(substring(document_records.doc_date,5,2), \"/\", substring(document_records.doc_date,7,2), \"/\", left(document_records.doc_date, 4)))))) as doc_datesymbol, doc_types.description, document_records.doc_date, document_records.gloc, clippings.title, clippings.source, clippings.author, images.ordinal, language.meaning, document_status.meaning from doc_types, document_records LEFT JOIN clippings on document_records.document_id = clippings.document_id, document_records as dr1 LEFT JOIN date_status ON dr1.date_status = date_status.date_status, document_records as dr2 LEFT JOIN images on dr2.document_id = images.document_id, document_records as dr3 LEFT JOIN language on dr3.language = language.language, document_records as dr4 LEFT JOIN document_status on dr4.document_status = document_status.document_status where dr4.document_id = document_records.document_id and dr3.document_id = document_records.document_id and doc_types.doc_type = document_records.doc_type and document_records.document_id = dr1.document_id and document_records.document_id = dr2.document_id and (images.ordinal is null or images.ordinal = 1) and document_records.document_id in (" ;
            $strOrderBy = "order by doc_date, document_records.document_id";
            $sqlStmtDocument = $sqlStmtDocument . $DocIds . ") " .  $strOrderBy . " limit 500";
            break;
    }
    $resultDocuments = mysqli_query($db,$sqlStmtDocument);
    // echo $sqlStmtDocument . "<BR><BR>";

    // sql statement for retreiving subjects information based on Document ids match
    // apply the descired sorting order then execute the query
    $sqlStmtDocument = "select names.name,  names.code, N1.document_id, subjects.description, doc_date, N1.role ";
    switch ($roleType) {
        case 'AR':
            // retrieve only subject information for recipients or authors whose name does not match first entered name
            if ( $numNames != 0) {
                $strOrderBy = "order by names.name, N1.role, doc_date, N1.document_id";
                $sqlStmtDocument = $sqlStmtDocument . " from subjects_in_documents STRAIGHT_JOIN subjects, names, document_records, names_in_documents as N1, names_in_documents as N2 where N1.document_id = N2.document_id and subjects.subject = subjects_in_documents.subject and subjects_in_documents.document_id  = N1.document_id and names.code = N1.name_code and N1.document_id = document_records.document_id and N1.document_id  in (" . $DocIds . ") and N1.role <> N2.role and N2.name_code = " . $arrNameCode[0] . " and names.code <> " . $arrNameCode[0] . " and N1.role in ('A','R') " .  $strOrderBy ;
            }
            else
            {                           // no name search -based solely on Doc id
                $strOrderBy = "order by doc_date, document_records.document_id";
                $sqlStmtDocument = "select ' ',  ' ', document_records.document_id, subjects.description, ' ', ' ' ";
                $sqlStmtDocument = $sqlStmtDocument . " from subjects_in_documents STRAIGHT_JOIN subjects, document_records where subjects.subject = subjects_in_documents.subject and subjects_in_documents.document_id = document_records.document_id and subjects_in_documents.document_id  in (" . $DocIds . ")  " .  $strOrderBy ;
            }
            break;
        default:
            // retrieve all subject information who match names codes who are name mentions
            $sqlStmtDocument = "select document_records.document_id, reel, frame, doc_types.doc_order, doc_date, subjects.description from doc_types, subjects_in_documents STRAIGHT_JOIN subjects,document_records where doc_types.doc_type = document_records.doc_type and subjects.subject = subjects_in_documents.subject and subjects_in_documents.document_id  = document_records.document_id and subjects_in_documents.document_id in (";
            $sqlStmtDocument = $sqlStmtDocument . $DocIds . ") " .  $strOrderBy;
            break;
    }
    $resultSubject = mysqli_query($db,$sqlStmtDocument);
    // echo $sqlStmtDocument . "<BR><BR>";

    // sql statement for retreiving names information based on Document ids match
    // apply the descired sorting order then execute the query
    $sqlStmtDocument= " select NAMES2.name, NAMES2.code, NAMESDOC1.document_id, ' ', ' ', ' ', document_records.doc_date, NAMESDOC3.role, NAMESDOC3.marginalia ";
    switch ($roleType) {
        case 'AR':
            // retrieve only names information for recipients or authors whose name codes do not match first entered name
            if ( $numNames != 0) {
                $strOrderBy = "order by NAMES1.name, NAMESDOC1.role, document_records.doc_date, NAMESDOC1.document_id";
                // $sqlStmtDocument = $sqlStmtDocument . " from document_records, names_in_documents as NAMESDOC2 STRAIGHT_JOIN names as NAMES2, names_in_documents as NAMESDOC1 STRAIGHT_JOIN names as NAMES1 where NAMESDOC1.document_id = document_records.document_id and NAMES1.code = NAMESDOC1.name_code and NAMES1.code <> 'XXX' and NAMESDOC1.document_id in (" . $DocIds . ") and NAMES2.code = NAMESDOC2.name_code and NAMESDOC2.name_code = " . $arrNameCode[0] . " and NAMESDOC1.role in ('A','R') and NAMESDOC2.document_id = NAMESDOC1.document_id and NAMESDOC2.role <> NAMESDOC1.role and NAMES1.code <> " . $arrNameCode[0] . " " .  $strOrderBy;
                $sqlStmtDocument = $sqlStmtDocument . " from document_records, names_in_documents as NAMESDOC2, names_in_documents as NAMESDOC3 STRAIGHT_JOIN names as NAMES2, names_in_documents as NAMESDOC1 STRAIGHT_JOIN names as NAMES1 where NAMESDOC1.document_id = document_records.document_id and NAMES1.code = NAMESDOC1.name_code and NAMES1.code <> 'XXX' and NAMESDOC1.document_id in (" . $DocIds . ")  and NAMES2.code = NAMESDOC3.name_code and NAMESDOC3.document_id = NAMESDOC2.document_id and NAMESDOC2.name_code = " . $arrNameCode[0] . " and NAMESDOC1.role in ('A','R') and NAMESDOC2.document_id = NAMESDOC1.document_id and NAMESDOC2.role <> NAMESDOC1.role " .  $strOrderBy;
            }
            else
            {                           // no name search -based solely on document id
                $strOrderBy = "order by doc_date, NAMESDOC1.document_id";
                $sqlStmtDocument= " select NAMES1.name, NAMES1.code, NAMESDOC1.document_id,  ' ',        ' ',         ' ',            document_records.doc_date, NAMESDOC1.role, NAMESDOC1.marginalia, null ";
                $sqlStmtDocument = $sqlStmtDocument . " from document_records, names_in_documents as NAMESDOC1,names as NAMES1 where NAMESDOC1.document_id = document_records.document_id and NAMES1.code = NAMESDOC1.name_code and NAMES1.code <> 'XXX' and NAMESDOC1.document_id  in (" . $DocIds . ") " .  $strOrderBy;
            }
            break;
        default:
            // retrieve all names information who match names codes who are name mentions
            $sqlStmtDocument= " select document_records.document_id, reel, frame, doc_types.doc_order , doc_date, names_in_documents.role, names.name, names.code, names_in_documents.marginalia, null from doc_types, names, document_records, names_in_documents where doc_types.doc_type = document_records.doc_type and document_records.document_id =  names_in_documents.document_id and names_in_documents.name_code = names.code and names.code <> 'XXX' and names_in_documents.document_id  in (";
            $sqlStmtDocument = $sqlStmtDocument . $DocIds . ") " .  $strOrderBy;
            break;
    }
    $resultNames = mysqli_query($db,$sqlStmtDocument);
    // echo $sqlStmtDocument . "<BR><BR>";

    // display Document info based on Report Type
    switch ($roleType) {
        case 'N':
            // build Document Names Mention Display Just Like Chronological Order Display
            echo "<B> Name Mentions </B><BR>";
            DocumentDateForm($resultNames, $resultSubject, $resultDocuments, $arrNameCode, "RecordsByType");
            break;
        default :
            // build Document Correspondence Display
            DocumentCorrForm($resultNames, $resultSubject, $resultDocuments, $arrNameCode);
            break;
    }
    }

?>
