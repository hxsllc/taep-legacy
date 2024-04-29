<?php
    // Display Document info in Condensed form for Correspondence only
    function CondensedCorrForm($resultDocuments, $arrNameCode) {

        $NameCode = "  ";
        $roleType = "";
        // loop through in Document Info resultset gathering Authors and Receipients
        while ($myrow = mysqli_fetch_row($resultDocuments))
        {

            $DocId = $myrow[2];           // set the document id to report on

            // set Document name group header("To or From"), if not already set
            if ( $NameCode !=  $myrow[1] || $roleType != $myrow[6]) {

                    $roleType = $myrow[6];        //  set the role type to report on
                    $NameCode = $myrow[1];        // set the NameCode to report on
                    if ( $roleType == 'R' )
                    {
                        echo "<B> To " . htmlspecialchars($myrow[0]) .  "</B>";
                    }
                    else
                    {
                        echo "<B> From " . htmlspecialchars($myrow[0]) . "</B>";
                    }
            }

            // Now display document date with document id as anchor parameter
            echo "<DIV STYLE=\"margin-left:4%;margin-right:5%\">";
            echo "<a target=\"ImageResult\" href=\"../NamesSearch/DocImage.php?DocId=" . $DocId . "&";
            echo "\">" . $myrow[3] . "</a>";
            echo "</DIV>";
         }
    }

?>
