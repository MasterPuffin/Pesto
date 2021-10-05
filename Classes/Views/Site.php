<?php

namespace Views;

use Pesto\RenderObject;

class Site {
    public static function pageTitle(): RenderObject {
        return new RenderObject(self::class, __FUNCTION__, [], [], "This Site Title");
    }

    public static function content(): RenderObject {
        #Components = [Alert]
        #Extends = [Page]
        $content =
            <<<EOL
<div>
	<p>Dies ist ein Text</p>
	<div>
    	<Alert @title="Beispiel" @attribute="Hello World">Moin</Alert>
	    <Alert @title="Beispiel2" @attribute="Hello World2"></Alert>
	</div>
</div>
EOL;
        return new RenderObject(self::class, __FUNCTION__, ["Alert"], ["Page"], $content);
    }
}