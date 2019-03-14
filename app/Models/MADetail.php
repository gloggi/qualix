<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $ma_id
 * @property string $ma_definition
 * @property int $killer
 * @property MA $ma
 */
class MADetail extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ma_detail';

    /**
     * @var array
     */
    protected $fillable = ['ma_id', 'ma_definition', 'killer'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ma()
    {
        return $this->belongsTo('App\Models\MA');
    }
}
