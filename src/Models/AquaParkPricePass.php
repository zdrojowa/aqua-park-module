<?php

namespace Selene\Modules\AquaParkModule\Models;

use Jenssegers\Mongodb\Eloquent\Model;

/**
 * @property mixed aqua_park_price
 */
class AquaParkPricePass extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'icon',
        'titles',
        'descriptions',
        'prices',
        'aqua_park_price',
        'order'
    ];

    /**
     * @var string
     */
    protected $connection = 'mongodb';

    public function getAquaParkPrice()
    {
        return AquaParkPrice::query()->find($this->aqua_park_price);
    }
}
