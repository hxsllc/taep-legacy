<?php
    // Display form in Reel Frame and Chronological Order
    function DocumentDateForm($resultNames, $resultSubjects, $resultDocuments, $arrNameCode, $rpttype ) {

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
            // for specified Document id, add anchor with href to name search page
            // with encoded Name if name do not match
            while ($myrowNames[0] == $DocId)
            {
                // check if name matches in which case - no href anchor
                $numNames = 0;
                while( @count($arrNameCode) > $numNames )
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
                    $marginalia = $marginalia . " " . htmlentities($myrowNames[6]);
                }
                // organize Authors, Receipients and Name Mentions
                // display italic conjecture for Document view page only
                switch ($myrowNames[5])
                {
                case 'A':
                    if ( $numNames == -1) {
                        if ( $myrowNames[9] && $rpttype == "DocImage") {
                            $NamesAuthor = $NamesAuthor . htmlentities($myrowNames[6]) . " <I>[supplied or conjectured]</I>#";
                        }
                        else
                        {
                            $NamesAuthor = $NamesAuthor . htmlentities($myrowNames[6]) . "#";
                        }
                    }
                    else
                    {
                        if ( $myrowNames[9] && $rpttype == "DocImage" ) {
                            $NamesAuthor = $NamesAuthor . "<a target=_parent href=\"http://edisonlegacy.reclaim.hosting/NamesSearch.php?name=" . rawurlencode($myrowNames[6]) . "&namecode=" . rawurlencode($myrowNames[7]) . "&\" > " . htmlentities($myrowNames[6]) . "</a>" . " <I>[supplied or conjectured]</I># " ;
                        }
                        else
                        {
                            $NamesAuthor = $NamesAuthor . "<a target=_parent href=\"http://edisonlegacy.reclaim.hosting/NamesSearch.php?name=" . rawurlencode($myrowNames[6]) . "&namecode=" . rawurlencode($myrowNames[7]) . "&\" > " . htmlentities($myrowNames[6]) . "</a>" . "# " ;
                        }
                    }
                    break;
                case 'R':
                    if ( $numNames == -1) {
                        if ( $myrowNames[9] && $rpttype == "DocImage" ) {
                            $NamesRecipient = $NamesRecipient . htmlentities($myrowNames[6]) . " <I>[supplied or conjectured]</I>#";
                        }
                        else
                        {
                            $NamesRecipient = $NamesRecipient . htmlentities($myrowNames[6]) . "#";
                        }
                    }
                    else
                    {
                        if ( $myrowNames[9] && $rpttype == "DocImage" ) {
                            $NamesRecipient = $NamesRecipient . "<a target=_parent href=\"http://edisonlegacy.reclaim.hosting/NamesSearch.php?name=" . rawurlencode($myrowNames[6]) . "&namecode=" . rawurlencode($myrowNames[7]) . "&\" > " . htmlentities($myrowNames[6]) . "</a>" . " <I>[supplied or conjectured]</I># ";
                        }
                        else
                        {
                            $NamesRecipient = $NamesRecipient . "<a target=_parent href=\"http://edisonlegacy.reclaim.hosting/NamesSearch.php?name=" . rawurlencode($myrowNames[6]) . "&namecode=" . rawurlencode($myrowNames[7]) . "&\" > " . htmlentities($myrowNames[6]) . "</a>" . "# ";
                        }
                    }
                    break;
                case 'N':
                    if ( $numNames == -1) {
                        if ( $myrowNames[9] && $rpttype == "DocImage" ) {
                            $NamesMention = $NamesMention . htmlentities($myrowNames[6]) . " <I>[supplied or conjectured]</I>; ";
                        }
                        else
                        {
                            $NamesMention = $NamesMention . htmlentities($myrowNames[6]) . "; ";
                        }
                    }
                    else
                    {
                        if ( $myrowNames[9] && $rpttype == "DocImage" ) {
                            $NamesMention = $NamesMention . "<a target=_parent href=\"http://edisonlegacy.reclaim.hosting/NamesSearch.php?name=" . rawurlencode($myrowNames[6]) . "&namecode=" . rawurlencode($myrowNames[7]) . "&\" >" . htmlentities($myrowNames[6]) . "</a>" . " <I>[supplied or conjectured]</I>; ";
                        }
                        else
                        {
                            $NamesMention = $NamesMention . "<a target=_parent href=\"http://edisonlegacy.reclaim.hosting/NamesSearch.php?name=" . rawurlencode($myrowNames[6]) . "&namecode=" . rawurlencode($myrowNames[7]) . "&\" >" . htmlentities($myrowNames[6]) . "</a>" . "; ";
                        }
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

            // gather subject description for specified document id
            while ($myrowSubject[0] == $DocId)
            {
                $Subjects = $Subjects . htmlentities($myrowSubject[5]) . "; ";
                $myrowSubject = mysqli_fetch_row($resultSubjects);
            }
            $Subjects = substr($Subjects, 0, strrpos($Subjects, ";"));

            // Now display document id info in properly indented format
            // Report Type of date only display date and Image while Reel/Frame
            // Report type of Film display date and Reel/frame
            // Report Type of document Type has same display format as date
            // Report Type of GlocDocuments and DocImage, display with a slight variation
            echo "<DIV STYLE=\"margin-left:4%;margin-right:5%\">";
            switch ($rpttype)
            {
                case "RecordsByTime":
                    // if images exist, display with check box otherwise do not display check box instead display reel:frame
                    if ( $myrow[12] ) {
                        echo "<input type=checkbox name=\"type[]\" value=" . $DocId . ">";
                        echo $myrow[5] .  "  (<a target=_blank href=\"http://edisonlegacy.reclaim.hosting/glocpage.php?gloc=" . rawurlencode($myrow[8]) . "&\"><IMG ALIGN=bottom BORDER=0 SRC=\"http://edisonlegacy.reclaim.hosting/graphics/OpenFolder.gif\" > $myrow[8] </a>) ";
                    }
                    else{               // skip a few spaces( where the checkbox would have appeared
                        echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $myrow[5] .  "  (<a target=_blank href=\"http://edisonlegacy.reclaim.hosting/glocpage.php?gloc=" . rawurlencode($myrow[8]) . "&\"><IMG ALIGN=bottom BORDER=0 SRC=\"http://edisonlegacy.reclaim.hosting/graphics/OpenFolder.gif\" > $myrow[8] </a>; " . $myrow[1] . ":" . $myrow[2] . ")";
                    }
                    break;
                case "RecordsByFilm":
                    // if images exist, display with check box otherwise do not display check box
                    if ( $myrow[12] ) {
                        echo "<input type=checkbox name=\"type[]\" value=" . $DocId . ">";
                    }
                    else
                    {                   // skip a few spaces( where the checkbox would have appeared)
                        echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                    }
                    echo $myrow[5] . " (" . $myrow[1] . ":" . $myrow[2] . ")";
                    break;
                case "RecordsByType":
                    // if images exist, display with check box otherwise do not display check box instead display reel:frame
                    if ( $myrow[12] ) {
                        echo "<input type=checkbox name=\"type[]\" value=" . $DocId . ">";
                        echo $myrow[5] .  "  (<a target=_blank href=\"http://edisonlegacy.reclaim.hosting/glocpage.php?gloc=" . rawurlencode($myrow[8]) . "&\"><IMG ALIGN=bottom BORDER=0 SRC=\"http://edisonlegacy.reclaim.hosting/graphics/OpenFolder.gif\" > $myrow[8] </a>) ";
                    }
                    else{               // skip a few spaces( where the checkbox would have appeared
                        echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $myrow[5] .  "  (<a target=_blank href=\"http://edisonlegacy.reclaim.hosting/glocpage.php?gloc=" . rawurlencode($myrow[8]) . "&\"><IMG ALIGN=bottom BORDER=0 SRC=\"http://edisonlegacy.reclaim.hosting/graphics/OpenFolder.gif\" > $myrow[8] </a>; " . $myrow[1] . ":" . $myrow[2] . ")";
                    }
                    break;
                case "GlocDocuments":
                    if ( $myrow[12] ) {     // if images exist, display with check box otherwise do not display check box instead display reel:frame
                        echo "<input type=checkbox name=\"type[]\" value=" . $DocId . ">";
                        echo $myrow[5];
                    }
                    else
                    {                   // skip a few spaces( where the checkbox would have appeared)
                        echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                        echo $myrow[5]. " (" . $myrow[1] . ":" . $myrow[2] . ")";
                    }
                    break;
                case "DocImage":
                    echo $myrow[5];
                    break;
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
            if ( $myrow[3] != '01') {   // if not Correspondence Type - display Document Type
                echo " -- " . $myrow[6];
            }

            echo "</DIV><DIV STYLE=\"margin-left:10%;margin-right:5%\">";
            // Display Clipping info - if Document Type is Clipping or Clippings
            if ($myrow[3] == "37" || $myrow[3] == "39")
            {
                $clipping = "";
                if ( $myrow[9] ) {   //Title
                    $clipping = "\"" . htmlentities($myrow[9]) . "\", ";
                }
                if ($myrow[10]) {    //Source
                    $clipping = $clipping . "<I>" . htmlentities($myrow[10]) . "</I>, ";
                }
                if ( $myrow[11]) {   //Author
                    $clipping = $clipping . htmlentities($myrow[11]) . ",";
                }
                $clipping = substr($clipping, 0, strrpos($clipping, ",")  );
                echo $clipping;
            }
            if ($NamesMention)
            {
                echo "(" . $NamesMention . ") ";
            }
            echo $Subjects;
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
