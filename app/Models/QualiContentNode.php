<?php

namespace App\Models;

/**
 * @property int $id
 * @property int $quali_id
 * @property int $order
 * @property string $json
 * @property Quali $quali
 */
class QualiContentNode extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'quali_content_nodes';

    /**
     * @var array
     */
    protected $fillable = ['order', 'json'];
    protected $fillable_relations = ['quali'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function quali() {
        return $this->belongsTo(Quali::class);
    }
}
