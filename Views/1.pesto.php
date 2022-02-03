#Extends = [2]
#Block(content1)
<div>A</div>
<?php
$news = "thisisnews";
$image = new Image("Image Name","Image URL");
?>
<p>{{ $news }}</p>
<p>{{ $image->name }}</p>
#Endblock
