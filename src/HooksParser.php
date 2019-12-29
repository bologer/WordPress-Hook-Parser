<?php

namespace Bologer;

use Exception;
use Bologer\Dto\HookDto;
use Bologer\Dto\HookTagDto;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Bologer\Dto\HookDocBlockDto;

/**
 * Class HooksParser helps to parse WordPress hooks in provided directory.
 *
 * Usage example:
 *
 * ```php
 * $parser = new HooksParser([
 *      'scanDirectory'     => 'path/to//wp-content/plugins/your-plugin',
 *      'ignoreDirectories' => [
 *          'vendor',
 *          'scripts',
 *          'languages',
 *          'assets'
 *      ],
 *      'scanExtensions'    => [ 'php' ],
 * ]);
 * $parsedItems = $parser->parse(); // Do something with found items from all files in directory
 * ```
 *
 * @package Bologer
 * @author Alexander Teshabaev <sasha.tesh@gmail.com>
 */
class HooksParser {
	/**
	 * @var string Directory to scan.
	 */
	public $scanDirectory;

	/**
	 * @var array List of extension to scan.
	 */
	public $scanExtensions = [ 'php' => 'php' ];

	/**
	 * @var array List of directories to ignore.
	 */
	public $ignoreDirectories = [];


	/**
	 * @var HookDto[] List of parsed hooks. Empty array when nothing was parsed.
	 */
	private $_foundHooks = [];

	/**
	 * HooksParser constructor.
	 *
	 * @param array $options List of configuration options.
	 *
	 * @throws Exception
	 */
	public function __construct( $options = [] ) {
		$this->normalizeAndPrepareOptions( $options );
	}

	/**
	 * Executes parsing.
	 *
	 * @return HookDto[]
	 */
	public function parse() {

		$filePaths = $this->getDirectoryFiles();

		$parsedFiles = \WP_Parser\parse_files( $filePaths, $this->scanDirectory );

		foreach ( $parsedFiles as $parsedFile ) {

			if ( isset( $parsedFile['hooks'] ) ) {

				foreach ( $parsedFile['hooks'] as $hook ) {
					$hookDto = new HookDto();

					$hookDto->name      = $hook['name'];
					$hookDto->path      = $parsedFile['path'];
					$hookDto->line      = $hook['line'];
					$hookDto->endLine   = $hook['end_line'];
					$hookDto->type      = $hook['type'];
					$hookDto->arguments = $hook['arguments'] ?? [];

					$hookDocBlock                  = new HookDocBlockDto();
					$hookDocBlock->description     = $hook['doc']['description'] ?? null;
					$hookDocBlock->longDescription = $hook['doc']['long_description'] ?? null;


					$tags = $hook['doc']['tags'] ?? [];

					foreach ( $tags as $tag ) {
						$tagDto               = new HookTagDto();
						$tagDto->name         = $tag['name'];
						$tagDto->content      = $tag['content'];
						$tagDto->types        = $tag['types'] ?? [];
						$tagDto->variable     = $tag['variable'] ?? null;
						$hookDocBlock->tags[] = $tagDto;
					}

					$hookDto->docBlock = $hookDocBlock;

					$this->_foundHooks[] = $hookDto;
				}
			}
		}

		return $this->_foundHooks;
	}

	/**
	 * Normalizes options.
	 *
	 * @param array $options
	 *
	 * @throws Exception
	 */
	protected function normalizeAndPrepareOptions( array $options ) {
		foreach ( $options as $optionName => $optionValue ) {
			switch ( $optionName ) {
				case 'scanExtensions':
					$this->setScanExtensions( $optionValue );
					break;
				case 'scanDirectory':
					if ( ! is_dir( $optionValue ) ) {
						throw new \Exception( sprintf( 'Directory %s does not exist', $optionValue ) );
					}
					$this->{$optionName} = $optionValue;
					break;
				default:
					$this->{$optionName} = $optionValue;
			}
		}
	}

	/**
	 * Normalises list of extensions.
	 *
	 * @param array $extensions List of extensions.
	 *
	 * @return array
	 */
	protected function setScanExtensions( $extensions ) {
		$scanExtension = [];
		foreach ( $extensions as $extension ) {
			$scanExtension[ $extension ] = trim( preg_replace( '/\W/m', '', $extension ) );
		}

		return $this->scanExtensions = $scanExtension;
	}

	/**
	 * Returns list of files in provided directory.
	 *
	 * @return array
	 */
	protected function getDirectoryFiles() {
		$recursiveIteratorObject = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $this->scanDirectory ) );

		$ignoreDirectories = $this->ignoreDirectories;


		$files = [];
		/**
		 * @var $fileOrFolder RecursiveDirectoryIterator
		 */
		foreach ( $recursiveIteratorObject as $fileOrFolder ) {

			if ( ! empty( $ignoreDirectories ) ) {

				$absolutePath = $fileOrFolder->getPathname();

				foreach ( $ignoreDirectories as $ignoreDirectoryName ) {

					if ( strpos( $absolutePath, $ignoreDirectoryName ) !== false ) {
						continue 2;
					}
				}

				if ( ! $fileOrFolder->isDir() && isset( $this->scanExtensions[ $fileOrFolder->getExtension() ] ) ) {


					$files[] = $fileOrFolder->getPathname();
				}
			}
		}

		return $files;
	}
}
