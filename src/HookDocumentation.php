<?php

namespace Bologer;

use Exception;
use Bologer\Dto\HookDto;
use Bologer\Dto\HookTagDto;

/**
 * Class HookDocumentation helps to generate automatic .md doc about hooks in the plugin.
 *
 * @package Bologer
 * @author Alexander Teshabaev <sasha.tesh@gmail.com>
 */
class HookDocumentation {

	/**
	 * @var HookDto[]
	 */
	private $_hooks;

	/**
	 * @var string Path to the place where to save .md file.
	 */
	private $_saveLocation;

	/**
	 * @var string Generated content of markdown document.
	 */
	private $_markdownContent = '';

	/**
	 * HookDocumentation constructor.
	 *
	 * @param HookDto[] $hooks
	 */
	public function __construct( $hooks ) {
		$this->_hooks = $hooks;
	}

	/**
	 * @param HookDto[] $hooks
	 */
	public function setHooks( $hooks ) {
		$this->_hooks = $hooks;
	}

	/**
	 * @param string $saveLocation
	 */
	public function setSaveLocation( $saveLocation ) {
		$this->_saveLocation = $saveLocation;
	}

	/**
	 * Writes documentation.
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function write() {

		foreach ( $this->_hooks as $item ) {
			$this->generateSingleHook( $item );
		}

		return $this->saveContent();
	}

	/**
	 * Generates hook function.
	 *
	 * @param HookDto $hook
	 *
	 * @return string
	 */
	protected function generateFunction( $hook ) {
		// Function execution
		$function = '';

		switch ( $hook->type ) {
			case 'filter':
				$function = 'apply_filters(';
				break;
			case 'action':
				$function = 'do_action(';
				break;
			default:
		}

		$function .= sprintf( '"%s"', $hook->name );

		if ( empty( $hook->docBlock->tags ) ) {
			$function .= ')';
		} else {
			/**
			 * @var HookTagDto $tag
			 */
			foreach ( $hook->docBlock->tags as $tag ) {
				if(empty($tag->variable)) {
					continue;
				}

				if ( empty( $tag->types ) ) {
					$function .= ', ' . $tag->variable;
				} else {
					$function .= ', ' . implode( '|', $tag->types ) . ' ' . $tag->variable;
				}
			}


			$function .= ')';
		}

		return $function;
	}

	/**
	 * Generates single hook.
	 *
	 * @param HookDto $hook
	 * @return void
	 */
	protected function generateSingleHook( $hook ) {

		$content = $this->_markdownContent;

		// Title & Description
		$title       = $hook->name;
		$description = $hook->docBlock->description;
		$content     .= '### ' . $title . PHP_EOL;
		$content     .= $description . PHP_EOL;

		$function = $this->generateFunction( $hook );
		$location = $hook->path . ':' . $hook->line;

		$content .= <<<EOT
```php
$function
```

Location: $location

EOT;


		if ( ! empty( $hook->docBlock->tags ) ) {
			$content .= '#### Arguments' . PHP_EOL;
			foreach ( $hook->docBlock->tags as $tag ) {
				if ( $tag->name === 'param' ) {
					$content .= sprintf(
						            '* `%s` (%s) %s',
						            $tag->variable,
						            '_' . implode( '_|_', $tag->types ) . '_',
						            $tag->content
					            ) . PHP_EOL;
				}

			}
		}

		$this->_markdownContent = $content;
	}

	/**
	 * Saves markdown content into specified location.
	 *
	 * @return bool
	 * @throws Exception
	 */
	protected function saveContent() {
		$filePath = $this->_saveLocation;

		if ( ! file_exists( $filePath ) ) {
			throw new \Exception( sprintf( 'Location %s does not exist', $this->_saveLocation ) );
		}

		return @file_put_contents( $filePath, $this->_markdownContent ) !== false;
	}
}
