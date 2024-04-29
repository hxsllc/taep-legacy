<?php
foreach($_POST as $key=>$value) ${$key}=$value;
foreach($_GET as $key=>$value) ${$key}=$value;
echo '
<HTML>
  <HEAD><TITLE>Searching for Names</TITLE></HEAD>
  <frameset cols="180,*" frameborder="yes" framespacing=1>
    <frame name="NameSearchForm" src="SearchForm.php" marginheight=10 marginwidth=5 scrolling=auto>';
echo '
    <frame name="NameDisplay" src="DocumentCriteria2.php?name='.rawurlencode($name).'&namecode='.rawurlencode($namecode).'" marginheight=20 marginwidth=20 scrolling=auto>
  </frameset>
</HTML>';
?>