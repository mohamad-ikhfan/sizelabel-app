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

            if (count($reportPrintPerdays) > 0) {
                sort($reportPrintPerdays);
            }

            foreach ($reportPrintPerdays as $reportPrintPerday) {
                $labels[] = now()->parse($reportPrintPerday['print_date'])->format('d M');

                $qtyPerdays = collect();
                $reportPrints = ReportPrint::where($reportPrintPerday)->where($reportPrintByUser->toArray())->get();

                foreach ($reportPrints as $reportPrint) {
                    $qtyPerdays->push($reportPrint->qty_total);
                }
                $quantities[] = $qtyPerdays->sum();
            }

            $label = "NaN";
            $backgrounColor = 'rgba(0, 0, 0,  0.2)';
            $borderColor = 'rgba(0, 0, 0,  0.1)';

            if ($reportPrintByUser->user) {
                $label = $reportPrintByUser->user->name;
                switch ($reportPrintByUser->user->user_id) {
                    case 1:
                        $backgrounColor = 'rgba(0, 0, 255,  0.2)';
                        $borderColor = 'rgba(0, 0, 255,  0.1)';
                        break;

                    default:
                        $backgrounColor = 'rgba(0, 255, 0,  0.2)';
                        $borderColor = 'rgba(0, 255, 0,  0.1)';
                        break;
                }
            }

            $datasets[] = [
                'label' => $label,
                'data' => $quantities,
                'backgroundColor' => $backgrounColor,
                'borderColor' => $borderColor,
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