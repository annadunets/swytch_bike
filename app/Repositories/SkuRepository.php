<?php
namespace App\Repositories;

use App\Models\Sku;

class SkuRepository
{
    public function getSkuByAttributeName(string $sku_name_attribute): ?\App\Models\Sku
    {
        return Sku::where('sku', 'like', $sku_name_attribute)->first();
    }

}
