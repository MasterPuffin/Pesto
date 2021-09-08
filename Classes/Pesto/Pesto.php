<?php

namespace Pesto;


class Pesto {
	public static function parse(RenderObject $ro) {
		$renderedContent = $ro->rawContent;

		//Render components
		foreach ($ro->components as $component) {
			$componentContent = ('\Components\\' . $component)::render();

			//Find body
			preg_match_all('/<' . $component . '.*>(.*)<\/' . $component . '>/m', $renderedContent, $contents);

			//Find component attributes
			preg_match_all('/@(.*)="(.*)"/mU', $renderedContent, $attributes);

			//Replace Tags with content
			$renderedContent = preg_replace('/<' . $component . '(.*)>(.*)<\/' . $component . '>/m', $componentContent, $renderedContent);

			$count = count($attributes[1]);
			for ($i = 0; $i < $count; $i++) {
				$renderedContent = preg_replace('/{{\s*' . $attributes[1][$i] . '\s*}}/mU', $attributes[2][$i], $renderedContent, 1);
				unset($attributes[1][$i]);
				unset($attributes[2][$i]);
			}

			//Now render to content of the component
			foreach ($contents[1] as $content) {
				$renderedContent = preg_replace('/{{\s*content\s*}}/mU', $content, $renderedContent, 1);
			}
		}
		echo $renderedContent;
	}

}