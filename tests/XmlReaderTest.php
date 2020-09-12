<?php

final class XmlReaderTest extends \PHPUnit\Framework\TestCase
{
    /** @var \Y\Xml\Reader */
    protected static $reader;

    public static function setUpBeforeClass()
    {
        self::$reader = (new \Y\Xml\Reader())
            ->setFilePath(__DIR__ . '/../files/test.xml')
            ->setDepth('/data/items/item')
            ->setSchema([
                'name'     => '/name',
                'surname'  => '/surname',
                'age'      => '/age',
                'images'   => '/images/image',
                'imagesId' => '/images/@id',
                'imageId'  => '/images/image/@id',
            ]);
    }

    public function testReadXmlFile()
    {
        foreach (self::$reader->get() as $item) {
            $this->assertEquals('Yakov', $item->get('name'));
            $this->assertEquals('Khorunzhyi', $item->get('surname'));
            $this->assertEquals(25, $item->get('age'));
            $this->assertEquals('photo', $item->get('imagesId'));

            $this->assertEquals(true, is_array($item->get('images')));
            $this->assertEquals(true, is_array($item->get('images')));

            $this->assertSame(4, count($item->get('images')));
            $this->assertSame(2, count($item->get('imageId')));

            $all = $item->getAll();

            $this->assertEquals(true, is_array($all));
            $this->assertNotEmpty($all);
        }
    }

    public static function tearDownAfterClass()
    {
        self::$reader = null;
    }
}