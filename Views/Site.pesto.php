<?php
?>
#Components = [Alert]
#Extends = [Page]

#Block(blocknametitle)
This is a title
#Endblock

#Block(blocknamecontent)
<div>
	<p>Dies ist ein Text</p>
	<div>
		<Alert @title="Beispiel" @attribute="Hello World">Moin</Alert>
		<Alert @title="Beispiel2" @attribute="Hello World2"></Alert>
	</div>
</div>
#Endblock