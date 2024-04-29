<?php
    // Display Gloc information with Gloc and Gloc Filename in hiddens and Gloc location order in Hidden
    // if ShowHdn flag is set to true
    function GlocInfoScreen($glocNum, $showHdn) {

        include("testheader.php");
        //retrieve gloc information
        $SqlStatement = "SELECT gloc, group_name, item_name, target, loc_order, credit_line from locations where gloc = '$glocNum' ";
        $result = mysqli_query($db,$SqlStatement);
        //Display message if no row returned
        if ( mysqli_num_rows($result) == 0) {
            printf(" no File Series ");

        }
        else
        {
            // Display Gloc information
            $myrow = mysqli_fetch_array($result);
            if ( $showHdn) {

                // get the Series file name
                $fileName = GetSeriesFileName($myrow["loc_order"]);

                // set the Next and Previous Location order
                $NextOrder = $myrow["loc_order"] + 1;
                $PrevOrder = $myrow["loc_order"] - 1;

                // retrieve the previous location order
                $SqlStatement = "SELECT gloc, loc_order from locations where loc_order <= $PrevOrder order by loc_order desc limit 1";
                // echo $SqlStatement;
                $resultGloc = mysqli_query($db,$SqlStatement);

                // position the icons to Top Right
//                echo "<DIV STYLE=\"position:absolute;top:3;left:60%;\">";
                // if a previous location order exist then record gloc as an anchor
                 if ( mysqli_num_rows($resultGloc) == 1) {
                    $myrowGloc = mysqli_fetch_row($resultGloc);
                    echo "<a href=\"glocpage.php?gloc=$myrowGloc[0]&\" ><IMG BORDER=0 SRC=\"/webimages/prevtext.gif\" height=\"47\" width=\"41\" ALT=\"Previous Text\" ></a>&nbsp;&nbsp;";
                 }

                //Display location in series Notes
                echo "<a href=\"../" . $fileName . ".htm#" . $myrow["gloc"] . "\"><IMG BORDER=0 SRC=\"/webimages/whichnote.gif\" height=\"47\" width=\"41\" ALT=\"Where am I?\" ></a>&nbsp;&nbsp;";

                // retrieve the next location order
                $SqlStatement = "SELECT gloc, loc_order from locations where loc_order >= $NextOrder order by loc_order asc limit 1";
                // echo $SqlStatement;
                $resultGloc = mysqli_query($db,$SqlStatement);
                // if a Next location order exist then record gloc as an anchor
                if ( mysqli_num_rows($resultGloc) == 1) {
                    $myrowGloc = mysqli_fetch_row($resultGloc);
                    echo "<a href=\"glocpage.php?gloc=$myrowGloc[0]&\" ><IMG BORDER=0 SRC=\"/webimages/nexttext.gif\" height=\"47\" width=\"41\" ALT=\"Next Text\" ></a>";
                }
//                echo "</DIV><BR><BR>";
                // Gloc target Text info form
                printf("<form name=GlocInfo method=POST action=\"GlocDocuments.php\" >");
                // use hiddens to save gloc
                printf("<input name=glocNum type=\"hidden\" value=\"%s\" >  ", $myrow["gloc"]);
                // use hiddens to save gloc location order
                printf("<input name=glocOrder type=\"hidden\" value=\"%s\" >  ", $myrow["loc_order"]);
                // store series file name  in hidden field
                printf("<input name=GlocFileName type=\"hidden\" value=\"%s\" >  ", $fileName);
                printf("<input name=start_offset type=\"hidden\" value=0 >");
            }
            printf("<B>%s: %s</B><BR>[%s] <BR>", $myrow["group_name"], $myrow["item_name"], $myrow["gloc"]);
            printf("%s <BR><BR>", $myrow["target"]);
            if ( $myrow["credit_line"] ) {
                printf("%s <BR>", $myrow["credit_line"]);
            }
        }
    }
    // Retrieve series file name given location order of the Gloc
    function GetSeriesFileName($locOrder) {
        include("testheader.php");

        // retrieve series file name based on location order range
        $SqlStatement = "SELECT filename from series_notes where first_order <= $locOrder and last_order >= $locOrder ";
        $result = mysqli_query($db,$SqlStatement);
        //  echo $SqlStatement;
        // fetch the series file name
        $myrow = mysqli_fetch_array($result);
        // return series file name
        return $myrow["filename"];
    }
?>
