<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Sushi\Sushi;

class AccuracyOfPrint extends Model
{
    use HasFactory, Sushi;

    protected $schema = [
        'schedule' => 'date',
        'print_date' => 'date',
        'accuracy' => 'float',
    ];

    public function getRows()
    {
        $rows = collect();
        $schedulePrints = SchedulePrint::whereNotNull('status_updated_at')
            ->get();

        foreach ($schedulePrints as $schedulePrint) {
            $rows->push([
                'schedule' => $schedulePrint->schedule,
                'print_date' => $schedulePrint->status_updated_at,
                'accuracy' => round((now()->parse($schedulePrint->status_updated_at)->diffInDays(now()) / now()->parse($schedulePrint->schedule)->diffInDays(now())) * 100, 1)
            ]);
        }
        return $rows->toArray();
    }
}