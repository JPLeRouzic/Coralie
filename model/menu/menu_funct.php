<?php

// Menu
function menu(string $class = null)
{
    $filename = "content/data/menu.json";
    if (file_exists($filename)) {
        $json = json_decode(file_get_contents('content/data/menu.json', true));
        $nodes = json_decode($json);
        if (empty($nodes)) {
            get_menu($class);
        } else {
            $html = parseNodes($nodes, null, $class);
            libxml_use_internal_errors(true);
            $doc = new DOMDocument();
            $doc->loadHTML($html);

            $finder = new DOMXPath($doc);
            $elements = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' dropdown-menu ')]");

            // loop through all <ul> with dropdown-menu class
            foreach ($elements as $element) {
                $nodes = $element->childNodes;
                foreach ($nodes as $node) {
                    $class = $node->getAttribute('class');
                    if (stripos($class, 'active')) {
                        $parentClass = $element->parentNode->getAttribute('class') . ' active';
                        $element->parentNode->setAttribute('class', $parentClass);
                    }
                }
            }

        return preg_replace('~<(?:!DOCTYPE|/?(?:html|head|body))[^>]*>\s*~i', '', $doc->saveHTML($doc->documentElement));

        }
    } else {
        get_menu($class);
    }
}

