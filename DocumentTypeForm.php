<?php
    // Display form in Document Type Order
    function DocumentTypeForm($resultNames, $resultSubjects, $resultDocuments, $arrNameCode, $strStartDate, $strEndDate) {

        // fetch first row from Names and Subject resultset
        $myrowNames = mysqli_fetch_row($resultNames);
        $myrowSubject = mysqli_fetch_row($resultSubjects);

        // loop through in Document Info resultset gathering Authors, Recipient,  Mentions and Subject Description
        while ($myrow = mysqli_fetch_row($resultDocuments))
        {

            $NamesAuthor = "";       // initialize author, recipient, mention and subject description
            $NamesRecipient = "";
            $NamesMention = "";
            $Subjects = "";
            $marginalia = "";         // initialize marginalia
            $DocId = $myrow[0];        // set the document id to report on

            // gather and separate names into Authors, recipient and mentions
            // for specified Document id, add anchor with href to name search page with encoded Name if name do not match
            while ($myrowNames[0] == $DocId)
            {
                // check if name matches in which case - no href anchor
                $numNames = 0;
                while( count($arrNameCode) > $numNames )
                {
                    if ($arrNameCode[$numNames] == ("'" . $myrowNames[7] . "'"))
                    {
                        $numNames = -1;
                        break;
                    }
                    $numNames ++;
                }
                // gather all marginalias
                if ( $myrowNames[8] ) {
                    $marginalia = $marginalia . " " . htmlspecialchars($myrowNames[6]);
                }
                // organize Authors, Receipients and Name Mentions
                switch ($myrowNames[5])
                {
                case 'A':
                    if ( $numNames == -1) {
                        $NamesAuthor = $NamesAuthor . htmlspecialchars($myrowNames[6]) . "#";
                    }
                    else
                    {
                        $NamesAuthor = $NamesAuthor . "<a href=\"http://edisonlegacy.reclaim.hosting/NamesSearch.php?name=" . urlencode($myrowNames[6]) . "&namecode=" . urlencode($myrowNames[7]) . "\" > " . htmlspecialchars($myrowNames[6]) . "</a>" . "# " ;
                    }
                    break;
                case 'R':
                    if ( $numNames == -1) {
                        $NamesRecipient = $NamesRecipient . htmlspecialchars($myrowNames[6]) . "#";
                    }
                    else
                    {
                        $NamesRecipient = $NamesRecipient . "<a href=\"http://edisonlegacy.reclaim.hosting/NamesSearch.php?name=" . urlencode($myrowNames[6]) . "&namecode=" . urlencode($myrowNames[7]) . "\" > " . htmlspecialchars($myrowNames[6]) . "</a>" . "# ";
                    }
                    break;
                case 'N':
                    if ( $numNames == -1) {
                        $NamesMention = $NamesMention . htmlspecialchars($myrowNames[6]) . "; ";
                    }
                    else
                    {
                        $NamesMention = $NamesMention . "<a href=\"http://edisonlegacy.reclaim.hosting/NamesSearch.php?name=" . urlencode($myrowNames[6]) . "&namecode=" . urlencode($myrowNames[7]) . "\" >" . htmlspecialchars($myrowNames[6]) . "</a>" . "; ";
                    }
                    break;
                }
                $myrowNames = mysqli_fetch_row($resultNames);
            }
            // replace the # character with commas but first insert 'and' expression on the last #
            $NamesAuthor = substr($NamesAuthor, 0, strrpos($NamesAuthor, "#")  );
            if ( strrpos($NamesAuthor, "#") ) {
                $NamesAuthor = substr($NamesAuthor, 0, strrpos($NamesAuthor, "#")) . " and " . substr($NamesAuthor, strrpos($NamesAuthor, "#") + 1);
                $NamesAuthor = str_replace("#", ",", $NamesAuthor);
            }
            $NamesRecipient = substr($NamesRecipient, 0, strrpos($NamesRecipient, "#") );
            if ( strrpos($NamesRecipient, "#") ) {
                $NamesRecipient = substr($NamesRecipient, 0, strrpos($NamesRecipient, "#")) . " and " . substr($NamesRecipient, strrpos($NamesRecipient, "#") + 1);
                $NamesRecipient = str_replace("#", ",", $NamesRecipient);
            }
            $NamesMention = substr($NamesMention , 0, strrpos($NamesMention , ";") );

            // set Document group header, if not already set
            if ( !($DocType) ) {
                echo "<B>" . htmlspecialchars($myrow[6]) . "</B><BR>";
                $DocType = $myrow[3];
            }
            else
            {
                if ( $DocType != $myrow[3] ) {
                    echo "<B>" . htmlspecialchars($myrow[6]) . "</B><BR>";
                    $DocType = $myrow[3];
                }
            }

            // gather subject description for specified document id
            while ($myrowSubject[0] == $DocId)
            {
                $Subjects = $Subjects . htmlspecialchars($myrowSubject[5]) . "; ";
                $myrowSubject = mysqli_fetch_row($resultSubjects);
            }
            $Subjects = substr($Subjects, 0, strrpos($Subjects, ";"));

            // Now display document id info in properly indented format
            echo "<DIV STYLE=\"margin-left:4%;margin-right:5%\">";
            // if images exist, display with check box otherwise do not display check box instead display reel:frame
            if ( $myrow[12] ) {
                echo "<input type=checkbox name=\"type[]\" value=" . $DocId . ">";
                echo $myrow[5] . "  (<a target=_blank href=\"http://edisonlegacy.reclaim.hosting/glocpage.php?gloc=" . urlencode($myrow[8]) . "&\"><IMG ALIGN=bottom BORDER=0 SRC=\"http://edisonlegacy.reclaim.hosting/graphics/OpenFolder.gif\" > $myrow[8] </a>)";
            }
            else{               // skip a few spaces( where the checkbox would have appeared
                echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $myrow[5] .  "  (<a target=_blank href=\"http://edisonlegacy.reclaim.hosting/glocpage.php?gloc=" . urlencode($myrow[8]) . "&\"><IMG ALIGN=bottom BORDER=0 SRC=\"http://edisonlegacy.reclaim.hosting/graphics/OpenFolder.gif\" > $myrow[8] </a>; " . $myrow[1] . ":" . $myrow[2] . ")";
            }

            echo " " . $NamesAuthor;

            //determine the joining factor by using document type if Recipient exist
            if ( $NamesRecipient) {
                if ( ($myrow[3] == "0") or ($myrow[3] == "1") or ($myrow[3] == "2") or ($myrow[3] == "3") or ($myrow[3] == "4") or ($myrow[3] == "33") or ($myrow[3] == "46") or ($myrow[3] == "53") or ($myrow[3] == "57") or ($myrow[3] == "58") or ($myrow[3] == "59") or ($myrow[3] == "61") or ($myrow[3] == "63") or ($myrow[3] == "65") or ($myrow[3] == "66") or ($myrow[3] == "78") or ($myrow[3] == "81") )
                    {
                    echo " to ";
                    }
                else if ($myrow[3] == "48")
                    {
                    echo " v. ";
                    }
                else if ($myrow[3] == "67")
                    {
                    echo " with ";
                    }
                else if ($myrow[3] == "83")
                    {
                    echo " about ";
                    }
                else
                    {
                    echo " and ";                //default
                    }
                echo $NamesRecipient;
            }

            echo "</DIV><DIV STYLE=\"margin-left:10%;margin-right:5%\">";
            // Display Clipping info - if Document Type is Clipping or Clippings
            if ($myrow[3] == "37" || $myrow[3] == "39")
            {
                $clipping = "";
                if ( $myrow[9] ) {    //Title
                    $clipping = "\"" . htmlspecialchars($myrow[9]) . "\", ";
                }
                if ($myrow[10]) {     //Source
                    $clipping = $clipping . "<I>" . htmlspecialchars($myrow[10]) . "</I>, ";
                }
                if ( $myrow[11]) {    //Author
                    $clipping = $clipping . htmlspecialchars($myrow[11]) . ",";
                }
                $clipping = substr($clipping, 0, strrpos($clipping, ",")  );
                echo $clipping;
            }
            if ($NamesMention)
            {
                echo "(" . $NamesMention . ") ";
            }
            echo $Subjects ;
            // display document status, language and marginalia
            if ( $myrow[13] || $myrow[14] || $marginalia ) {
                $DocInfo = " [";
                if ( $myrow[14] ) {     //display document status
                    $DocInfo = $DocInfo . $myrow[14] . "; ";
                }
                if ( $myrow[13] ) {     // display language
                    $DocInfo = $DocInfo . $myrow[13] . "; ";
                }
                if ( $marginalia ) {    // display marginalia
                    $DocInfo = $DocInfo . "Marginalia by " . $marginalia . "; ";
                    echo "margin";
                }
                // truncate the trailing semicolon
                $DocInfo = substr($DocInfo, 0, strrpos($DocInfo, ";")  );
                $DocInfo = $DocInfo . "]";
                echo $DocInfo;
            }
            echo "</DIV>";
        }
    }
?>
