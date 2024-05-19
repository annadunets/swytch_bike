<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;



class CrowdOxOrderLine extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'crowd_ox_id',
        'type',
        'crowd_ox_project_id',
        'crowd_ox_order_id',
        'crowd_ox_product_id',
        'raw_data',
        'crowd_ox_line_type',
        'crowd_ox_price_product',
        'crowd_ox_price_shipping',
        'crowd_ox_price_total'
    ];

    public function crowdOxProject() {
        return $this->belongsTo(CrowdOxProject::class);
    }

    public function crowdOxOrder() {
        return $this->belongsTo(CrowdOxOrder::class);
    }

    public function crowdOxProductBundle() {
        return $this->belongsTo(CrowdOxProduct::class, 'crowd_ox_product_id')->withDefault();
    }

    public function crowdOxOrderSelections() {
        return $this->hasMany(CrowdOxOrderSelection::class);
    }

}
