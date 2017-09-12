<?php

namespace app\validators;

use yii\validators\RegularExpressionValidator;

class InvoiceValidator extends RegularExpressionValidator {

	/**
	 * @inheritdoc
	 */
	public $pattern = '/^[A|B]\d{2}-\d{5}$/';

	/**
	 * @inheritdoc
	 */
	public $message = 'El número de factura debe tener el formato: AXX-XXXXX o BXX-XXXXX';
}
