It helps to parse WordPress hooks & generate automatic Markdown documentation for them.

### Installation

We are using [phpdoc-parser](https://github.com/WordPress/phpdoc-parser) for parsing code only. 

First thing first - follow installation instructions in their repository. 

About phpdoc-parser: 

> By default phpdoc-parser library parses your code & saves code reference in your database, so it can be served as API.
> This library serves a bit different purpose. It allows you to generate Markdown document with all hooks from the directory you provided. 

**Next:**

You can clone this repository for example in `wp-content/plugins/wordpress-hook-parser`. 

Though it is not a plugin, it is recommended to put it there, because `phpdoc-parser` should be placed in this location. 
This way it would be easier to require dependencies where you need it.

Clone this repository: 
```
git clone https://github.com/bologer/WordPress-Hook-Parser.git wordpress-hook-parser
cd wordpress-hook-parser
```

Prepare composer: 

```
composer dump-autoload
```

### Usage 


```php
<?php

// Include this library
include __DIR__ . '/../../wordpress-hook-parser/vendor/autoload.php';
// Include parser library
include __DIR__ . '/../../phpdoc-parser/vendor/autoload.php';

$hooksParser = new Bologer\HooksParser( [
	'scanDirectory'     => '/absolute/path/to/your/directory',
	'ignoreDirectories' => [
		'vendor',
	]
] );

$parsedItems = $hooksParser->parse();

$hooksDocumentation = new Bologer\HookDocumentation( $parsedItems );
$hooksDocumentation->setSaveLocation( '/absolute/path/where/to/store/hooks.md' );
$hooksDocumentation->write();
```

### Example

See [this](https://github.com/bologer/anycomment.io/blob/0.0.99/docs/hooks.md) file as an example of what is going to be generated.


### Change Markdown Template

You may use `Bologer\HookDocumentation` for this purpose. 

For example: 

```php 
<?php 

class MyHookDocumentation extends Bologer\HookDocumentation {
    
    /**
     * This is method which you can override to change template generation.
     */ 
    protected function generateSingleHook( $hook ) {
        // Generate your own format.
    }
}
```
