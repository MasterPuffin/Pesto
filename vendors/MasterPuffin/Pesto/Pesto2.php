<?php

namespace MasterPuffin\Pesto;

class Pesto2 {
	private string $templateDir;

	public function __construct(string $templateDir) {
		$this->templateDir = $templateDir;
	}

	public function render(string $template) {
		$templateCode = file_get_contents($this->templateDir . "/" . $template . '.pesto.php');
		print_r($templateCode);

		$components = self::findPestoFeature('Components', $templateCode);
		$extends = self::findPestoFeature('Extends', $templateCode);

		//print_r($components);
		//print_r($extends);
		$blocks = self::findPestoBlocks($templateCode);
		print_r($blocks);
	}


	//Extracts a pesto feature (extends etc.) from the template string
	private static function findPestoFeature(string $feature, string $template): array {
		preg_match_all('/#' . $feature . '\s?=\s?(\[[a-zA-Z0-9",\s]*\])/', $template, $featureResolved);
		return self::pestoArrToPhpArr($featureResolved[0][0]);
	}

	//Finds and returns an array with each pesto block
	private static function findPestoBlocks(string $template): array {
		preg_match_all('/#Block\(([a-zA-Z0-9\s,"]+)\)(.+)#Endblock/sU', $template, $rawBlocks, PREG_SET_ORDER);
		$blocks = [];
		//Map each block content to an array where the array key is the name of the block
		foreach ($rawBlocks as $rawBlock) {
			$blocks[$rawBlock[1]] = trim($rawBlock[2]);
		}
		return $blocks;
	}

	//Converts a pesto style array "[Alert,Element]" to a php array
	private static function pestoArrToPhpArr(string $elements): array {
		preg_match_all('/(?=[\[|,]([a-zA-Z0-9\s,"]+)[\]|,])/U', $elements, $result);
		return $result[1];
	}

}