<?php

namespace MasterPuffin\Pesto;

class Pesto {
	private string $viewsDir;
	private string $componentsDir;

	public function __construct(string $classRoot, string $viewsDir = "Views", $componentsDir = "Components") {
		$this->viewsDir = $classRoot . $viewsDir;
		$this->componentsDir = $classRoot . $componentsDir;
	}

	public function render(string $templateName): string {
		$templateCode = file_get_contents($this->viewsDir . "/" . $templateName . '.pesto.php');
		$parsedTemplate = self::parse($templateCode);
		$parsedTemplate = trim($parsedTemplate);

		//Render and escape variables
		$parsedTemplate = preg_replace('/{{\s*(\$[a-zA-Z0-9-_]*)\s*}}/m', '<?php echo htmlspecialchars($1) ?>', $parsedTemplate);

		//Add php codes so that the template can get processed
		$parsedTemplate = "?>" . $parsedTemplate . '<?php';

		//Eval the code to the output buffer
		ob_start();
		eval($parsedTemplate);
		$renderedTemplate = ob_get_contents();
		ob_end_clean();

		//Return the output buffer
		return $renderedTemplate;
	}

	private function parse(string $templateCode): string {
		$components = self::findPestoFeature('Components', $templateCode);
		$templateCode = self::parseComponents($templateCode, $components);

		return self::parseExtends($templateCode);
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

				//Replace component in original RenderObject
				// [^\S\r\n]* at the start removes the indentation
				$template = preg_replace('/[^\S\r\n]*<' . $component . '.*>(.*)<\/' . $component . '>/', $parsedComponent, $template);
			}
		}
		return $template;

	}

	private function parseExtends(string $templateCode): string {
		//Find extends
		$extends = self::findPestoFeature('Extends', $templateCode);
		if (!empty($extends)) {
			//Only use the first extend as templates can only extend once
			$extension = $extends[0];

			$extendedTemplateCode = file_get_contents($this->viewsDir . "/" . $extension . '.pesto.php');

			//Find and replace each block
			$blocks = self::findPestoBlocks($templateCode);

			foreach ($blocks as $blockName => $blockContent) {
				$extendedTemplateCode = preg_replace('/#Block\([\s|"]*' . $blockName . '[\s|"]*\).*#Endblock/sU', $blockContent, $extendedTemplateCode);
			}
			//Parse eventual higher extends. If there are no extends the next call will just return the code
			$templateCode = self::parse($extendedTemplateCode);
		}
		return $templateCode;
	}

	//Extracts a pesto feature (extends etc.) from the template string
	private static function findPestoFeature(string $feature, string $template): array {
		preg_match_all('/#' . $feature . '\s?=\s?(\[[a-zA-Z0-9",\s]*\])/', $template, $featureResolved);
		if (empty($featureResolved[0])) {
			return [];
		} else {
			return self::pestoArrToPhpArr($featureResolved[0][0]);
		}
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