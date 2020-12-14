<?php

namespace Selene\Modules\AquaParkModule\Models;

use Jenssegers\Mongodb\Eloquent\Model;

/**
 * @property mixed aqua_park
 * @property mixed _id
 */
class AquaParkPrice extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'icon',
        'titles',
        'descriptions',
        'icons',
        'aqua_park',
        'groups',
        'order'
    ];

    /**
     * @var string
     */
    protected $connection = 'mongodb';

    public function getAquaPark()
    {
        return AquaPark::query()->find($this->aqua_park);
    }
}
