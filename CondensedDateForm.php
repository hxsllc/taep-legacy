<?php
    // Display Correspondense Condensed form for Reel Frame and Chronological Order
    // also use to Display Names Mentions in Condensed Form
    function CondensedDateForm($resultNames, $resultDocuments, $rptType, $arrNameCode ) {

        // fetch first row from Names resultset
        $myrowNames = mysqli_fetch_row($resultNames);

        // loop through in Document Info resultset gathering Authors and Recipient
        while ($myrow = mysqli_fetch_row($resultDocuments))
        {
            $NamesAuthor = "";       // initialize author and recipient
            $NamesRecipient = "";
            $DocId = $myrow[0];        // set the document id to report on

            //Since it is possible to possible to have Documents which are considered Name mention only
            //this logic skip to next name in favor of complicated SQL
            // is is possible to pull a no name search which has no authors or recepients
            // which will cause imbalance between Document resultset and Names resultset
            if ( @count($arrNameCode) != 0) {
                while ($myrowNames[0] != $DocId)
                {
                    $myrowNames = mysqli_fetch_row($resultNames);
                }
            }
            // gather and separate names into Authors and recipient
            // for specified Document id, extract each name up to first comma
            while ($myrowNames[0] == $DocId)
            {
                 // Skip a name, if its code is " No One "
                 if (  $myrowNames[7] != "XXX") {
                    switch ($myrowNames[5])
                    {
                    case 'A':
                        if ( strchr($myrowNames[6], ",") ) {
                            $NamesAuthor = $NamesAuthor . " ". htmlspecialchars(substr($myrowNames[6], 0, strpos($myrowNames[6], ','))) . "#";
                        }
                        else
                        {
                            $NamesAuthor = $NamesAuthor . " " . htmlspecialchars($myrowNames[6]) . "#";
                        }
                        break;
                    case 'R':
                        if ( strchr($myrowNames[6], ",") ) {
                            $NamesRecipient = $NamesRecipient . " " . htmlspecialchars(substr($myrowNames[6], 0, strpos($myrowNames[6], ','))) . "#";
                        }
                        else
                        {
                            $NamesRecipient = $NamesRecipient . " " . htmlspecialchars($myrowNames[6]) . "#";
                        }
                        break;
                    }
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

            // Display Date with Document IDs as a parameter for anchor
            echo "<DIV STYLE=\"margin-left:4%;margin-right:5%\">";
            echo "<a target=\"ImageResult\" href=\"/DocImage.php?DocId=" . $DocId . "&";
            echo "\">" . $myrow[5] . "</a> &nbsp;";

            // If Reel/Frame Display,  then display Reel and Frame
            if ( $rptType == "RecordsByFilm") {
                echo "(" . $myrow[1] . ":" . $myrow[2] . ") ";
            }

            echo $NamesAuthor;

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
            // if Correspondense type then do not display Document Type Description
            if ( $myrow[3] != '01') {
                echo " --" . htmlspecialchars($myrow[6]);
            }
            echo "</DIV>";
        }
    }

?>
