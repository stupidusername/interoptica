<?php

namespace app\models;

use yii\base\Model;
use yii\web\UploadedFile;
use ruskid\csvimporter\CSVImporter;
use ruskid\csvimporter\CSVReader;
use ruskid\csvimporter\MultipleUpdateStrategy;
use app\helpers\ImportHelper;

class ProductsImportForm extends Model {

	const SCENARIO_PRICE = 'price';
	const SCENARIO_STOCK = 'stock';

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
	public function scenarios() {
		return [
			self::SCENARIO_PRICE => ['file'],
			self::SCENARIO_STOCK => ['file'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'file' => 'Archivo',
		];
	}

	/**
	 * Prepares values for db insert
	 * @param string $value
	 * @param string $fromEncoding
	 * @return string
	 */
	public function processValue($value, $fromEncoding) {
		return mb_convert_encoding($value, ImportHelper::TO_ENCODING, $fromEncoding);
	}

	/**
	 * Imports csv file to customers database.
	 * @return boolean whether upload was successful
	 */
	public function import() {
		if ($this->validate()) {

			switch ($this->scenario) {
				case self::SCENARIO_PRICE:
					$delimiter = ';';
					$startFromLine = 2;
					$cvsKey = function ($line) {
						return $this->processValue($line[0], 'CP850');
					};
					$configs = [
							[
							'attribute' => 'gecom_code',
							'value' => function($line) {
									return $this->processValue($line[0], 'CP850');
								},
							],
							[
							'attribute' => 'gecom_desc',
							'value' => function($line) {
									return $this->processValue($line[1], 'CP850');
								},
							],
							[
							'attribute' => 'price',
							'value' => function($line) {
									return (float) filter_var($line[3], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
								},
							],
					];
					$skipImport = function ($line) {
						return	!$line[0] || count($line) != 5;
					};
					break;
				case self::SCENARIO_STOCK:
					$delimiter = ',';
					$startFromLine = 0;
					$cvsKey = function ($line) {
						return $this->processValue($line[0], 'ISO-8859-3');
					};
					$configs = [
							[
							'attribute' => 'gecom_code',
							'value' => function($line) {
									return $this->processValue($line[0], 'ISO-8859-3');
								},
							],
							[
							'attribute' => 'gecom_desc',
							'value' => function($line) {
									return $this->processValue($line[1], 'ISO-8859-3');
								},
							],
							[
							'attribute' => 'stock',
							'value' => function($line) {
									return (int) $line[3];
								},
							],
					];
					$skipImport = function ($line) {
						return	!$line[0] || count($line) != 8;
					};
					break;
			}

			$importer = new CSVImporter;

			//Will read CSV file
			$importer->setData(new CSVReader([
				'filename' => $this->file->tempName,
				'startFromLine' => $startFromLine,
				'fgetcsvOptions' => [
					'delimiter' => $delimiter,
					'enclosure' => chr(8),
				]
			]));

			$records = $importer->import(new MultipleUpdateStrategy([
				'className' => Product::className(),
				'csvKey' => $cvsKey,
				'rowKey' => function ($row) {
					return $row['gecom_code'];
				},
				'skipImport' => $skipImport,
				'configs' => $configs,
			]));
			return true;
		} else {
			return false;
		}
	}

}
