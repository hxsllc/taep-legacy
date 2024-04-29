<?php
    // Display condensed Document Type form( no Correspondence with name search or Name Mentions)
    //  or Display Corespondence without name search(ZEROTYPE - rpttype)
    function CondensedTypeForm($resultNames, $resultDocuments, $DocIds, $arrNameCode, $rptType) {

        // fetch first row from Names resultset
        $myrowNames = mysqli_fetch_row($db,$resultNames);

        // loop through in Document Info resultset gathering Authors and Recipient
        $noNameFlag = true;          //no name flag set to true by default
        while ($myrow = mysqli_fetch_row($resultDocuments))
        {
            $NamesAuthor = "";       // initialize author and recipient
            $NamesRecipient = "";
            $DocId = $myrow[0];        // set the document id to report on
            //Since it is possible to possible to have Documents which are considered Name mention only
            //this logic skips over the document id in favor of complicated SQL
            // is is possible to pull a no name search which has no authors or recepients
            // which will cause imbalance between Document resultset and Names resultset
            if ( count($arrNameCode) != 0  ) {
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
                            $NamesAuthor = $NamesAuthor . " " . htmlspecialchars(substr($myrowNames[6], 0, strpos($myrowNames[6], ','))) . "#";
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
            // Display Document group header, if not already set for non Correpondence Document Type
            if ( $rptType == "TYPE" ) {
                if ( !($DocType) ) {
                    echo "<B>" . htmlspecialchars($myrow[6]) . "</B>";
                    $DocType = $myrow[3];
                }
                else
                {

                    if ( $DocType != $myrow[3] ) {
                        echo "<B>" . htmlspecialchars($myrow[6]) . "</B>";
                        $DocType = $myrow[3];
                    }
                }
            }
            else
            {
                // display Correspondence header first time only
                if ( $noNameFlag ) {
                    echo "<B> Correspondence </B><BR>";
                    $noNameFlag = false;
                }
            }

            // setup the anchor with Document Ids parameter
            echo "<DIV STYLE=\"margin-left:4%;margin-right:5%\">";
            echo "<a target=\"ImageResult\" href=\"../NamesSearch/DocImage.php?DocId=" . $DocId . "&";
            // if non Correspondence Document Type(no name search) and Other Document Type have separate SQL
            // where the date field is in a difference position
            if ( $rptType == "TYPE" ) {
                echo "\">" . $myrow[5] . "</a>&nbsp;";
                $doctype = 3;           // set the exact position of Document Type position in SQL
            }
            else
            {
                echo "\">" . $myrow[1] . "</a>&nbsp;";
                $doctype = 4;           // set the exact position of Document Type position in SQL
            }

            echo $NamesAuthor;

            //determine the joining factor by using document type if Recipient exist
            if ( $NamesRecipient) {
                if ( ($myrow[$doctype] == "0") or ($myrow[$doctype] == "1") or ($myrow[$doctype] == "2") or ($myrow[$doctype] == "3") or ($myrow[$doctype] == "4") or ($myrow[$doctype] == "33") or ($myrow[$doctype] == "46") or ($myrow[$doctype] == "53") or ($myrow[$doctype] == "57") or ($myrow[$doctype] == "58") or ($myrow[$doctype] == "59") or ($myrow[$doctype] == "61") or ($myrow[$doctype] == "63") or ($myrow[$doctype] == "65") or ($myrow[$doctype] == "66") or ($myrow[$doctype] == "78") or ($myrow[$doctype] == "81") )
                    {
                    echo " to ";
                    }
                else if ($myrow[$doctype] == "48")
                    {
                    echo " v. ";
                    }
                else if ($myrow[$doctype] == "67")
                    {
                    echo " with ";
                    }
                else if ($myrow[$doctype] == "83")
                    {
                    echo " about ";
                    }
                else
                    {
                    echo " and ";                //default
                    }
                echo $NamesRecipient;
            }
            echo "</DIV>";

        }
    }

?>
