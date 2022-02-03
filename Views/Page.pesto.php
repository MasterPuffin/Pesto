<?php
?>
#Components = [Alert]
#Extends = [Base]

#Block(title)
This is a title
#Endblock

#Block(content)
<div>
	<p>Dies ist ein Text</p>
	<div>
		<Alert @title="Beispiel" @attribute="Hello World">Moin</Alert>
		<Alert @title="Beispiel2" @attribute="Hello World2"></Alert>
	</div>
	<?php
	$news = "thisisnews";
	$image = new Image("Image Name","Image URL");
	?>
    <p>{{ $news }}</p>
    <p>{{ $image->name }}</p>
</div>
#Endblock
