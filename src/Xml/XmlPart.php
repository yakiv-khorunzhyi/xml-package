<?php

namespace Xml;

/**
 * Class XmlPart
 * @package Xml
 */
class XmlPart
{
    /** @var string[] $part */
    private $part;

    /**
     * @param string[] $part
     *
     * @return void
     */
    public function setPart($part)
    {
        foreach ($part as &$item) {
            if (sizeof($item) == 1) {
                $item = $item[0];
            }
        }
        $this->part = $part;
    }

    /**
     * To access through the arrow
     *
     * @param string $key
     *
     * @return string|string[]|null
     */
    public function __get($key)
    {
        return $this->part[$key] ?? null;
    }

    /**
     * Access by array key
     *
     * @param string $key
     *
     * @return string|string[]|null
     */
    public function get($key)
    {
        return $this->part[$key] ?? null;
    }

    /**
     * Get the whole array
     * @return array
     */
    public function getAll()
    {
        return $this->part;
    }
}