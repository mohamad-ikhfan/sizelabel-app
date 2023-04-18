<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMaterial extends Model
{
    use HasFactory;
    protected $fillable = ['material_id', 'date', 'quantity', 'description', 'status', 'first_stock', 'last_stock'];

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }
}
