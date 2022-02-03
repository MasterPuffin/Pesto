<?php

namespace MasterPuffin\Pesto;

class Pesto2 {
	private string $viewsDir;
	private string $componentsDir;
	private string $classRoot;

	public function __construct(string $classRoot, string $viewsDir = "Views", $componentsDir = "Components") {
		$this->classRoot = $classRoot;
		$this->viewsDir = $classRoot . $viewsDir;
		$this->componentsDir = $classRoot . $componentsDir;
	}

	public function render(string $template) {
		$templateCode = file_get_contents($this->viewsDir . "/" . $template . '.pesto.php');
		//print_r($templateCode);
		echo "\n";

		$components = self::findPestoFeature('Components', $templateCode);
		$extends = self::findPestoFeature('Extends', $templateCode);

		//print_r($components);
		//print_r($extends);
		$blocks = self::findPestoBlocks($templateCode);
		$parsedBlocks = [];
		//print_r($blocks);
		foreach ($blocks as $blockName => $block) {
			$parsedBlocks[$blockName] = self::parseComponents($block, $components);
		}

		print_r($parsedBlocks);

	}


	private function parseComponents(string $template, array $components): string {
		foreach ($components as $component) {

			//Find occurrences of the component
			preg_match_all('/<' . $component . '.*>(.*)<\/' . $component . '>/m', $template, $componentOccurrences);

			//Get the code for the component
			$componentContent = file_get_contents($this->componentsDir . "/" . $component . '.pesto.php');

			foreach ($componentOccurrences[0] as $co) {

				//Find component attributes
				preg_match_all('/@(.*)="(.*)"/mU', $co, $attributes);

				//Find body
				preg_match_all('/<' . $component . '.*>(.*)<\/' . $component . '>/m', $co, $contents);

				//Push the content to the attributes, so we only have to loop trough one array
				$attributes[1][] = "content";
				$attributes[2][] = $contents[1][0];

				$parsedComponent = $componentContent;

				//Replace each attribute with its content
				for ($i = 0; $i < count($attributes); $i++) {
					$parsedComponent = preg_replace('/{{\s*' . $attributes[1][$i] . '\s*}}/', $attributes[2][$i], $parsedComponent);
				}
				print_r($co);

				//Replace component in original RenderObject
				// [^\S\r\n]* at the start removes the indentation
				$template = preg_replace('/[^\S\r\n]*<' . $component . '.*>(.*)<\/' . $component . '>/', $parsedComponent, $template);
			}
		}
		return $template;

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