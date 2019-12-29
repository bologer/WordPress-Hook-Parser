<?php


namespace Bologer\Dto;

/**
 * Class HookTagDto holds tag information. For example it can be "@" docblock parameters or hooks arguments.
 *
 * @package Bologer\Dto
 */
class HookTagDto {
	/**
	 * @var string
	 */
	public $name;

	/**
	 * @var string
	 */
	public $content;

	/**
	 * @var array
	 */
	public $types = [];

	/**
	 * @var string|null
	 */
	public $variable;
}
