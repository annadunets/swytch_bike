<?php
namespace App\Services;

use App\Repositories\SkuRepository;

class SuggestionsService implements SuggestionsServiceInterface
{
    public function __construct(
        protected SkuParserService $skuParserService,
        protected SkuRepository $skuRepository
    ) {}

    public function getSuggestions($sku_name_attribute): array
    {
        // Fetch the SKUs based on the attribute name and the generated names
        $sku_kit = $this->skuRepository->getSkuByAttributeName($sku_name_attribute);

        $sku_attributes = $this->skuParserService->parseSkuNameAttribute($sku_name_attribute);

        $sku_pack = $this->getPackSuggestion($sku_attributes);

        $sku_wheel = $this->getWheelSuggestion($sku_attributes);

        // Collect all SKUs in an array
        $skus = [$sku_kit, $sku_pack, $sku_wheel];

        // Filter out null values
        $skus = array_filter($skus, function ($sku) {
            return !is_null($sku);
        });

        return $skus;
    }

    /**
     * @param array $sku_attributes
     * @return \App\Models\Sku|null
     */
    public function getPackSuggestion(array $sku_attributes): ?\App\Models\Sku
    {
        $pack_sku = sprintf("PAC-%s-%s-%s", $sku_attributes["powerpack_size"], $sku_attributes["wheelstype"], $sku_attributes["wheelsize"]);
        return $this->skuRepository->getSkuByAttributeName($pack_sku);
    }

    /**
     * @param array $sku_attributes
     * @return \App\Models\Sku|null
     */
    public function getWheelSuggestion(array $sku_attributes): ?\App\Models\Sku
    {
        $wheel_sku = sprintf("%s-%s-%s", $sku_attributes["wheelstype"], $sku_attributes["wheelsize"], $sku_attributes["colour"]);
        $sku_wheel = $this->skuRepository->getSkuByAttributeName($wheel_sku);
        return $sku_wheel;
    }


}
