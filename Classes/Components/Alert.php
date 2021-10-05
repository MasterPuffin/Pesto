<?php

namespace Components;

class Alert {
    public static function component(): string {
        return <<<EOL
<div class="alert">
    <h1>{{ title }}</h1>
	<p>{{ attribute }}</p>
	<p>{{ content }}</p>
<div>
EOL;
    }
}