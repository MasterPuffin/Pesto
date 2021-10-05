<?php

namespace Views;

use Pesto\RenderObject;

class Site {
    public static array $components = ["Alert"];
    public static array $extends = ["Page"];

    public static function title(): RenderObject {
        return new RenderObject(self::class, self::$components, self::$extends, "This Site Title");
    }

    public static function content(): RenderObject {
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
        return new RenderObject(self::class, self::$components, self::$extends, $content);
    }
}