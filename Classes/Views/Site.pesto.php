<?php
?>
#Components = [Alert,Element,Element2]
#Extends = [Page]

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
</div>
#Endblock
