<?php

use dektrium\rbac\migrations\Migration;

class m170703_211453_create_roles extends Migration
{

	# Added just for compat with standard migrations
	public $db;
	public $compact;

	public function safeUp()
	{
		$this->createRole('admin', 'Administrador');
		$this->createRole('management', 'Administración');
		$this->createRole('customerSupport', 'Atención');
		$this->createRole('depot', 'Depósito');
		$this->createRole('salesman', 'Vendedor');
		$this->createRole('credit', 'Crédito');
	}

	public function safeDown()
	{
		$this->removeItem('admin');
		$this->removeItem('management');
		$this->removeItem('customerSupport');
		$this->removeItem('depot');
		$this->removeItem('salesman');
		$this->removeItem('credit');
	}
}
