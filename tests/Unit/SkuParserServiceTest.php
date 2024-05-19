<?php
namespace Tests\Unit;

use App\Services\SkuParserService;
use PHPUnit\Framework\TestCase;

class SkuParserServiceTest extends TestCase
{
    public function testParseSkuNameAttribute()
    {
        $example_sku = 'KIT-ECO-NAR-349-S';
        $parserService = new SkuParserService();
        $result = $parserService->parseSkuNameAttribute($example_sku);

        $this->assertEquals('ECO', $result['powerpack_size']);
        $this->assertEquals('NAR', $result['wheelstype']);
        $this->assertEquals('349', $result['wheelsize']);
        $this->assertEquals('S', $result['colour']);
    }

    public function testParseSkuNameAttributeIncorrectFormat()
    {
        $example_sku = 'NAR-349-B';
        $this->expectExceptionMessage("Incorrect SKU format: " . $example_sku);
        $parserService = new SkuParserService();
        $parserService->parseSkuNameAttribute($example_sku);
    }
}
