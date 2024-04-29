<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>The Thomas A. Edison Papers at Rutgers University</title>
	<link href="https://fonts.googleapis.com/css?family=Asap|Titillium+Web:300,400" rel="stylesheet">
	<link href="https://www.w3schools.com/lib/w3.css" rel="stylesheet">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js">
	</script>
	<link href="http://edison.rutgers.edu/css/style.css" rel="stylesheet" type="text/css">
	<style type="text/css">
		#results {
		    width: 24.21053%;
			float: left;
			margin-right: 1.05263%;
			display: inline;
			padding-left:1em;
		}
		#results ul {
			display:block;
			border: 1px solid #ccc;
			position: relative;
			margin: 0;
		}
		#results ul li {
			list-style-type: none;
		    background-color: #fff;
		    width:100%;
		}
		#results ul li a {
			border-bottom: 1px solid #ccc;
			display: block;
			padding: 0.75em 1em;
			text-align:left !important;
		}
	</style>
</head>
<body onload="document.start.search1.focus();">
	<header>
		<div id="rutgers-bar">
			<a href="http://www.rutgers.edu" id="ru-sas"></a>
			<h1 style="">Thomas A. Edison Papers</h1>
		</div>
	</header>
	<nav>
		<ul>
			<li>
				<a class="active" href="http://edison.rutgers.edu/">Home</a>
			</li>
			<li class="dropdown">
				<a class="dropbtn" href="#">About</a>
				<div class="dropdown-content">
					<a href="http://edison.rutgers.edu/mission.htm">Our Mission</a> <a href="http://edison.rutgers.edu/who.htm">Staff</a> <a href="http://edison.rutgers.edu/help.htm">Donate</a> <a href="http://edison.rutgers.edu/newsletter.htm">News</a> <a href="http://edison.rutgers.edu/sponsors.htm">Sponsors</a> <a href="http://edison.rutgers.edu/contact.htm">Contact Us</a>
				</div>
			</li>
			<li class="dropdown">
				<a class="dropbtn" href="#">Research</a>
				<div class="dropdown-content">
					<a href="http://edison.rutgers.edu/digital.htm">Digital Edition</a> <a href="http://edison.rutgers.edu/microfilm.htm">Microfilm Edition</a> <a href="http://edison.rutgers.edu/book.htm">Book Edition</a> <a href="http://edison.rutgers.edu/mopix/mopix.htm">Motion Picture Catalogs</a> <a href="http://edison.rutgers.edu/docsamp.htm">Document Sampler</a>
				</div>
			</li>
			<li class="dropdown">
				<a class="dropbtn" href="#">Life of Edison</a>
				<div class="dropdown-content">
					<a href="http://edison.rutgers.edu/biogrphy.htm">Biography</a> <a href="http://edison.rutgers.edu/shortbib.htm">Bibliography</a> <a href="http://edison.rutgers.edu/brfchron.htm">Chronology</a> <a href="http://edison.rutgers.edu/company.htm">Companies</a> <a href="http://edison.rutgers.edu/inventions.htm">Inventions</a> <a href="http://edison.rutgers.edu/edisoninno.htm">Innovations</a> <a href="http://edison.rutgers.edu/patents.htm">Patents</a>
				</div>
			</li>
			<li class="dropdown">
				<a class="dropbtn" href="#">Resources</a>
				<div class="dropdown-content">
					<a href="https://www.google.com/maps/d/u/0/viewer?mid=1cYuurm5i0CIwcm1VlyVI0iEy8kI&hl=en_US&ll=40.614811525361155%2C-74.33394099999998&z=9" target="_blank">Map of NJ Sites</a> <a href="http://edison.rutgers.edu/curriculum.htm">Curriculum</a> <a href="http://edison.rutgers.edu/learning-resources.htm">Learning Resources</a> <a href="http://edison.rutgers.edu/links.htm">Links</a> <a href="http://edison.rutgers.edu/latimer/blueprnt.htm">Latimer</a>
				</div>
			</li>
			<li class="dropdown">
				<a class="dropbtn" href="#">Search</a>
				<div class="dropdown-content">
					<a href="http://edison.rutgers.edu/searchsite.htm">Search (Website)</a> <a href="http://edison.rutgers.edu/NamesSearch/NamesSearch.php">Search (Docs)</a> <a href="http://edison.rutgers.edu/srchtext.htm">Folders & Volumes</a> <a href="http://edison.rutgers.edu/singldoc.htm">Single Document</a> <a href="http://edison.rutgers.edu/srchsn.htm">Series Notes</a>
				</div>
			</li>
		</ul>
	</nav>
	<main>
		<header style="text-align:center;width:100%;display:block;">
			<h1 style="font-family:'Titillium Web',sans-serif;font-weight:300;font-size:2.2em;padding-top:10px;color:#c03;">To begin, search our database for names and companies associated with Edison</h1>
			<p style="width:75%;margin:auto;">Our advanced legacy search engine only supports searching by people names and company names. If you would like a Google-style search, please try out our <a href="http://edison.rutgers.edu/digital2">beta simple search platform</a>.</p><br>
			<form name="start" method="POST" action="searchform1.php"><input type="text" name="search1" id="search1" style="width:80%;font-size:1.4em;"/>&nbsp;<br>&nbsp;<input type="submit" value="Search" style="margin-top:10px;font-size:1.4em;background-color:red;"/></form>
		</header>
		
		<section id="results">
			<ul>
			
		<?php
			
		include 'dbcon.php';
		foreach($_POST as $key=>$value) ${$key}=$value;
		foreach($_GET as $key=>$value) ${$key}=$value;
			
		if ($search1)
		{
		    //display all names and accent name(if exist) and document count with a match of the selected name
		    $SqlStatement = "SELECT names.code as code, names.name as name, names_accent.name as name_accent, count(names_in_documents.name_code) as name_count FROM names_in_documents, names LEFT JOIN names_accent on names.code = names_accent.code where names_in_documents.name_code = names.code and names.name like '%". htmlentities(addslashes($search1)) . "%' group by names.name, names.code, name_accent ORDER BY name_count DESC";
		    $result = mysqli_query($db,$SqlStatement);
		    // echo $SqlStatement;
		    //set failed flag if no rows returned
		    if ( mysqli_num_rows($result) == 0) {
		        $failed = true;
		    }
		    else
		    {
				echo "<li><strong>Names Found</strong></li>";
				// names should be wrapped with urlencoding to prevent quote or any other literal from being interpreted
		        while ($myrow = mysqli_fetch_array($result))
		        {
		            // Use the accent name if exist
		            if ( $myrow["name_accent"] ) {
		                $name = $myrow["name_accent"];
		            }
		            else
		            {
		                $name = $myrow["name"];
		            }
		            printf("<li><a href=\"%s\" target=\"NameSearchForm\" onClick=\"top.frames[1].setName('%s', '%s', -1)\"> %s [%s]</a></li>\n", "SearchForm.php", $myrow["code"], rawurlencode($name),$name,$myrow["name_count"]);
		        }
		    }

		    //display cross references with document count with a match of the selected name
		    $SqlStatement = "SELECT names_2.code as code, names_2.name as name, count(names_in_documents.name_code) as name_count FROM names_in_documents, names, cross_references, names as names_2 where names.code like 'Z%' and names.code = cross_references.code and cross_references.xref = names_2.code and names_in_documents.name_code = names_2.code and names.name like '%". htmlentities(addslashes($key1)) . "%' group by names_2.name, names_2.code ";
		    $result = mysqli_query($db,$SqlStatement);
		    //echo $SqlStatement;
		    // if rows found display Cross References
		    if ( mysqli_num_rows($result) != 0)
		    {
		        // if main name search failed display body and reset flag
		        if ($failed) {
		                printf("<body><div class=\"sidesearchcolumn\">Names Found</div>");
		
		                $failed = false;
		        }
		        // names should be wrapped with urlencoding to prevent quote or any other literal from being interpreted
		        while ($myrow = mysqli_fetch_array($result))
		        {
		            // Use the accent name if exist
		            if ( $myrow["name_accent"] ) {
		                $name = $myrow["name_accent"];
		            }
		            else
		            {
		                $name = $myrow["name"];
		            }
		            printf("<p class=\"footnote\">See <a href=\"%s\" target=\"NameSearchForm\" onClick=\"top.frames[1].setName('%s', '%s', -1)\"> %s </a>&nbsp;[%s]</p>\n", "SearchForm.php", $myrow["code"], rawurlencode($name),$name,$myrow["name_count"]);
		        }
			}
		}
		// if failed flag was set or starting the name search page
		if (!($key1) | $failed) {
			//
			//<script type="text/javascript">
			//<!--
			// check if maximum number of names reached
			//function chkMaximum()
			//{
			//            if ( top.frames[1].nextPosition() == -1)
			//            {
			//                alert(" a maximum of four names ");
			//                return false;
			//            }
			//            return true;
			//       }
			//--> 
			//</script>

		    //display name search page
		    printf("<body><div class=\"sidesearchcolumn\">Name Search</div>");
		
		    printf("<form METHOD=POST onSubmit=\"return chkMaximum()\" ACTION=\"$PHP_SELF\" name=theform >");
		    printf("<center><input type=text name=key1 value=\"\" size=18><br>");
		    printf("<input type=submit value=\"Find Name\" ></center></form>");
			printf("<div class=\"footnote\"><p>Search names with &amp; as \"Gold\" or \"Stock\" not \"Gold &amp; Stock\".</p><p>To find an individual, enter the last name but not the first name (i.e.,  \"Anthony\" will retrieve all the names containing \"Anthony\" but \"Anthony, Frank\" will not retrieve \"Anthony, F A\").</p><p>To find a company enter principal word in the name. (i.e.,  \"mechanic\" will find \"American Institute Mechanical Engineers,\" \"American Society of Mechanical Engineers,\" and \"Mechanical News\").</p></div>");	
		}			
						
		?>
			
			</ul>
			
			
		</section>
	</main>
</body>
</html>