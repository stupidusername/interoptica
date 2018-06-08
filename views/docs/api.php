<?php

use app\models\Customer;
use nezhelskoy\highlight\HighlightAsset;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */

$this->title = 'API';

HighlightAsset::register($this);
?>

<h1>API endpoints</h1>

<h2>api/get-orders</h2>

<p>
  Devuelve la información de todos los pedidos. Opcionalmente se pueden filtrar
  los pedidos a los cuales se les cambio el estado a partir de una fecha dada.
</p>

<h3>Parámetros</h3>

<ul>
  <li>key: API secret key</li>
  <li>updated_since (optional): Fecha con el formato yyyy-mm-dd. Si es brindada
    se devolveran los pedidos cuyo estado haya cambiado a partir de la misma.
  </li>
  <li>page (optional): Número de página de los resultados. Si no es brindado la
    primer página sera devuelta (la primer página es la número 0).
  </li>
</ul>

<p>Nota: Las fechas y horarios usados por esta API usan la zona horaria GMT.</p>

<h3>Situación frente al IVA de los clientes</h3>

<p>
  Esta información es indicada por el campo "tax_situation" que puede tomar lo siguientes valores:
  <?= Html::ul(array_map(function ($key, $val) { return "$key: $val"; }, array_keys(Customer::taxSituationLabels()), Customer::taxSituationLabels())) ?>
  En caso de tomar el valor de "M", el campo "tax_situation_category" indica la categoría de monotributo.
</p>

<h3>Ejemplo</h3>

<p>Request a <?= Url::to(['api/get-orders', 'key' => '123', 'updated_since' => '2018-01-01', 'page' => 0], true) ?></p>

<pre><code class="json">
{
  "meta": {
    "total-pages": 1
  },
  "data": [
    {
      "id": 1,
      "status": "billing",
      "status_update_datetime": "2018-03-25T12:00:00Z",
      "discount_percentage": 12.5,
      "subtotal": 1000,
      "total": 875,
      "include_iva": true,
      "relationships": {
        "customer": {
          "id": 1,
          "gecom_id": 1001,
          "name": "Customer One",
          "email": "customer@one.com",
          "cuit": "00-00000000-0",
          "tax_situation": "M",
          "tax_situation_category": "A",
          "phone_number": "111-1111"
        },
        "items": [
          {
            "id": 1,
            "code": "1",
            "title": "Product One",
            "unit_price": 100,
            "quantity": 10,
            "imported": true,
            "relationships": {
              "batches": [
                {
                  "id": 1,
                  "dispatch_number": "1",
                  "quantity": 5
                },
                {
                  "id": 2,
                  "dispatch_number": null,
                  "quantity": 5
                },
              ]
            }
          }
        ]
      }
    }
  ]
}
</code></pre>

<h3>Esquema</h3>

<pre><code class="json">
{
  "title": "Order set",
  "type": "object",
  "properties": {
    "meta": {
      "type": "object",
      "properties": {
        "total-pages": {
          "type": "number"
        }
      }
    },
    "data": {
      "type": "array",
      "items": {
        "title": "Order",
        "type": "object",
        "properties": {
          "id": {
            "type": "number"
          },
          "status": {
            "type": "string",
            "enum": [
              "deleted",
              "entered",
              "collect",
              "collect_revision",
              "administration",
              "pending_put_together",
              "put_together",
              "put_together_printed",
              "billing",
              "packaging",
              "waiting_for_transport",
              "sent",
              "delivered"
            ]
          },
          "status_update_datetime": {
            "format": "date-time",
            "type": "string"
          },
          "discount_percentage": {
            "type": "number"
          },
          "subtotal": {
            "description": "Discount not included (IVA not included)",
            "type": "number"
          },
          "total": {
            "description": "Discount included (IVA not included)",
            "type": "number"
          },
          "include_iva": {
            "description": "Indicates whether IVA should be charged",
            "type": "boolean"
          },
          "relationships": {
            "type": "object",
            "properties": {
              "customer": {
                "type": "object",
                "properties": {
                  "id": {
                    "type": "number"
                  },
                  "gecom_id": {
                    "type": "number"
                  },
                  "name": {
                    "type": "string"
                  },
                  "email": {
                    "format": "email",
                    "type": "string"
                  },
                  "cuit": {
                    "type": "string"
                  },
                  "tax_situation": {
                    "type": "string"
                  },
                  "tax_situation_category": {
                    "type": ["null", "string"]
                  },
                  "phone_number": {
                    "type": "string"
                  }
                }
              },
              "items": {
                "type": "array",
                "items": {
                  "title": "Item",
                  "type": "object",
                  "attrbitues": {
                    "id": {
                      "type": "number"
                    },
                    "code": {
                      "description": "SKU",
                      "type": "string"
                    },
                    "title": {
                      "type": "string"
                    },
                    "unite_price": {
                      "type": "number"
                    },
                    "quantity": {
                      "type": "number"
                    },
                    "imported": {
                      "type": "boolean",
                    },
                    "relationships": {
                      "type": "object",
                      "properties": {
                        "batches": {
                          "type": "array",
                          "items": {
                            "title": "Batch",
                            "type": "object",
                            "attributes": {
                              "id": {
                                "type": "number"
                              },
                              "dispatch_number": {
                                "type": ["null", "string"]
                              },
                              "quantity": {
                                "type": "number"
                              }
                            }
                          }
                        }
                      }
                    }
                  }
                }
              }
            }
          }
        }
      }
    }
  }
}
</code></pre>
