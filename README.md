### Installation

#### Dependencies 
We are using [PhpDoc Parser](https://github.com/WordPress/phpdoc-parser) for parsing hooks. 

First thing first - follow installation instructions in their repository. 

#### This Library

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

use Bologer\HooksParser;
use Bologer\HookDocumentation;

// Include this library
include __DIR__ . '/../../wordpress-hook-parser/vendor/autoload.php';
// Include parser library
include __DIR__ . '/../../phpdoc-parser/vendor/autoload.php';


$hooksParser = new HooksParser( [
	'scanDirectory'     => '/absolute/path/to/your/directory',
	'ignoreDirectories' => [
		'vendor',
	]
] );

$parsedItems = $hooksParser->parse();

$hooksDocumentation = new HookDocumentation( $parsedItems );
$hooksDocumentation->setSaveLocation( '/absolute/path/where/to/store/hooks.md' );
$hooksDocumentation->write();
```