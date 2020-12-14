<?php

namespace Selene\Modules\AquaParkModule\Models;

use Jenssegers\Mongodb\Eloquent\Model;
use Selene\Modules\CityModule\Models\City;

/**
 * @property mixed _id
 */
class AquaPark extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'logo',
        'marker',
        'photo',
        'phone',
        'mail',
        'coordinates',
        'city',
        'address',
        'facebook',
        'instagram',
        'work_days',
        'work_hours',
        'season_low',
        'season_high',
        'gallery',
        'map',
        'icons',
        'order'
    ];

    /**
     * @var string
     */
    protected $connection = 'mongodb';

    public function getCity()
    {
        return City::query()->find($this->city);
    }
}
