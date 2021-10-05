<?php

namespace Pesto;

class RenderObject {
	public string $class;
    public string $function;
	public array $components;
	public array $extends;
	public string $rawContent;

	public function __construct(string $rawContent, array $components = [], array $extends = []) {
        $backtrace = debug_backtrace()[1];
		$this->class = $backtrace['class'];
		$this->function = $backtrace['function'];
		$this->components = $components;
		$this->extends = $extends;
		$this->rawContent = $rawContent;
	}
}