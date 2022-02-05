# Folder Structure
- `Cache` *must be writable by www-data*
- `Components`
- `Views`

# Commads
`#Components = [<ComponentName>,...]`  
Defines the components that are available in the current template

`#Extends = [<TemplateName>]`  
Defines another template which the templates expands into. Only one entry is possible

`#Block(<blockname>) <content> #Endblock`  
Defines in which parent block the content inside this block should be rendered

`#Block(<blockname>)#Endblock`  
Defines where the content from the child block should go

PHP variables declared inside a block can only be accesses inside this block. PHP variables declared outside a block can be accessed everywhere. 

`{{ <PHPVariable> }}`  
Echoes a PHP variable. Objects and arrays are supported. Variables are autoescaped.

# Examples
## Example for rendering a template
```php
$p = new Pesto(__DIR__ . "/");
$p->enableCaching = false; #True or false

$r = $p->render('Page'); #Templatename without ending
echo $r;
```

## Example for a template
`Base.pesto.php`
````html
#Components = [Alert]
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>#Block(title)#Endblock</title>
</head>
<body>
<Alert @title="HWinPesto" @attribute="Hello World in Page"></Alert>
#Block(content)#Endblock
</body>
</html>
````

## Example for an expending template
`Page.pesto.php`
````html
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
````

## Example for a component
`Alert.pesto.php`
```html
<div class="alert">
    <h1>{{ title }}</h1>
    <p>{{ attribute }}</p>
    <p>{{ content }}</p>
<div>

```