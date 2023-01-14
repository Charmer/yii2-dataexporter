Yii2 data file exporter
=======================
Exports Yii2 data provider models to file using batch iteration and temp file for reduce memory usage

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist charmer/yii2-dataexporter "*"
```

or add

```
"charmer/yii2-dataexporter": "*"
```

to the require section of your `composer.json` file.


Usage
-----

Example web controller with action that returns file for download: 
```php
<?php

namespace app\controllers;

use app\models\Clients;
use charmer\dataexporter\Exporter;
use charmer\dataexporter\Writers\CsvWriter;
use yii\web\Controller;
use yii\data\ActiveDataProvider;

class TestController extends Controller
{
    public function actionTest()
    {
        //Create Exporter object
        $exporter = new Exporter();
        //Create temp file
        $exporter->initTmpFile();
        //Create writer for CSV file format
        $writer = new CsvWriter();
        //Headers for exporting columns, ['model attribute' => 'Label']
        //you can simply use Model::attributeLabels() if you want to export all fields
        //or just type only fields you need for export
        $headers = [
            'id' => '№',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'email' => 'E-Mail',
            'created_at' => 'Created at'
        ];
        
        //Set fields for export
        $writer->setFields($headers);
        
        //Set writer to exporter
        $exporter->setWriter($writer);

        //Create DataProvider for export
        $provider = new ActiveDataProvider([
            'query' => Clients::find(),
            'pagination' => [
                'pageSize' => 5,
            ],
        ]);

        //Set created data provider to exporter 
        $exporter->setDataProvider($provider);
        //Prepare for export
        $exporter->prepare();
        //Export
        $exporter->export();
        
        //Get temp file path
        $path = $exporter->getTmpFileMetaData()['uri'];
        
        //return file for download
        return Yii::$app->response->sendFile($path, 'export.csv');
    }
}
```

Using different file type writers
---------------------------------

There are 3 default writers in this package: **CSV**, **JSON** and **XML**. All you need to change exported file format is create needed writer object:
```php
$csvWriter = new CsvWriter();
$jsonWriter = new JsonWriter();
$xmlWriter = new XmlWriter();
```
All other operations are the same for both writers.

Create own writers
------------------
For creating you own writer you need to extend your custom writer class from  ```charmer\dataexporter\Writers\BaseWriter```
and implement ```write(array $data)``` method:

```php
<?php

namespace charmer\dataexporter\Writers;

class TsvWriter extends BaseWriter
{
    public function write(array $data)
    {
        $this->exportedCount++;
        $row = implode('\t', $data)
        fputs($this->tmpFile, $row."\n");
    }
}
```

You can use ```BaseWriter``` class properties:
* ```mixed $tmpFile``` - temp file for export data
* ```int $exportedCount``` - number of exported items in current moment
* ```int $totalCount``` - total count of exporting items (DataProvider::getTotalCount() value)

You can use ```BaseWriter``` class methods:
* ```public Writer::initWriter()``` - if this method exists in your writer, it will be called right **before** ```Writer::write()```
* ```closeWriter()``` - if this method exists in your writer, it will be called right **after** ```Writer::write()```
* ```public function writeHeader()``` - writes headers line (from ```$writer->setFields($headers);```) of exported fields (for example, for CSV export)

Example of ```JsonWriter```:
```php
<?php

namespace charmer\dataexporter\Writers;

class JsonWriter extends BaseWriter
{
    //Writes opening array symbol "[" in the beginning of the JSON file
    public function initWriter()
    {
        fputs($this->tmpFile, '[');
    }

    //Writes closing array symbol "]" in the end of the JSON file
    public function closeWriter()
    {
        fputs($this->tmpFile, ']');
    }

    //Writes JSON-serialized data to file
    public function write(array $data)
    {
        fputs($this->tmpFile, json_encode($data));
        $this->exportedCount++;

        if ($this->exportedCount < $this->totalCount) {
            fputs($this->tmpFile, ',');
        }
    }
}
```

You can use other writer class as the example of using other ```Writer``` methods and properties 
