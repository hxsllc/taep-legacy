<?php
foreach($_POST as $key=>$value) ${$key}=$value;
foreach($_GET as $key=>$value) ${$key}=$value;

include 'DocumentCriteria6.php';
//?name='.rawurlencode($name).'&namecode='.rawurlencode($namecode);
?>