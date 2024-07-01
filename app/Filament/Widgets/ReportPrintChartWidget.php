<?php

namespace App\Filament\Widgets;

use App\Models\ReportPrint;
use App\Models\User;
use Filament\Widgets\ChartWidget;

class ReportPrintChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Report Print Perdays';

    protected static ?string $maxHeight = '300px';

    public ?string $filter = "";

    protected function getData(): array
    {
        $datasets = [];
        $labels = [];

        $reportPrintByUsers = ReportPrint::select('user_id')
            ->groupBy('user_id')
            ->get();

        foreach ($reportPrintByUsers as $reportPrintByUser) {
            $quantities = [];

            $reportPrintPerdays = ReportPrint::select('print_date')
                ->groupBy('print_date')
                ->whereMonth('print_date', intval(!empty($this->filter) ? $this->filter : now()->month))
                ->get()
                ->toArray();
            sort($reportPrintPerdays);

            foreach ($reportPrintPerdays as $reportPrintPerday) {
                $labels[] = now()->parse($reportPrintPerday['print_date'])->format('d M');

                $qtyPerdays = collect();
                $reportPrints = ReportPrint::where($reportPrintPerday)->where($reportPrintByUser->toArray())->get();

                foreach ($reportPrints as $reportPrint) {
                    $qtyPerdays->push($reportPrint->qty_total);
                }
                $quantities[] = $qtyPerdays->sum();
            }

            $datasets[] = [
                'label' => $reportPrintByUser->user->name,
                'data' => $quantities,
                'backgroundColor' => $reportPrintByUser->user_id == 1 ? 'rgba(0, 0, 255,  0.2)' : 'rgba(0, 255, 0,  0.2)',
                'borderColor' => $reportPrintByUser->user_id == 1 ? 'rgba(0, 0, 255, 1.0)' : 'rgba(0, 255, 0, 1.0)',
                'tension' => 0.3
            ];
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
            ->get()
            ->toArray();

        foreach ($reportPrints as $reportPrint) {
            if (!is_numeric(array_search($reportPrint['print_date'], $filters))) {
                $filters += [now()->parse($reportPrint['print_date'])->format('m') => now()->parse($reportPrint['print_date'])->format('F, Y')];
            }
        }

        return $filters;
    }

    protected function getType(): string
    {
        return 'line';
    }
}