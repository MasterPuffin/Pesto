<?php

namespace Views;

use MasterPuffin\Pesto\RenderObject;

class Page {
    public static function content(): RenderObject {
        #Components = [Alert]
        $content =
            <<<EOL
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{title}}</title>
</head>
<body>
<Alert @title="Alert in Page" @attribute="Alert in Page Attribute">This is an Alert inside the extended Page</Alert>
{{content}}
</body>
</html>
EOL;
        return new RenderObject($content, ["Alert"], []);
    }
}