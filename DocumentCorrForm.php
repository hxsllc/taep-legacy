<?php
    // Display Corresponding Document Type
    function DocumentCorrForm($resultNames, $resultSubjects, $resultDocuments, $arrNameCode) {

        // fetch first row from Names and Subject resultset
        $myrowNames = mysqli_fetch_row($resultNames);
        $myrowSubject = mysqli_fetch_row($resultSubjects);

        // loop through in Document Info resultset gathering Name Mentions and Subject Description
        $NameCode = "";
        $roleType = "";
        $noNameFlag = true;          // no name search flag is set-- display Correspondence header
        while ($myrow = mysqli_fetch_row($resultDocuments))
        {

            $NamesAuthor = "";       // initialize author, recipient, mention and subject description
            $NamesRecipient = "";
            $NamesMention = "";            // initialize name mention and subject description
            $Subjects = "";
            $marginalia = "";         // initialize marginalia

            $DocId = $myrow[2];           // set the document id to report on
            if ( count($arrNameCode) != 0 ) {
                // for name search, set Document name group header("To or From") with a named anchor, if not already set
                if ( $NameCode !=  $myrow[1] || $roleType != $myrow[6]) {
                    $roleType = $myrow[6];          //  set the role type to report on
                    $NameCode = $myrow[1];          // set the NameCode to report on
                    if ( $myrow[6] == 'R' )
                    {
                        echo "<B> To " . "<a href=\"http://edisonlegacy.reclaim.hosting/NamesSearch.php?name=" . urlencode($myrow[0]) . "&namecode=" . urlencode($myrow[1]) . "\" > " . htmlspecialchars($myrow[0]) . "</a>" . "</B><BR>";
                    }
                    else
                    {
                        echo "<B> From " . "<a href=\"http://edisonlegacy.reclaim.hosting/NamesSearch.php?name=" . urlencode($myrow[0]) . "&namecode=" . urlencode($myrow[1]) . "\" > " . htmlspecialchars($myrow[0]) . "</a>" . "</B><BR>";
                    }
                }
            }
            else
            {
                // display Correspondence header first time only - no name search
                if ( $noNameFlag ) {
                    echo "<B> Correspondence </B><BR>";
                    $noNameFlag = false;
                }
            }
            // on a no name search - select Author, Receipient and Name Mention
            if ( count($arrNameCode) == 0 ) {
                // gather and separate names into Authors, recipient and mentions
                // for specified Document id, add anchor with href to name search page with encoded Name if name do not match
                while ($myrowNames[2] == $DocId)
                {
                        // gather all marginalias
                        if ( $myrowNames[8] ) {
                                $marginalia = $marginalia . " " . htmlspecialchars($myrowNames[0]);
                        }
                        switch ($myrowNames[7])
                        {
                        case 'A':
                                $NamesAuthor = $NamesAuthor . "<a target=_parent href=\"http://edisonlegacy.reclaim.hosting/NamesSearch.php?name=" . urlencode($myrowNames[0]) . "&namecode=" . urlencode($myrowNames[1]) . "&\" > " . htmlspecialchars($myrowNames[0]) . "</a>" . "# " ;
                                break;
                        case 'R':
                                $NamesRecipient = $NamesRecipient . "<a target=_parent href=\"http://edisonlegacy.reclaim.hosting/NamesSearch.php?name=" . urlencode($myrowNames[0]) . "&namecode=" . urlencode($myrowNames[1]) . "&\" > " . htmlspecialchars($myrowNames[0]) . "</a>" . "# ";
                                break;
                        case 'N':
                                $NamesMention = $NamesMention . "<a target=_parent href=\"http://edisonlegacy.reclaim.hosting/NamesSearch.php?name=" . urlencode($myrowNames[0]) . "&namecode=" . urlencode($myrowNames[1]) . "&\" >" . htmlspecialchars($myrowNames[0]) . "</a>" . "; ";
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
            }
            else {
                // on a name search gather and select name mentions
                // for specified name code & Document Id, add anchor with href to name search page with encoded Name
                // while ($myrowNames[1] == $NameCode and $DocId == $myrowNames[2])
                while ($DocId == $myrowNames[2])
                {
                        // gather all marginalias
                        if ( $myrowNames[8] ) {
                                $marginalia = $marginalia . " " . htmlspecialchars($myrowNames[0]);
                        }
                        // gather all name mentions
                        if ($myrowNames[7] == 'N' )
                        {
                                $NamesMention = $NamesMention . "<a href=\"http://edisonlegacy.reclaim.hosting/NamesSearch.php?name=" . urlencode($myrowNames[0]) . "&namecode=" . urlencode($myrowNames[1]) . "\" >" . htmlspecialchars($myrowNames[0]) . "</a>" . "; ";
                        }
                        $myrowNames = mysqli_fetch_row($resultNames);
                }
                $NamesMention = substr($NamesMention , 0, strrpos($NamesMention , ";") );
            }
            // name search - It is possible that the same document appear more than once under
            // a different name
            if ( count($arrNameCode) != 0 ) {
                // gather subject description for specified document id & name code
                while ($myrowSubject[1] == $NameCode and $DocId == $myrowSubject[2])
                {
                    $Subjects = $Subjects . htmlspecialchars($myrowSubject[3]) . "; ";
                    $myrowSubject = mysqli_fetch_row($resultSubjects);
                }
                $Subjects = substr($Subjects, 0, strrpos($Subjects, ";"));
            }
            else
            {
                // gather subject description for specified document id
                while ( $DocId == $myrowSubject[2])
                {
                    $Subjects = $Subjects . htmlspecialchars($myrowSubject[3]) . "; ";
                    $myrowSubject = mysqli_fetch_row($resultSubjects);
                }
                $Subjects = substr($Subjects, 0, strrpos($Subjects, ";"));
            }

            // Now display Document info(doc ID, date, Authors/Recepient, Name Mention & Subjects) in properly indented format
            echo "<DIV STYLE=\"margin-left:4%;margin-right:5%\">";
            // if images exist, display with check box otherwise do not display check box instead display reel:frame
            if ( $myrow[8] ) {
                echo "<input type=checkbox name=\"type[]\" value=" . $DocId . ">";
                echo $myrow[3] . "  (<a target=_blank href=\"http://edisonlegacy.reclaim.hosting/glocpage.php?gloc=" . urlencode($myrow[4]) . "&\"><IMG ALIGN=bottom BORDER=0 SRC=\"http://edisonlegacy.reclaim.hosting/graphics/OpenFolder.gif\" > $myrow[4] </a>) ";
            }
            else
            {
                echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $myrow[3] . "  (<a target=_blank href=\"http://edisonlegacy.reclaim.hosting/glocpage.php?gloc=" . urlencode($myrow[4]) . "&\"><IMG ALIGN=bottom BORDER=0 SRC=\"http://edisonlegacy.reclaim.hosting/graphics/OpenFolder.gif\" > $myrow[4] </a>; " . $myrow[9] . ":" . $myrow[10] . ")";
            }
            // if no name seach - display author and receipients
            if ( count($arrNameCode) == 0 ) {
                echo " " . $NamesAuthor;

                //determine the joining factor by using document type if Recipient exist
                if ( $NamesRecipient) {
                    if ( ($myrow[7] == "0") or ($myrow[7] == "1") or ($myrow[7] == "2") or ($myrow[7] == "3") or ($myrow[7] == "4") or ($myrow[7] == "33") or ($myrow[7] == "46") or ($myrow[7] == "53") or ($myrow[7] == "57") or ($myrow[7] == "58") or ($myrow[7] == "59") or ($myrow[7] == "61") or ($myrow[7] == "63") or ($myrow[7] == "65") or ($myrow[7] == "66") or ($myrow[7] == "78") or ($myrow[7] == "81") )
                        {
                        echo " to ";
                        }
                    else if ($myrow[7] == "48")
                        {
                        echo " v. ";
                        }
                    else if ($myrow[7] == "67")
                        {
                        echo " with ";
                        }
                    else if ($myrow[7] == "83")
                        {
                        echo " about ";
                        }
                    else
                        {
                        echo " and ";                //default
                        }
                    echo $NamesRecipient;
                }
            }
            else
            {
                echo "<BR>";
            }

            echo "</DIV><DIV STYLE=\"margin-left:10%;margin-right:5%\">";
            if ($NamesMention)
            {
                echo "(" . $NamesMention . ") ";
            }
            echo $Subjects;
            // display document status, language and marginalia
            if ( $myrow[11] || $myrow[12] || $marginalia ) {
                $DocInfo = " [";
                if ( $myrow[11] ) {     //display document status
                    $DocInfo = $DocInfo . $myrow[11] . "; ";
                }
                if ( $myrow[12] ) {     // display language
                    $DocInfo = $DocInfo . $myrow[12] . "; ";
                }
                if ( $marginalia ) {    // display marginalia
                    $DocInfo = $DocInfo . "Marginalia by " . $marginalia . "; ";
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
