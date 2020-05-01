<?php

namespace App\Models;

/**
 * @property int $id
 * @property int $quali_id
 * @property int $order
 * @property string $notes
 * @property Quali $quali
 */
class QualiNote extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'quali_notes';

    /**
     * @var array
     */
    protected $fillable = ['order', 'notes'];
    protected $fillable_relations = ['quali'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function quali() {
        return $this->belongsTo(Quali::class);
    }
}
