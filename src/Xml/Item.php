<?php

/**
 * @author       Yakiv Khorunzhyi
 * @copyright    2020
 * @license      MIT
 */

namespace Y\Xml;

/**
 * Class Item
 * @package Y\Xml
 */
class Item
{
    /** @var string[] $data */
    private $data;

    /**
     * @param string[] $data
     *
     * @return void
     */
    public function setData($data)
    {
        foreach ($data as &$item) {
            if (count($item) === 1) {
                $item = $item[0];
            }
        }
        $this->data = $data;
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
        if (isset($this->data[$key])) {
            return $this->data[$key];
        }

        return null;
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
        if (isset($this->data[$key])) {
            return $this->data[$key];
        }

        return null;
    }

    /**
     * Get the whole array
     * @return array
     */
    public function getAll()
    {
        return $this->data;
    }
}