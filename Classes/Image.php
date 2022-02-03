<?php

class Image {
	public string $name;
	public string $url;

	/**
	 * @param string $name
	 * @param string $url
	 */
	public function __construct(string $name, string $url) {
		$this->name = $name;
		$this->url = $url;
	}

}