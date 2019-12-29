<?php


namespace Bologer\Dto;

/**
 * Class HookDocBlockDto holds dockblock information.
 *
 * @package Bologer\Dto
 * @author Alexander Teshabaev <sasha.tesh@gmail.com>
 */
class HookDocBlockDto {
	/**
	 * @var string
	 */
	public $description;

	/**
	 * @var string
	 */
	public $longDescription;

	/**
	 * @var HookTagDto
	 */
	public $tags = [];
}
