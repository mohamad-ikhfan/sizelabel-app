<?php

namespace App\Jobs;

use App\Exports\LoadplanExport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Filament\Notifications;

class LoadplanExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $receipent;

    public function __construct($receipent)
    {
        $this->receipent = $receipent;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $fileName = 'Loadplan_export_' . now()->format('d-m-Y') . '-' . time() . '.xlsx';
        try {
            (new LoadplanExport)->store(filePath: "exports/$fileName", disk: 'public');

            Notifications\Notification::make()
                ->success()
                ->title('Export loadplan completed successfully.')
                ->body('Click the link below to download.')
                ->actions([
                    Notifications\Actions\Action::make('download')
                        ->color('primary')
                        ->link()
                        ->url(route('download', $fileName), shouldOpenInNewTab: true)
                        ->close(false),
                ])
                ->sendToDatabase($this->receipent);
        } catch (\Exception $e) {
            Notifications\Notification::make()
                ->danger()
                ->title('Export loadplan failed.')
                ->body($e->getMessage())
                ->sendToDatabase($this->receipent);
        }
    }
}