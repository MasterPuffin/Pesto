<?php

namespace Pesto;


class Pesto {
    public static function parse(RenderObject $ro) {
        $parsedContent = $ro->rawContent;

        //Render components
        foreach ($ro->components as $component) {
            $componentContent = ('\Components\\' . $component)::component();

            //Find body
            preg_match_all('/<' . $component . '.*>(.*)<\/' . $component . '>/m', $parsedContent, $contents);

            //Find component attributes
            preg_match_all('/@(.*)="(.*)"/mU', $parsedContent, $attributes);

            //Replace Tags with content
            $parsedContent = preg_replace('/<' . $component . '(.*)>(.*)<\/' . $component . '>/m', $componentContent, $parsedContent);

            $count = count($attributes[1]);
            for ($i = 0; $i < $count; $i++) {
                $parsedContent = preg_replace('/{{\s*' . $attributes[1][$i] . '\s*}}/mU', $attributes[2][$i], $parsedContent, 1);
                unset($attributes[1][$i]);
                unset($attributes[2][$i]);
            }

            //Now render to content of the component
            foreach ($contents[1] as $content) {
                $parsedContent = preg_replace('/{{\s*content\s*}}/mU', $content, $parsedContent, 1);
            }
        }

        foreach ($ro->extends as $extend) {
            //Render extension
            $extendRo = ('\Views\\' . $extend):: {$ro->function}();
            $parsedExtendRo = self::parse($extendRo);

            preg_match_all('/{{\s*(.*)\s*}}/mU', $parsedExtendRo, $matches, 1);

            //Always render content first
            if (in_array("content",$matches[1])) {
                $parsedContent = preg_replace('/{{\s*content\s*}}/mU', $parsedContent, $parsedExtendRo, 1);
            }

            foreach ($matches[1] as $match) {
                //Content has already been rendered
                if ($match != "content") {
                    $matchedElement = ('\\' . $ro->class)::$match();
                    $parsedMatchedElement = self::parse($matchedElement);

                    $parsedContent = preg_replace('/{{\s*'.$match.'\s*}}/mU', $parsedMatchedElement, $parsedContent, 1);
                }
            }
        }
        return $parsedContent;
    }

    public static function render(RenderObject $ro) {
        echo self::parse($ro);
    }
}