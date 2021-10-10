<?php

namespace Pesto;

class Pesto2 {
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

		return $parsedContent;
	}

	public static function render(RenderObject $ro) {
		echo self::parse($ro);
	}


}