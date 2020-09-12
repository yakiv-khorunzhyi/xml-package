<?php

final class XmlWriterTest extends \PHPUnit\Framework\TestCase
{
    /** @var \Y\Xml\Writer */
    protected static $writer;

    protected static $resultStr = '<?xml version="1.0" encoding="UTF-8"?><data><baseWrap><items id="someId" name="someName"><item attr="itemAttr"><name id="someId" attr="someAttr">Yakiv</name><images><image>https://www.google.com</image><image>https://yandex.ru</image></images></item></items></baseWrap></data>';

    protected static $filePath = __DIR__ . '/../files/test2.xml';

    public static function setUpBeforeClass()
    {
        self::$writer = new \Y\Xml\Writer();

        self::$writer
            ->setFilePath(self::$filePath)
            ->setParameters([
                'root'     => 'data',
                'node'     => 'node',
                'version'  => '1.0',
                'encoding' => 'UTF-8',
            ]);
    }

    public function testWriteXmlFile()
    {
        self::$writer->start();

        self::$writer->wrap('baseWrap');

        self::$writer->wrap('items', [
            'id'   => 'someId',
            'name' => 'someName',
        ]);

        self::$writer->write(
            $this->getDataForWrite()
        );

        self::$writer->end();

        $this->assertSame(
            self::$resultStr,
            file_get_contents(self::$filePath)
        );
    }

    public function getDataForWrite()
    {
        return [
            'item' => [
                '@attr'  => 'itemAttr',
                'name'   => [
                    '@id'   => "someId",
                    '@attr' => "someAttr",
                    '='     => "Yakiv",
                ],
                'images' => [
                    'image' => [
                        "https://www.google.com",
                        "https://yandex.ru",
                    ],
                ],
            ],
        ];
    }

    public static function tearDownAfterClass()
    {
        self::$writer = null;
    }
}