<?php
namespace App\Http\Controllers\Api\Tools\ServiceCenter\ReturnsLog;

use App\Http\Requests\Tools\ServiceCenter\ReturnsLog\ServiceCenterReturnLogReturnRequest;
use App\Models\Returns;
use App\Models\ServiceCenter;
use App\Services\SuggestionsService;

class ReturnsLogController
{
    public function __construct(
        protected SuggestionsService $suggestionsService
    ) {}

    public function show(ServiceCenter $service_center, Returns $return)
    {
        return $return->load('checkInUser', 'sku', 'returnItems.sku');
    }

    public function update(ServiceCenterReturnLogReturnRequest $request, ServiceCenter $service_center, Returns $return)
    {
        return $return->update($request->input());
    }

    public function destroy(ServiceCenter $service_center, Returns $return)
    {
        $return->delete();
        return;
    }

    public function suggestedSkus(ServiceCenter $service_center, Returns $return)
    {
        $sku_name_attribute = $return->getSkuNameAttribute();
        // fallback in case sku doesn't match expected format
        try {
            $suggested_skus = $this->suggestionsService->getSuggestions($sku_name_attribute);
        } catch (\Exception) {
            $suggested_skus = Sku::withCount('returnItems')->orderByDesc('return_items_count')->limit(5)->get();
        }
        return $suggested_skus;
    }

}
