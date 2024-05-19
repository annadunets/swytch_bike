<?php
namespace App\Services;

interface SuggestionsServiceInterface
{
    public function getSuggestions(string $sku_name_attribute): array;
}
