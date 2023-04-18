<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Material extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = ['material_group_id', 'name', 'code', 'description'];

    public function material_group(): BelongsTo
    {
        return $this->belongsTo(MaterialGroup::class);
    }
}
