<?php

namespace MasterPuffin\Pesto;

class Pesto2 {
	private string $templateDir;

	public function __construct(string $templateDir) {
		$this->templateDir = $templateDir;
	}

	public function render(string $template) {
		echo $template;
		$templateCode = file_get_contents($this->templateDir . "/" . $template . '.pesto.php');
		print_r($templateCode);

		$components = self::findPestoFeature('Components', $templateCode);
		$extends = self::findPestoFeature('Extends', $templateCode);

		print_r($components);
		print_r($extends);
	}


	//Extracts a pesto feature (extends etc.) from the template string
	private static function findPestoFeature(string $feature, string $template) {
		preg_match_all('/#' . $feature . '\s?=\s?(\[[a-zA-Z0-9",\s]*\])/', $template, $featureResolved);
		return self::pestoArrToPhpArr($featureResolved[0][0]);
	}

	//Converts a pesto style array "[Alert,Element]" to a php array
	private static function pestoArrToPhpArr(string $elements): array {
		preg_match_all('/(?=[\[|,]([a-zA-Z0-9\s,"]+)[\]|,])/U', $elements, $result);
		return $result[1];
	}

}