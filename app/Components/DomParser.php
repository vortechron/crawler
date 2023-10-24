<?php

namespace App\Components;

use DOMXPath;
use DOMDocument;

// just simple wrapper for now
class DomParser
{
    protected DOMDocument $dom;

    public static function load(string $url): static
    {
        $html = file_get_contents($url);
        $dom = new DOMDocument;

        libxml_use_internal_errors(true);

        $parser = new static;
        $parser->dom = $dom->loadHTML($html);

        libxml_use_internal_errors(false);

        return $parser;
    }

    public function dom(): DOMDocument
    {
        if (!$this->dom) {
            throw new \Exception('DOMDocument not loaded');
        }

        return $this->dom;
    }

    public function query(string $path): mixed
    {
        $xpath = new DOMXPath($this->dom());

        return $xpath->query($path);
    }
}
