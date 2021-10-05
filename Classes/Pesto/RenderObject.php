<?php

namespace Pesto;

class RenderObject {
	public string $class;
    public string $function;
	public array $components;
	public array $extends;
	public string $rawContent;

	public function __construct($class, $function, $components, $extends, $rawContent) {
		$this->class = $class;
		$this->function = $function;
		$this->components = $components;
		$this->extends = $extends;
		$this->rawContent = $rawContent;
	}

}