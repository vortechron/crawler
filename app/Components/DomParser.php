<?php

namespace App\Components;

use DOMXPath;
use DOMDocument;

// just simple wrapper for now
class DomParser
{
    protected mixed $dom;
    public mixed $html;

    public static function load(string $url): static
    {
        $html = file_get_contents($url);
        libxml_use_internal_errors(true);

        $parser = new static;
        $parser->html = $html;
        $dom = new DOMDocument;
        $dom->loadHTML($html);
        $parser->dom = $dom;

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

    public function query(string $path, ...$rest): mixed
    {
        $xpath = new DOMXPath($this->dom());

        return $xpath->query($path, ...$rest);
    }
}
