<?php
foreach($_POST as $key=>$value) ${$key}=$value;
foreach($_GET as $key=>$value) ${$key}=$value;

include 'DocumentCriteria4.php';
//?name='.rawurlencode($name).'&namecode='.rawurlencode($namecode);
?>