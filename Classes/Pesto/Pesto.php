<?php

namespace Pesto;


class Pesto {
    public static function parse(RenderObject $ro) {
//        print_r($ro);
//        die();

        $renderedContent = $ro->rawContent;

        //Render components
        foreach ($ro->components as $component) {
            $componentContent = ('\Components\\' . $component)::component();

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

        foreach ($ro->extends as $extend) {
            //Render extension
            $extendRo = ('\Views\\' . $extend)::content();
            $renderedExtendRo = self::parse($extendRo);
            //Place rendered content in content of rendered extension
            $renderedContent = preg_replace('/{{\s*content\s*}}/mU', $renderedContent, $renderedExtendRo, 1);
        }
        return $renderedContent;
    }

    public static function render(RenderObject $ro) {
        echo self::parse($ro);

    }
}