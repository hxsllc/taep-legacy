<?php
    // Display a list of locations which match criteria
    function LocationForm($result) {

        echo "<FONT SIZE=-1>";
        // loop through in Location Info
        while ($myrow = mysqli_fetch_row($result))
        {
            echo "<DIV STYLE=\"margin-left:4%;margin-right:5%\">";
            echo "<input type=checkbox name=\"type[]\" value=" . urlencode($myrow[0]) . ">";
            echo "   <a target=_blank href=\"http://edison.rutgers.edu/NamesSearch/glocpage.php?gloc=" . urlencode($myrow[0]) . "&\"><IMG ALIGN=bottom BORDER=0 SRC=\"http://edison.rutgers.edu/NamesSearch/graphics/OpenFolder.gif\" >($myrow[0])&nbsp;" . $myrow[1] . "&nbsp;" . $myrow[2] . "</a> ";
            echo "</DIV>";
        }
        echo "</FONT>";
    }

?>
