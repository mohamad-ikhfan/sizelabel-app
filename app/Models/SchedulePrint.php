<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchedulePrint extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function shoe(): BelongsTo
    {
        return $this->belongsTo(Shoe::class);
    }

    public function status_updated_by_user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}