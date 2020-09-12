## Examples

##### For reading:
Create a reader object:
```
$reader = new \Y\Xml\Reader();
```
Specify the path to the file from which you want to receive data:
```
$reader->setFilePath(__DIR__ . '/files/test.xml');
```
Indicate nesting of cyclic elements:
```
$reader->setDepth('/data/items/item');
```
Specify aliases and tag paths:
```
$reader->setSchema([
    'name' => '/name',
    'surname' => '/surname',
    'age' => '/age',
    'images' => '/images/image',
    'imagesId' => '/images/@id',
    'imageId' => '/images/image/@id',
]);
```
Retrieving data for big xml files:
```
$all = [];
foreach ($reader->get() as $part) {
    // You can get data by alias
    $name = $part->get('name');    // | $name = $part->name;

    // Or get all the data that you specified in the scheme
    $all[] = $part->getAll();
}

// OR for small files
$all = $reader->getAll();
```
An example is given for a file of type:
```
<?xml version="1.0" encoding="UTF-8"?>
<data>
    <items>
        <item>
            <name>Test</name>
            <surname>Test</surname>
            <age>25</age>
            <images id="photo">
                <image id="someId">image1</image>
                <image id="someId2">image2</image>
            </images>
        </item>
    </items>
</data>
```
##### For writing:
Create a writer object:
```
$writer = new \Y\Xml\XMLW();
```
Set the path where the file will be written:
```
$writer->setFilePath(__DIR__ . '/files/test.xml');
```
If you do not specify these parameters, they will be the default:
```
$writer->setParameters([
    'root'     => 'data',     // by default
    'node'     => 'node',     // by default
    'version'  => '1.0',      // by default
    'encoding' => 'UTF-8',    // by default
]);
```
Next, start writing:
```
$writer->start();
```
To wrap data in a base tag:
```
$writer->wrap('baseWrap');
```
To wrap data in a base tag with attributes:
```
$writer->wrap('items', [
    'id' => 'someId',
    'name' => 'someName',
]);
```
For attributes use `@`, for value use `=`.
You can use it just 'name' => 'Test' for type value, but then you cannot use attributes.
```
$arr = [
   'item' => [
       '@attr' => 'itemAttr',
       'name' => [
           '@id' => "someId",
           '@attr' => "someAttr",
           '=' => "Yakiv",
       ],
       'images' => [
           'image' => [
               "https://www.google.com",
               "https://yandex.ru",
           ],
       ],
   ],
];

```
Convert the array to xml and write to the file:
```
$writer->write($arr);
```
End writing (required):
```
$writer->end();
```
Output file:
```
<?xml version="1.0" encoding="UTF-8"?>
<data>
    <baseWrap>
        <items id="someId" name="someName">
            <item attr="itemAttr">
                <name id="someId" attr="someAttr">Yakiv</name>
                <images>
                    <image>https://www.google.com</image>
                    <image>https://yandex.ru</image>
                </images>
            </item>
        </items>
    </baseWrap>
</data>
```