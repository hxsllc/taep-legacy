<?php
foreach($_POST as $key=>$value) ${$key}=$value;
foreach($_GET as $key=>$value) ${$key}=$value;

include 'DocumentCriteria5.php';
//?name='.rawurlencode($name).'&namecode='.rawurlencode($namecode);
?>