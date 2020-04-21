<?php

namespace tests;

use PHPUnit\Framework\TestCase;
use Xml\XMLR;

class TestXMLR extends TestCase
{
    protected static $reader;

    public static function setUpBeforeClass(): void
    {
        self::$reader = (new XMLR())
            ->setFilePath(__DIR__ . '/files/test.xml')
            ->setDepth('/data/items/item')
            ->setSchema([
                'name' => '/name',
                'surname' => '/surname',
                'age' => '/age',
                'images' => '/images/image',
                'imagesId' => '/images/@id',
                'imageId' => '/images/image/@id',
            ]);
    }

    public function testReadXmlFile()
    {
        foreach (self::$reader->getPart() as $item) {
            $this->assertEquals('Yakov', $item->get('name'));
            $this->assertEquals('Khorunzhyi', $item->get('surname'));
            $this->assertEquals(25, $item->get('age'));
            $this->assertEquals('photo', $item->get('imagesId'));

            $this->assertIsArray($item->get('images'));
            $this->assertIsArray($item->get('imageId'));

            $this->assertSame(4, sizeof($item->get('images')));
            $this->assertSame(2, sizeof($item->get('imageId')));

            $all = $item->getAll();

            $this->assertIsArray($all);
            $this->assertNotEmpty($all);
        }
    }

    public static function tearDownAfterClass(): void
    {
        self::$reader = null;
    }
}