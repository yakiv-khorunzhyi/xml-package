<?php

namespace Xml;

/**
 * Class XMLR (XML Reader)
 * @package Xml
 */
class XMLR
{
    /** @var string[] $elements */
    private $elements;

    /** @var string $elementPath */
    private $elementPath;

    /** @var string $depth */
    private $depth;

    /** @var string $filePath */
    private $filePath;

    /**
     * Sets the structure of an xml document
     *
     * @param string[] $elements , example: ['name' => '/name', 'id' => '/name/@id']
     *
     * @return $this
     */
    public function setSchema($elements)
    {
        $this->elements = $elements;

        return $this;
    }

    /**
     * Sets the base depth of elements
     *
     * @param string $depth , example: '/data/items/item'
     *
     * @return $this
     */
    public function setDepth($depth)
    {
        $this->depth = $depth;

        return $this;
    }

    /**
     * The path to the file
     *
     * @param string $filePath
     *
     * @return $this;
     */
    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;

        return $this;
    }

    /**
     * Get part of the xml document
     * @return \Generator
     */
    public function getPart()
    {
        $reader = new \XMLReader();
        $xmlPart = new XmlPart();
        $part = [];

        $elements = &$this->elements;
        $elementPath = &$this->elementPath;
        $depth = &$this->depth;

        foreach ($elements as $key => &$value) {
            $value = "{$depth}{$value}";
        }

        $reader->open($this->filePath);

        while ($reader->read()) {
            // 1 add element to path, 1 - XMLReader::ELEMENT
            if ($reader->nodeType == 1) {
                $elementPath .= "/{$reader->name}";
            }

            // 2 handle value, 2 - XMLReader::ATTRIBUTE, 3 - XMLReader::TEXT, 4 - XMLReader::CDATA
            if ($reader->nodeType == 2 || $reader->nodeType == 3 || $reader->nodeType == 4) {
                if ($elementName = array_search($elementPath, $elements)) {
                    $part[$elementName][] = $reader->value;
                }
            }
            // end handle value

            // 3 handle attributes
            if ($reader->nodeType == 1 && $reader->hasAttributes) {

                while ($reader->moveToNextAttribute()) {
                    $elementPath .= "/@{$reader->name}";

                    // 4 handle value, 2 - XMLReader::ATTRIBUTE, 3 - XMLReader::TEXT, 4 - XMLReader::CDATA
                    if ($reader->nodeType == 2 || $reader->nodeType == 3 || $reader->nodeType == 4) {
                        if ($elementName = array_search($elementPath, $elements)) {
                            $part[$elementName][] = $reader->value;
                        }
                    }
                    // end handle value

                    $elementPath = substr($elementPath, 0, strrpos($elementPath, '/'));
                }

                $reader->moveToElement();
            }
            // end handle attributes

            // 5 remove element, 15 - XMLReader::END_ELEMENT
            if ($reader->nodeType == 15 || $reader->isEmptyElement) {
                $elementPath = substr($elementPath, 0, strrpos($elementPath, '/'));
            }
            // end remove element

            // 6 get part
            if ("{$elementPath}/{$reader->name}" == $depth) {
                $xmlPart->setPart($part);
                $part = [];

                yield $xmlPart;
            }
            // end get part
        }

        $reader->close();
    }
}