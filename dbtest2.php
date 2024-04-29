<?php
$db = mysql_connect2("localhost2","taep2","1234") or die("Could not connect");
print ("Connected successfully");

mysql_select_db("taep_taep",$db);


    //display all names and accent name(if exist) and document count with a match of the selected name
    $SqlStatement = "SELECT names.code as code, names.name as name, names_accent.name as name_accent, count(names_in_documents.name_code) as name_count FROM names_in_documents, names LEFT JOIN names_accent on names.code = names_accent.code where names_in_documents.name_code = names.code and names.name like '%Latimer%' group by names.name, names.code, name_accent ";
    $result = mysql_query($SqlStatement,$db);
    // echo $SqlStatement;
    //set failed flag if no rows returned
    if ( mysql_num_rows($result) == 0) {
        $failed = true;
    }
    else
    {
        printf("<body><div class=\"sidesearchcolumn\">Names Found</div>");
        // names should be wrapped with urlencoding to prevent quote or any other literal from being interpreted
        while ($myrow = mysql_fetch_array($result))
        {
            // Use the accent name if exist
            if ( $myrow["name_accent"] ) {
                $name = $myrow["name_accent"];
            }
            else
            {
                $name = $myrow["name"];
            }
            printf("<p class=\"footnote\"><a href=\"%s\" target=\"NameSearchForm\" onClick=\"top.frames[1].setName('%s', '%s', -1)\"> %s </a>&nbsp;[%s]</p>\n", "../NamesSearch/SearchForm.php", $myrow["code"], rawurlencode($name),$name,$myrow["name_count"]);
        }
    }
?>