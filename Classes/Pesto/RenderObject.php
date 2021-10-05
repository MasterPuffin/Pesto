<?php

namespace Pesto;

class RenderObject {
	public string $class;
	public array $components;
	public array $extends;
	public string $rawContent;

	public function __construct($class, $components, $extends, $rawContent) {
		$this->class = $class;
		$this->components = $components;
		$this->extends = $extends;
		$this->rawContent = $rawContent;
	}

}