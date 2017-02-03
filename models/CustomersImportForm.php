<?php

namespace app\models;

use yii\base\Model;
use yii\web\UploadedFile;
use ruskid\csvimporter\CSVImporter;
use ruskid\csvimporter\CSVReader;
use ruskid\csvimporter\MultipleUpdateStrategy;

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
	 * Prepares values for db insert
	 * @param string $value
	 * @return string
	 */
	public function processValue($value) {
		return ucwords(mb_strtolower(mb_convert_encoding($value, 'UTF-8', 'ISO-8859-3')), "_-. \t\r\n\f\v");
	}
	
	/**
	 * Imports csv file to customers database.
	 * @return boolean whether upload was successful
	 */
	public function import() {
		if ($this->validate()) {
			$importer = new CSVImporter;

			//Will read CSV file
			$importer->setData(new CSVReader([
				'filename' => $this->file->tempName,
				'fgetcsvOptions' => [
					'delimiter' => ';',
					'enclosure' => chr(8),
				]
			]));

			$records = $importer->import(new MultipleUpdateStrategy([
				'className' => Customer::className(),
				'csvKey' => function ($line) {
					return $line[0];
				},
				'rowKey' => function ($row) {
					return $row['gecom_id'];
				},
				'skipImport' => function ($line) {
					return !$line[0];
				},
				'configs' => [
					[
						'attribute' => 'gecom_id',
						'value' => function($line) {
							return $line[0];
						},
					],
					[
						'attribute' => 'name',
						'value' => function($line) {
							return $this->processValue($line[1]);
						},
					],
					[
						'attribute' => 'tax_situation',
						'value' => function($line) {
							return $this->processValue($line[2]);
						},
					],
					[
						'attribute' => 'address',
						'value' => function($line) {
							return $this->processValue($line[3]);
						},
					],
					[
						'attribute' => 'zip_code',
						'value' => function($line) {
							return $this->processValue($line[4]);
						},
					],
					[
						'attribute' => 'locality',
						'value' => function($line) {
							return $this->processValue($line[5]);
						},
					],
					[
						'attribute' => 'phone_number',
						'value' => function($line) {
							return $this->processValue($line[6]);
						},
					],
					[
						'attribute' => 'doc_number',
						'value' => function($line) {
							return $this->processValue($line[7]);
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
