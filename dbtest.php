<?php
$dbname="taep_taep";

echo "connecting to mysql...<br>";
$handle=mysql_connect();

if (!$handle) {
	echo 'connection failed<br>';
} else {
	echo 'connection handle: ' . $handle . '<br>';
}

echo "selecting database $dbname...<br>";
mysql_select_db($dbname,$handle);

echo "<br>trying some sql commands...<br>";
$sql="SHOW TABLES from $dbname";
echo $sql . '<br>';
$result = mysql_query($sql);
echo 'results count: ' . mysql_num_rows($result) . '<br>';
// while ($row = mysql_fetch_row($result)) {
   // echo "Table: {$row[0]}<br>";
// }
$row=-1;
while ($cols = mysql_fetch_array($result, MYSQL_NUM)) {
	$row++;
	// loop through each field in the record
	for ($i=0; $i<count($cols); $i++){
		echo "row $row column $i=$cols[$i]<br>";
	}
}

$sql="DESCRIBE locations";
echo '<br>' . $sql . '<br>';
$result = mysql_query($sql);
$count=mysql_num_rows($result);
echo 'results count: ' . $count . '<br>';
$row=-1;
while ($cols = mysql_fetch_array($result, MYSQL_NUM)) {
	$row++;
	// loop through each field in the record
	for ($i=0; $i<count($cols); $i++){
		echo "row $row column $i=$cols[$i]<br>";
	}
}

echo "<br>trying to pull out some data...<br>";
$sql="select * from locations";
echo $sql . '<br>';
$result = mysql_query($sql);
$count=mysql_num_rows($result);
echo 'results count: ' . $count . '<br>';
$row=-1;
while ($cols = mysql_fetch_array($result, MYSQL_NUM)) {
	$row++;
	// loop through each field in the record
	for ($i=0; $i<count($cols); $i++){
		echo "row $row column $i=$cols[$i]<br>";
	}
}			

$sql="select * from series_notes";
echo '<br>' . $sql . '<br>';
$result = mysql_query($sql);
$count=mysql_num_rows($result);
echo 'results count: ' . $count . '<br>';
$row=-1;
while ($cols = mysql_fetch_array($result, MYSQL_NUM)) {
	$row++;
	// loop through each field in the record
	for ($i=0; $i<count($cols); $i++){
		echo "row $row column $i=$cols[$i]<br>";
	}
}

$sql="SELECT gloc, group_name, item_name, target, loc_order, credit_line from locations";
echo '<br>' . $sql . '<br>';
$result = mysql_query($sql);
$count=mysql_num_rows($result);
echo 'results count: ' . $count . '<br>';
$row=-1;
while ($cols = mysql_fetch_array($result, MYSQL_NUM)) {
	$row++;
	// loop through each field in the record
	for ($i=0; $i<count($cols); $i++){
		echo "row $row column $i=$cols[$i]<br>";
	}
}

$sql="SELECT * from locations";
echo '<br>' . $sql . '<br>';
$result = mysql_query($sql);
$count=mysql_num_rows($result);
echo 'results count: ' . $count . '<br>';
$row=-1;
while ($cols = mysql_fetch_array($result, MYSQL_NUM)) {
	$row++;
	// loop through each field in the record
	for ($i=0; $i<count($cols); $i++){
		echo "row $row column $i=$cols[$i]<br>";
	}
}


?>
