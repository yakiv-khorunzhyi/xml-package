<?php

namespace Y\Xml;

/**
 * Class Writer
 * @package Y\Xml
 */
class Writer
{
    /** @var XMLWriter */
    private $writer;

    /** @var string */
    private $root;

    /** @var string */
    private $node;

    /** @var string */
    private $version;

    /** @var string */
    private $encoding;

    /** @var string $filePath */
    private $filePath;

    /** @var array $tags */
    private $tags;

    /** XMLW constructor. */
    public function __construct()
    {
        $this->writer = new \XMLWriter();
    }

    /**
     * Set options for xml file
     *
     * @param array $params
     *
     * @return $this
     * @example ['root' => 'data','node' => 'node','version' => '1.0','encoding' => 'UTF-8']
     */
    public function setParameters($params)
    {
        foreach ($params as $property => &$param) {
            $this->$property = $param;
        }

        return $this;
    }

    /**
     * Sets the default value provided that they are not set
     * @return void
     */
    private function setDefaultParameters()
    {
        $this->version  = $this->version ?: '1.0';
        $this->encoding = $this->encoding ?: 'UTF-8';
        $this->root     = $this->root ?: 'data';
        $this->node     = $this->node ?: 'node';
    }

    /**
     * The path to the file
     *
     * @param string $filePath
     *
     * @return $this
     */
    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;

        return $this;
    }

    /**
     * Write the beginning of the document to a file
     * @return $this
     */
    public function start()
    {
        $this->setDefaultParameters();
        $startText = "<?xml version=\"{$this->version}\" encoding=\"{$this->encoding}\"?><{$this->root}>";

        file_put_contents(
            $this->filePath,
            $startText,
            LOCK_EX
        );

        return $this;
    }

    /**
     * Add base tags after root node xml
     * @return void
     */
    protected function addTagsAfter($tag, $attrs)
    {
        $str = "<{$tag}";

        foreach ($attrs as $attrKey => &$attrValue) {
            $str .= " {$attrKey}=\"{$attrValue}\"";
        }

        $str .= '>';

        file_put_contents(
            $this->filePath,
            $str,
            LOCK_EX | FILE_APPEND
        );
    }

    /**
     * Add base tags before end root node
     * @return void
     */
    protected function addTagsBefore()
    {
        $wrapTagsWithAttr = &$this->tags;

        if (empty($wrapTagsWithAttr)) {
            return;
        }

        $tags = array_reverse(array_column($wrapTagsWithAttr, 'tag'));
        $str  = '</' . implode('></', $tags) . '>';

        file_put_contents(
            $this->filePath,
            $str,
            LOCK_EX | FILE_APPEND
        );
    }

    /**
     * Write end of document to file
     * @return $this
     */
    public function end()
    {
        $this->addTagsBefore();

        file_put_contents(
            $this->filePath,
            "</{$this->root}>",
            LOCK_EX | FILE_APPEND
        );

        return $this;
    }

    /**
     * Base tags
     *
     * @param       $tag
     * @param array $attrs
     *
     * @return $this
     */
    public function wrap($tag, $attrs = [])
    {
        $this->tags[] = [
            'tag'   => $tag,
            'attrs' => $attrs,
        ];

        $this->addTagsAfter($tag, $attrs);

        return $this;
    }

    /**
     * Converts and writes an array to a file
     *
     * @param array $data
     *
     * @return $this
     */
    public function write($data)
    {
        file_put_contents(
            $this->filePath,
            $this->toXml($data),
            LOCK_EX | FILE_APPEND
        );

        return $this;
    }

    /**
     * Converts an array to xml
     *
     * @param array $data
     *
     * @return string
     */
    public function toXml($data)
    {
        $writer = $this->writer;
        $writer->openMemory();

        $this->convert($writer, $data);

        $xml = $writer->outputMemory(true);
        $writer->flush();

        return $xml;
    }

    /**
     * The function of recursively converting an array to a xml
     *
     * @param array       $data
     * @param string|null $parrent
     */
    protected function convert(&$writer, &$data, $parrent = null)
    {
        foreach ($data as $key => &$val) {
            if ($key[0] === '@') {
                $writer->writeAttribute(strtr($key, ['@' => '']), $val);
                $writer->endAttribute();
                continue;
            }

            if ($key === '=') {
                $writer->text($val);
                continue;
            }

            if (is_numeric($key) && is_array($val)) {
                $writer->startElement($parrent);
                $this->convert($writer, $val, $parrent);
                $writer->endElement();
                continue;
            }

            if (is_array($val)) {
                foreach ($val as $firstKey => &$unused) {
                    break;
                }

                if (is_numeric($firstKey)) {
                    $this->convert($writer, $val, $key);
                } else {
                    $writer->startElement($key);
                    $this->convert($writer, $val, $key);
                    $writer->endElement();
                }
                continue;
            }

            if (is_numeric($key)) {
                $writer->writeElement($parrent ?: $this->node, $val);
                continue;
            }

            $writer->writeElement($key, $val);
        }
    }
}