<?php

namespace App\Ingestors\CrowdOx\Transformers;

use App\CrowdOxOrder;
use App\CrowdOxOrderLine;
use App\CrowdOxProduct;
use App\CrowdOxProject;
use App\Ingestors\CrowdOx\Transformers\Contracts\CrowdOxTransformersContract;
use Illuminate\Database\Eloquent\Model;

class OrderLine implements CrowdOxTransformersContract {

    /**
     * Records the remote ticket field to the database
     *
     * @param object $remote_order_line
     * @return Model
     */
    public function transform(object $remote_order_line): Model {
        //get relationships
        $project = CrowdOxProject::where('crowd_ox_id', $remote_order_line->relationships->project->data->id)->firstOrFail();
        $order = CrowdOxOrder::where('crowd_ox_id', $remote_order_line->relationships->order->data->id)->firstOrFail();
        $product_bundle_remote =  $remote_order_line->relationships->{'product-bundle'}->data;
        if ($product_bundle_remote) {
            $product = CrowdOxProduct::where('crowd_ox_id',$product_bundle_remote->id)->first();
        }
        $price_data = $this->getPriceDetails($remote_order_line);

        //record
        $order_line = CrowdOxOrderLine::updateIfChangedOrCreate(["crowd_ox_id" => $remote_order_line->id], [
            "type" => $remote_order_line->attributes->{'line-type'},
            "crowd_ox_project_id" => $project->id,
            "crowd_ox_product_id" => $product_bundle_remote ? $product->id : null,
            "crowd_ox_order_id" => $order->id ], [
                "raw_data" => json_encode($remote_order_line),
                "crowd_ox_line_type" => $price_data['line_type'],
                "crowd_ox_price_product" => $price_data['product_price_cents'],
                "crowd_ox_price_shipping" => $price_data['shipping_price_cents'],
                "crowd_ox_price_total" => $price_data['total_price_cents']
            ]
        );


        return $order_line;
    }

   /**
    * Extracts price details from a remote order line object.
    *
    * @param object $remote_order_line
    * @return array
    */
    private function getPriceDetails(object $remote_order_line): array
    {
        $raw_data = $remote_order_line;

        $line_type = null;
        $product_price = null;
        $shipping_price = null;
        $total_price = null;

        if (isset($raw_data->attributes->{'line-type'})) {
          $line_type = $raw_data->attributes->{'line-type'};
        }

        if (isset($raw_data->attributes->{'price-data'}->prices)) {
          foreach ($raw_data->attributes->{'price-data'}->prices as $price) {
            if($price->type == "product") {
              $product_price = $price->{'amount_cents'};
            } else if($price->type == "shipping") {
              $shipping_price = $price->{'amount_cents'};
            }
          }
        }

        if (isset($raw_data->attributes->{'price-cents'})) {
          $total_price = $raw_data->attributes->{'price-cents'};
        }

        return [
          'line_type' => $line_type,
          'product_price_cents' => $product_price,
          'shipping_price_cents' => $shipping_price,
          'total_price_cents' => $total_price
        ];
    }

}
