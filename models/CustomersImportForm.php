<?php

namespace app\models;

use yii\base\Model;
use yii\web\UploadedFile;
use ruskid\csvimporter\CSVImporter;
use ruskid\csvimporter\CSVReader;
use ruskid\csvimporter\MultipleUpdateStrategy;
use yii\helpers\ArrayHelper;
use app\helpers\ImportHelper;

class CustomersImportForm extends Model {

	/**
	 * @var UploadedFile
	 */
	public $file;

	public function rules() {
		return [
				[['file'], 'file', 'skipOnEmpty' => false, 'extensions' => ['csv'], 'checkExtensionByMimeType' => false],
		];
	}
	
	/**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'file' => 'Archivo',
        ];
    }

	/**
	 * Prepares values for db insert
	 * @param string $value
	 * @return string
	 */
	public function processValue($value, $toEncoding) {
		return ImportHelper::processValue($value, $toEncoding);
	}
	
	/**
	 * Imports csv file to customers database.
	 * @return boolean whether upload was successful
	 */
	public function import() {
		if ($this->validate()) {
			$importer = new CSVImporter;

			//Will read CSV file
			$importer->setData(new CustomerReader([
				'filename' => $this->file->tempName,
			]));
			
			// Get all zones (deleted or not). Index array by gecom_id for easy search.
			$zones = ArrayHelper::map(Zone::find()->select(['id', 'gecom_id'])->where([])->asArray()->all(), 'gecom_id', 'id');

			$records = $importer->import(new MultipleUpdateStrategy([
				'className' => Customer::className(),
				'csvKey' => function ($line) {
					return isset($line[1]) ? $line[1] : null;
				},
				'rowKey' => function ($row) {
					return $row['gecom_id'];
				},
				'skipImport' => function ($line) {
					return !isset($line[1]);
				},
				'configs' => [
					[
						'attribute' => 'gecom_id',
						'value' => function($line) {
							return $line[1];
						},
					],
					[
						'attribute' => 'name',
						'value' => function($line) {
							return $this->processValue($line[2], 'UTF-8');
						},
					],
					[
						'attribute' => 'address',
						'value' => function($line) {
							return $this->processValue($line[3], 'UTF-8');
						},
					],
					[
						'attribute' => 'zip_code',
						'value' => function($line) {
							return $line[4];
						},
					],
					[
						'attribute' => 'locality',
						'value' => function($line) {
							return $this->processValue($line[5], 'UTF-8');
						},
					],
					[
						'attribute' => 'province',
						'value' => function($line) {
							return $this->processValue($line[6], 'UTF-8');
						},
					],
					[
						'attribute' => 'zone_id',
						'value' => function($line) use ($zones){
							return ArrayHelper::getValue($zones, $line[7], null);
						},
					],
					[
						'attribute' => 'phone_number',
						'value' => function($line) {
							return $line[8];
						},
					],
					[
						'attribute' => 'tax_situation',
						'value' => function($line) {
							return $line[9];
						},
					],
					[
						'attribute' => 'doc_number',
						'value' => function($line) {
							return $line[10];
						},
					],
				],
			]));
			return true;
		} else {
			return false;
		}
	}

}

class CustomerReader extends CSVReader {
	
	/**
     * Will read customers file into array
     * @throws Exception
     * @return $array customer file filtered data [n][0] it's the whole line
     */
    public function readFile() {
		$lines = [];
        if (!file_exists($this->filename)) {
            throw new Exception(__CLASS__ . ' couldn\'t find the customer file.');
        }
		$handle = fopen($this->filename, "r");
		if ($handle) {
			$i = 0;
			$value = '';
			while (($line = fgets($handle)) !== false) {
				$value .= $line;
				$i++;
				if ($i % 3 == 0) {
					$value = str_replace('""', '"', mb_convert_encoding($value, 'UTF-8', 'ISO-8859-1'));
					$matches = [];
					preg_match_all('/\D*(\d*)\w*\s*(.{31})(.{31})\s*(\d*)\s*(.{16})(.{16})\w*\s*(\d*)\s*.*;[\r\n]+\s*(.{49}).{27}(\w)\s(.{13}).*;[\r\n]+.*;/u', $value, $matches, PREG_SET_ORDER);
					$lines[] = isset($matches[0]) ? $matches[0] : [];
					$value = '';
				}
			}
			fclose($handle);
		}
		return $lines;
    }
}
