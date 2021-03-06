<?php

class Pesto {
	private string $viewsDir;
	private string $componentsDir;
	private string $cacheDir;
	public bool $enableCaching = true;
	public bool $writeResultToCache = false;

	public function __construct(string $root, string $viewsDir = "Views", $componentsDir = "Components", $cacheDir = "Cache") {
		$this->viewsDir = $root . $viewsDir;
		$this->componentsDir = $root . $componentsDir;
		$this->cacheDir = $root . $cacheDir;
	}

	public function render(string $templateName): string {
		//Check if file is cached
		if ($this->enableCaching && file_exists($this->cacheDir . "/" . $templateName)) {
			//Load file from cache
			$parsedTemplate = file_get_contents($this->cacheDir . "/" . $templateName);
		} else {
			//Parse file
			$templateCode = file_get_contents($this->viewsDir . "/" . $templateName . '.pesto.php');
			$parsedTemplate = self::parse($templateCode);

			//Render and escape variables
			$parsedTemplate = preg_replace('/{{\s*([a-zA-Z0-9-_>\$\[\]"]*)\s*}}/m', '<?php echo htmlspecialchars($1) ?>', $parsedTemplate);

			//Render and escape functions
			$parsedTemplate = preg_replace('/{{\s*([a-zA-Z0-9-_]*)\(\s*(.*)s*\)\s*}}/mU', '<?php echo htmlspecialchars($1($2)) ?>', $parsedTemplate);

			//Remove leftover pesto tags
			$parsedTemplate = preg_replace('/#.*]/m', '', $parsedTemplate);

			$parsedTemplate = trim($parsedTemplate);

			//Add php codes so that the template can get processed
			$parsedTemplate = "?>" . $parsedTemplate . '<?php';

			if ($this->enableCaching || $this->writeResultToCache) {
				//Save parsed cache to file
				file_put_contents($this->cacheDir . "/" . $templateName, $parsedTemplate);
			}
		}

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
			preg_match_all('/<' . $component . '.*>(.*)<\/' . $component . '>/ms', $template, $componentOccurrences);

			//Get the code for the component
			$componentContent = file_get_contents($this->componentsDir . "/" . $component . '.pesto.php');

			foreach ($componentOccurrences[0] as $co) {

				//Find component attributes
				preg_match_all('/@(.*)="(.*)"/mU', $co, $attributes);

				//Find body
				preg_match_all('/<' . $component . '.*>(.*)<\/' . $component . '>/ms', $co, $contents);

				//Push the content to the attributes, so we only have to loop trough one array
				$attributes[1][] = "content";
				$attributes[2][] = $contents[1][0];

				//Make a new array where the attribute names are the keys and the attribute contents are the values
				$sortedAttributes = [];
				for ($i = 0; $i < count($attributes[1]); $i++) {
					$sortedAttributes[$attributes[1][$i]] = $attributes[2][$i];
				}

				$parsedComponent = $componentContent;

				//Find all attributes used in this component
				//The Regex pattern has been provided from Casimir et Hippolyte at Stackoverflow unter CC BY-SA 4.0 (https://stackoverflow.com/a/71038459/4774591)
				preg_match_all('/@[a-zA-Z0-9-_]+ (?= [^{}]* (?: {(?!{) [^{}]* | }(?!}) [^{}]* )* }} )/x', $parsedComponent, $attributOccurrences);
				//Remove double entries in array
				$attributOccurrences = array_unique($attributOccurrences[0]);

				//Remove @ from beginning of value
				$attributOccurrences = array_map(function ($entry) {
					return str_replace('@', '', $entry);
				}, $attributOccurrences);

				foreach ($attributOccurrences as $occurrence) {
					//Replace in {{ }} tags
					$parsedComponent = preg_replace('/{{\s?@' . $occurrence . '\s?}}/mU', $sortedAttributes[$occurrence], $parsedComponent);

					//Replace in functions
					//The function has been provided from Casimir et Hippolyte at Stackoverflow unter CC BY-SA 4.0 (https://stackoverflow.com/a/71038459/4774591)
					$parsedComponent = preg_replace_callback(
						'~{{.*?}}~s',
						fn($m) => str_replace('@' . $occurrence, $sortedAttributes[$occurrence], $m[0]),
						$parsedComponent
					);

					//Remove PHP Tags inside functions
					$parsedComponent = preg_replace_callback(
						'~{{.*?}}~s',
						fn($m) => str_replace(['<?=','<?php','<?','?>'], '', $m[0]),
						$parsedComponent
					);
				}

				//Replace component in original RenderObject
				// [^\S\r\n]* at the start removes the indentation
				$template = preg_replace('/[^\S\r\n]*<' . $component . '.*>(.*)<\/' . $component . '>/ms', $parsedComponent, $template);
			}
		}
		return $template;

	}

	private function parseExtends(string $templateCode): string {
		//Find extends
		$extends = self::findPestoFeature('Extends', $templateCode);
		if (!empty($extends)) {
			$templateGlobalPHP = self::getTemplateGlobalPHP($templateCode);
			//Only use the first extend as templates can only extend once
			$extension = $extends[0];

			$extendedTemplateCode = file_get_contents($this->viewsDir . "/" . $extension . '.pesto.php');

			//Find and replace each block
			$blocks = self::findPestoBlocks($templateCode);

			foreach ($blocks as $blockName => $blockContent) {
				$extendedTemplateCode = preg_replace('/#Block\([\s|"]*' . $blockName . '[\s|"]*\).*#Endblock/sU', $blockContent, $extendedTemplateCode);
			}

			//Add the templates global PHP code back
			$extendedTemplateCode = implode("\n", $templateGlobalPHP) . "\n" . $extendedTemplateCode;

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

	//Finds the code that is not inside a block
	private static function getTemplateGlobalPHP($template): array {
		$rawCode = preg_replace('/#Block\(([a-zA-Z0-9\s,"]+)\)(.+)#Endblock/sU', '', $template);
		preg_match_all('/(<\?php|<\?|<\?=).*\?>/sU', $rawCode, $code);
		return $code[0];
	}

	//Converts a pesto style array "[Alert,Element]" to a php array
	private static function pestoArrToPhpArr(string $elements): array {
		preg_match_all('/(?=[\[|,]([a-zA-Z0-9\s,"]+)[\]|,])/U', $elements, $result);
		return $result[1];
	}

}