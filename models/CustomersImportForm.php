<?php

namespace app\models;

use yii\base\Model;
use yii\web\UploadedFile;
use ruskid\csvimporter\CSVImporter;
use ruskid\csvimporter\CSVReader;
use ruskid\csvimporter\MultipleUpdateStrategy;
use yii\helpers\ArrayHelper;

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
			
			// Get all zones (deleted or not). Index array by gecom_id for easy search.
			$zones = ArrayHelper::map(Zone::find()->select(['id', 'gecom_id'])->where([])->asArray()->all(), 'gecom_id', 'id');

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
						'attribute' => 'address',
						'value' => function($line) {
							return $this->processValue($line[2]);
						},
					],
					[
						'attribute' => 'zip_code',
						'value' => function($line) {
							return $line[3];
						},
					],
					[
						'attribute' => 'locality',
						'value' => function($line) {
							return $this->processValue($line[4]);
						},
					],
					[
						'attribute' => 'province',
						'value' => function($line) {
							return $this->processValue($line[5]);
						},
					],
					[
						'attribute' => 'zone_id',
						'value' => function($line) use ($zones){
							return ArrayHelper::getValue($zones, $line[6], null);
						},
					],
					[
						'attribute' => 'phone_number',
						'value' => function($line) {
							return $line[7];
						},
					],
					[
						'attribute' => 'tax_situation',
						'value' => function($line) {
							return $line[8];
						},
					],
					[
						'attribute' => 'doc_number',
						'value' => function($line) {
							return $line[9];
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
