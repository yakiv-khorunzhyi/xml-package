<?php

namespace Y\Xml;

/**
 * Class Reader
 * @package Y\Xml
 */
class Reader
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
     * @param string[] $elements
     *
     * @return $this
     * @example ['name' => '/name', 'id' => '/name/@id']
     */
    public function setSchema($elements)
    {
        $this->elements = $elements;

        return $this;
    }

    /**
     * Sets the base depth of elements
     *
     * @param string $depth
     *
     * @return $this
     * @example '/data/items/item'
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
     * Get items of the xml document
     * @return \Generator
     */
    public function get()
    {
        $reader  = new \XMLReader();
        $xmlItem = new Item();
        $data    = [];

        $elements    = &$this->elements;
        $elementPath = &$this->elementPath;
        $depth       = &$this->depth;

        foreach ($elements as $key => &$value) {
            $value = "{$depth}{$value}";
        }

        $reader->open($this->filePath);

        while ($reader->read()) {
            // add element to path, 1 - XMLReader::ELEMENT
            if ($reader->nodeType == 1) {
                $elementPath .= "/{$reader->name}";
            }

            // handle value, 2 - XMLReader::ATTRIBUTE, 3 - XMLReader::TEXT, 4 - XMLReader::CDATA
            if ($reader->nodeType == 2 || $reader->nodeType == 3 || $reader->nodeType == 4) {
                if ($elementName = array_search($elementPath, $elements)) {
                    $data[$elementName][] = $reader->value;
                }
            }

            // handle attributes
            if ($reader->nodeType == 1 && $reader->hasAttributes) {
                while ($reader->moveToNextAttribute()) {
                    $elementPath .= "/@{$reader->name}";

                    // handle value, 2 - XMLReader::ATTRIBUTE, 3 - XMLReader::TEXT, 4 - XMLReader::CDATA
                    if ($reader->nodeType == 2 || $reader->nodeType == 3 || $reader->nodeType == 4) {
                        if ($elementName = array_search($elementPath, $elements)) {
                            $data[$elementName][] = $reader->value;
                        }
                    }

                    $elementPath = substr($elementPath, 0, strrpos($elementPath, '/'));
                }

                $reader->moveToElement();
            }

            // remove element, 15 - XMLReader::END_ELEMENT
            if ($reader->nodeType == 15 || $reader->isEmptyElement) {
                $elementPath = substr($elementPath, 0, strrpos($elementPath, '/'));
            }

            // get xml item
            if ("{$elementPath}/{$reader->name}" == $depth) {
                $xmlItem->setData($data);
                $data = [];

                yield $xmlItem;
            }
        }

        $reader->close();
    }

    /**
     * Return all xml items as array
     * @return array
     */
    public function getAll()
    {
        $reader = $this;
        $items  = [];

        /** @var \Y\Xml\Item $item */
        foreach ($reader->get() as $item) {
            $items[] = $item->getAll();
        }

        return $items;
    }
}