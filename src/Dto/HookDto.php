<?php

namespace Bologer\Dto;

/**
 * Class HookDto holds single hooks instance.
 *
 * @package Bologer\Dto
 * @author Alexander Teshabaev <sasha.tesh@gmail.com>
 */
class HookDto {
	/**
	 * @var string
	 */
	public $name;

	/**
	 * @var string
	 */
	public $path;

	/**
	 * @var integer
	 */
	public $line;
	/**
	 * @var integer
	 */
	public $endLine;
	/**
	 * @var string
	 */
	public $type;

	/**
	 * @var array List of hook arguments.
	 */
	public $arguments = [];

	/**
	 * @var HookDocBlockDto
	 */
	public $docBlock;
}
