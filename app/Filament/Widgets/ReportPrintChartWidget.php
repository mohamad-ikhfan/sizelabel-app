<?php

namespace App\Filament\Widgets;

use App\Models\ReportPrint;
use App\Models\User;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;

class ReportPrintChartWidget extends ChartWidget
{

    public function getHeading(): string|Htmlable|null
    {
        return !empty($this->filter) ? 'Result prints of ' . now()->parse($this->filter . '-01')->format('F, Y') : 'Result prints of ' . now()->format('F, Y');
    }

    protected static ?string $maxHeight = '300px';

    public ?string $filter = null;

    public array $rgbColors = [
        '0,0,0',
        '0,0,255',
        '255,0,0',
        '0,255,0',
        '255,255,0',
        '0,255,255',
        '255,0,255',
        '0,255,255',
        '255,255,255',
    ];

    protected function getData(): array
    {
        $datasets = [];
        $labels = [];

        $reportPrintByUsers = ReportPrint::select('user_id')
            ->groupBy('user_id')
            ->get();

        $useId = 1;
        foreach ($reportPrintByUsers as $reportPrintByUser) {
            $quantities = [];

            $reportPrintPerdays = ReportPrint::select('print_date')
                ->groupBy('print_date')
                ->whereYear('print_date', intval(!empty($this->filter) ? explode('-', $this->filter)[0] : now()->year))
                ->whereMonth('print_date', intval(!empty($this->filter) ? explode('-', $this->filter)[1] : now()->month))
                ->orderBy('print_date')
                ->get()
                ->toArray();

            foreach ($reportPrintPerdays as $reportPrintPerday) {
                $labels[] = now()->parse($reportPrintPerday['print_date'])->format('d M');

                $qtyPerdays = collect();
                $reportPrints = ReportPrint::where($reportPrintPerday)->where($reportPrintByUser->toArray())->get();

                foreach ($reportPrints as $reportPrint) {
                    $qtyPerdays->push($reportPrint->qty_total);
                }
                $quantities[] = $qtyPerdays->sum();
            }

            $label =  $reportPrintByUser->user->name ?? "NaN";
            $backgrounColor = $this->rgbColors[0] . ',0.2';
            $borderColor = $this->rgbColors[0] . ',1.0';

            if ($label !== "NaN") {
                $backgrounColor = $this->rgbColors[$useId] . ',0.2';
                $borderColor = $this->rgbColors[$useId] . ',1.0';
            }

            $datasets[] = [
                'label' => $label,
                'data' => $quantities,
                'backgroundColor' => "rgba($backgrounColor)",
                'borderColor' => "rgba($borderColor)",
                'tension' => 0.3
            ];

            $useId++;
        }

        return [
            'datasets' => $datasets,
            'labels' => array_unique($labels)
        ];
    }

    protected function getFilters(): ?array
    {
        $filters = [null => 'Select filter'];

        $reportPrints = ReportPrint::select('print_date')
            ->groupBy('print_date')
            ->orderBy('print_date', 'desc')
            ->get()
            ->toArray();

        foreach ($reportPrints as $reportPrint) {
            if (!is_numeric(array_search($reportPrint['print_date'], $filters))) {
                $filters += [now()->parse($reportPrint['print_date'])->format('Y-m') => now()->parse($reportPrint['print_date'])->format('F, Y')];
            }
        }

        return $filters;
    }

    protected function getType(): string
    {
        return 'line';
    }
}