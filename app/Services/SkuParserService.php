<?php
namespace App\Services;

class SkuParserService
{
    public function parseSkuNameAttribute($sku)
    {
        $sku_attributes = explode('-', $sku);
        if(count($sku_attributes) !== 5){
            throw new \Exception("Incorrect SKU format: " . $sku);
        }

        return [
            'powerpack_size' => $sku_attributes[1],
            'wheelstype' => $sku_attributes[2],
            'wheelsize' => $sku_attributes[3],
            'colour' => $sku_attributes[4]
        ];
    }
}
