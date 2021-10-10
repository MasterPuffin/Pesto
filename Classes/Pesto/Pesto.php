<?php

namespace Pesto;

class Pesto {
	public static function parse(RenderObject $ro) {
		$parsedContent = $ro->rawContent;

		//Parse the components
		foreach ($ro->components as $component) {
			//Find the component occurrences
			preg_match_all('/<' . $component . '.*>(.*)<\/' . $component . '>/m', $parsedContent, $componentOccurrences);

			foreach ($componentOccurrences[0] as $co) {
				//Get the code for the component
				$componentContent = ('\Components\\' . $component)::component();

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
				$parsedContent = preg_replace('/<' . $component . '.*>(.*)<\/' . $component . '>/', $parsedComponent, $parsedContent);
			}
		}


		//Render extensions
		foreach ($ro->extends as $extend) {
			//Get the extension
			$extendRo = ('\Views\\' . $extend)::{$ro->function}();
			$parsedExtendRo = self::parse($extendRo);

			preg_match_all('/{{\s*(.*)\s*}}/mU', $parsedExtendRo, $matches, 1);

			//Always render content first
			if (in_array("content", $matches[1])) {
				$parsedContent = preg_replace('/{{\s*content\s*}}/mU', $parsedContent, $parsedExtendRo, 1);
			}

			foreach ($matches[1] as $match) {
				//Content has already been rendered
				if ($match != "content") {
					$matchedElement = ('\\' . $ro->class)::{trim($match)}();
					$parsedMatchedElement = self::parse($matchedElement);

					$parsedContent = preg_replace('/{{\s*' . $match . '\s*}}/mU', $parsedMatchedElement, $parsedContent, 1);
				}
			}
		}

		return $parsedContent;
	}

	public static function render(RenderObject $ro) {
		echo self::parse($ro);
	}
}