<?php
    // Organize Condensed Data for Display of Correpondence and Name Mentions in Condensed Form
    // when sorting order by Document Type
    function CondensedCorrMention($roleType, $DocIds, $arrNameCode) {

    $numNames = count($arrNameCode);

    // build dynamic SQL for pulling filtered Document IDs based on Selected Document IDs and Name code array
    // dynamic SQL leads to efficient SQL based alias of name_in_documents table
    // the reason for this name filter is Correspondence Document Type restrict by name to Author or Receipient
    // while restrict Name mentions only to names in Name Mentions
    switch ($numNames) {
        case 0:
            // SQL statement to retrieve document ids based on single name code criteria
            $sqlStmtDocument = "select document_records.document_id from  names_in_documents as NAMES1, document_records where document_records.document_id = NAMES1.document_id and NAMES1.document_id " . $DocIds;
            break;
        case 1:
            // SQL statement to retrieve document ids based on single name code criteria
            $sqlStmtDocument = "select document_records.document_id from  names_in_documents as NAMES1, document_records where document_records.document_id = NAMES1.document_id and NAMES1.name_code = " . $arrNameCode[0];
            // if AR then restrict only to Author and Recepients otherwise restrict only to name mention
            if ( $roleType == 'AR' ) {
                $sqlStmtDocument = $sqlStmtDocument . " and NAMES1.document_id " . $DocIds . "  and NAMES1.role in ('A','R') ";
                }
            else
            {
                $sqlStmtDocument = $sqlStmtDocument . " and NAMES1.document_id " . $DocIds . "  and NAMES1.role = '" . $roleType . "'";
                }
            break;
        default:
            // SQL statement to retrieve document ids based on name code criteria for Multiple names code
            $sqlStmtDocument = "select document_records.document_id from  names_in_documents as NAMES1, ";

            // build a dynamic From clause
            for ( $i=1; count($arrNameCode)>$i; ++$i) {
                $sqlStmtDocument = $sqlStmtDocument . " names_in_documents as NAMES" . sprintf("%d", $i+1) . ",";
            }
            $commaPos = strrpos($sqlStmtDocument, ",");
            // if AR then restrict only to Author and Recepients otherwise restrict only to name mention
            if ( $roleType == 'AR' ) {
                $sqlStmtDocument = substr($sqlStmtDocument, 0, $commaPos) . ", document_records where document_records.document_id = NAMES1.document_id and NAMES1.document_id " . $DocIds .  "  and NAMES1.role in ('A','R') and NAMES1.name_code = " . $arrNameCode[0];
                }
            else
            {
                $sqlStmtDocument = substr($sqlStmtDocument, 0, $commaPos) . ", document_records where document_records.document_id = NAMES1.document_id and NAMES1.document_id " . $DocIds .  " and NAMES1.role = '" . $roleType . "' and NAMES1.name_code = " . $arrNameCode[0];
                }

            // build a dynamic where clause
            for ( $i=1; count($arrNameCode)>$i; ++$i) {
                $sqlStmtDocument = $sqlStmtDocument . " and NAMES" . sprintf("%d", $i+1) . ".name_code = " . $arrNameCode[$i] . " and NAMES1.document_id = NAMES" . sprintf("%d", $i+1) . ".document_id ";
                // if AR then restrict only to Author and Recepients otherwise restrict only to name mention
                if ( $roleType == 'AR' ) {
                    $sqlStmtDocument = $sqlStmtDocument . " and NAMES" . sprintf("%d", $i+1) . ".document_id " . $DocIds .  " and NAMES" . sprintf("%d", $i+1) .  ".role in ('A','R')" ;
                }
                else
                {
                    $sqlStmtDocument = $sqlStmtDocument . " and NAMES" . sprintf("%d", $i+1) . ".document_id " . $DocIds .  " and NAMES" . sprintf("%d", $i+1) .  ".role = '" . $roleType . "'";
                }
            }
            break;
    }

    // assign Document type criteria of Correspondence only if not Names mention
    // Names Mention can be any Document Type
    if ($roleType != 'N')
    {
        $sqlStmtDocument = $sqlStmtDocument . " and document_records.doc_type in ('01', '02', '03','04') ";
    }

    // echo $sqlStmtDocument . "<BR><BR>";

    //run the query, retrieve document ids
    $result = mysqli_query($db,$sqlStmtDocument);

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
    $DocIds = " in (" . implode(",",$DocIdsArray) . " ) ";        // string to hold all document ids that were selected

    unset($DocIdsArray);       //release Document id Array

    // Condensed sql statement for retrieving Document Information(name, name_code and  date
    // apply document ID match criteria then desired sorting order and then execute the query
    // Correspondence require a different Sql statement from Name Mentions
    $sqlStmtDocument = "select names.name,  names.code, document_records.document_id,  (if(date_status.date_status = 'U', \"<I>[No Date]</I>\", if(date_status.date_status = 'M' or date_status.date_status = '8', concat(left(document_records.doc_date, 4), \"-\", right(document_records.doc_date, 4)), if (substring(document_records.doc_date,5,2) = '00' and substring(document_records.doc_date,7,2) = '00', left(document_records.doc_date,4), concat(substring(document_records.doc_date,5,2), \"/\", substring(document_records.doc_date,7,2), \"/\", left(document_records.doc_date, 4)))))) as doc_datesymbol, document_records.gloc, doc_date, N1.role ";
    switch ($roleType) {
        case 'AR':
            // include name in criteria if name search exist
            if ( $numNames != 0) {
                $strOrderBy = "order by names.name, N1.role,doc_date, N1.document_id";
                $strNameCode = " in ( " . implode(",",$arrNameCode) . ")";        //used to build name code criteria
                // retrieve only Document information for recipients or authors
                $sqlStmtDocument = $sqlStmtDocument . " from document_records LEFT JOIN date_status ON document_records.date_status = date_status.date_status, names, names_in_documents as N1, names_in_documents as N2 where N1.document_id = N2.document_id and names.code = N1.name_code and document_records.document_id = N1.document_id and N1.document_id " . $DocIds . " and document_records.doc_type in ('01','02','03','04') and N1.role <> N2.role and N2.name_code = " . $arrNameCode[0] . " and names.code <> " . $arrNameCode[0] . " and N1.role in ('A','R') " .  $strOrderBy;
            }
            else
            {
                // retrieve only Document information for recipients or authors on no name search
                $strOrderBy = "order by doc_date, document_records.document_id";
                $sqlStmtDocument = "select document_records.document_id,  (if(date_status.date_status = 'U', \"<I>[No Date]</I>\", if(date_status.date_status = 'M' or date_status.date_status = '8', concat(left(document_records.doc_date, 4), \"-\", right(document_records.doc_date, 4)), if (substring(document_records.doc_date,5,2) = '00' and substring(document_records.doc_date,7,2) = '00', left(document_records.doc_date,4), concat(substring(document_records.doc_date,5,2), \"/\", substring(document_records.doc_date,7,2), \"/\", left(document_records.doc_date, 4)))))) as doc_datesymbol, document_records.gloc, doc_date ";
                $sqlStmtDocument = $sqlStmtDocument . " , document_records.doc_type from document_records LEFT JOIN date_status ON document_records.date_status = date_status.date_status  where document_records.document_id " . $DocIds . " and document_records.doc_type in ('01','02','03','04')  " .  $strOrderBy;
            }
            break;
        default:
            // retrieve all document information who match names codes who are name mentions
            $sqlStmtDocument = "select document_records.document_id, reel, frame, doc_types.doc_type, doc_types.doc_order,(if(date_status.date_status = 'U', \"<I>[No Date]</I>\", if(date_status.date_status = 'M' or date_status.date_status = '8', concat(left(document_records.doc_date, 4), \"-\", right(document_records.doc_date, 4)), if (substring(document_records.doc_date,5,2) = '00' and substring(document_records.doc_date,7,2) = '00', left(document_records.doc_date,4), concat(substring(document_records.doc_date,5,2), \"/\", substring(document_records.doc_date,7,2), \"/\", left(document_records.doc_date, 4)))))) as doc_datesymbol, doc_types.description, doc_date, document_records.gloc from doc_types, document_records LEFT JOIN date_status ON document_records.date_status = date_status.date_status where doc_types.doc_type = document_records.doc_type and document_records.document_id " ;

            $strOrderBy = "order by doc_date, document_records.document_id";
            $sqlStmtDocument = $sqlStmtDocument . $DocIds . " " .  $strOrderBy;
            break;
    }
    // echo $sqlStmtDocument . "<BR><BR>";
    $resultDocuments = mysqli_query($db,$sqlStmtDocument);


    // if no name search, display correspondence with Authors and Receipents
    // if name search, no need to display names
    if ( count($arrNameCode) == 0 ) {
        // Condensed sql statement for retreiving authors & recipients only no name mentions based on Document ids match
        // apply the descired sorting order then execute the query
        $sqlStmtDocument = " select document_records.document_id, reel, frame, doc_types.doc_order , doc_date, names_in_documents.role, names.name, names.code, doc_types.doc_type from doc_types, names, document_records, names_in_documents where document_records.doc_type in ('01','02','03','04') and names_in_documents.role in ('A','R') and doc_types.doc_type = document_records.doc_type and document_records.document_id =  names_in_documents.document_id and names_in_documents.role <> 'N' and names_in_documents.name_code = names.code and names_in_documents.document_id ";
        $sqlStmtDocument = $sqlStmtDocument . " " . $DocIds . " " .  $strOrderBy;
        // echo $sqlStmtDocument . "<BR><BR>";
        $resultNames = mysqli_query($db,$sqlStmtDocument);
    }

    // if Name mention, retrieve name mentions for Name mention Document type breakdown
    if ( $roleType == 'N' ) {
             // retrieve all names information who match names codes who are name mentions
            $sqlStmtDocument= " select document_records.document_id, reel, frame, doc_types.doc_order , doc_date, names_in_documents.role, names.name, names.code from doc_types, names, document_records, names_in_documents where doc_types.doc_type = document_records.doc_type and document_records.document_id =  names_in_documents.document_id and names_in_documents.name_code = names.code and names_in_documents.role <> 'N' and names_in_documents.document_id ";
            $sqlStmtDocument = $sqlStmtDocument . $DocIds . " " .  $strOrderBy;
            $resultNames = mysqli_query($db,$sqlStmtDocument);
            // echo $sqlStmtDocument . "<BR><BR>";
    }

    switch ($roleType) {
        case 'N':
            // build Document Names Mention Display Just Like Chronological Order Display
            echo "<B> Name Mentions </B><BR>";
            include("../NamesSearch/CondensedDateForm.php");
            CondensedDateForm($resultNames, $resultDocuments, "RecordsByTime", $arrNameCode);
            break;
        default :
            // build Document Correspondence Display
            // if no name search, then display authors and Recipients
            if ( count($arrNameCode) == 0 ) {
                CondensedTypeForm($resultNames, $resultDocuments, $roleType, $arrNameCode, "ZEROTYPE");
            }
            else
            {
                // Display Correspondence in Condensed form with no names(author or reciepients)
                include("../NamesSearch/CondensedCorrForm.php");
                CondensedCorrForm($resultDocuments, $arrNameCode);
            }
            break;
    }
    }

?>
