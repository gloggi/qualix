<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $kurs_id
 * @property string $email
 * @property string $token
 * @property Kurs $kurs
 */
class Einladung extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'einladung';

    /**
     * @var array
     */
    protected $fillable = ['kurs_id', 'email', 'token'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function kurs()
    {
        return $this->belongsTo('App\Models\Kurs', 'kurs_id');
    }
}
