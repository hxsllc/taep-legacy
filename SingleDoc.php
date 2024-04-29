<?php foreach($_GET as $key=>$value) ${$key}=$value; ?>
<html>
<head>
    <script LANGUAGE="JavaScript">
    <!--
    // blank out a frame
    function blank()
    {
        return "<HTML></HTML>";
    }
    //-->
    </script>
</head>
<title TITLE="Document Images"></title>
<frameset rows="0,300,*" frameborder="yes" framespacing=1>
<frame name="Blank" src="javascript:parent.blank()" marginheight=0 marginwidth=0 scrolling=auto>
    <frame name="ImageResult" src="DocImage.php?DocId=<?php echo $DocId;?>&noName=NONE&" marginheight=20 marginwidth=20 scrolling=auto>
    <frame name="ImagePicture" src="javascript:parent.blank()" marginheight=20 marginwidth=20 scrolling=auto>
</frameset><noframes></noframes>
</html>