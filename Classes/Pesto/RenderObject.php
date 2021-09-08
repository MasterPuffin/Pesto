<?php

namespace Pesto;

class RenderObject {
	public string $class;
	public array $components;
	public string $rawContent;

	public function __construct($class, $components, $rawContent) {
		$this->class = $class;
		$this->components = $components;
		$this->rawContent = $rawContent;
	}

}