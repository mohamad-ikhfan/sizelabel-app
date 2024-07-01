<?php

namespace App\Jobs;

use App\Imports\LoadplanImport;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class LoadplanImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $receipent, $file, $fileName;
    /**
     * Create a new job instance.
     */
    public function __construct(User $receipent, $file, $fileName)
    {
        $this->receipent = $receipent;
        $this->file = $file;
        $this->fileName = $fileName;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            (new LoadplanImport)->import($this->file);
            Notification::make()
                ->success()
                ->title('Imported ' . $this->fileName . '.')
                ->body('Imported successfully, please refresh page.')
                ->sendToDatabase($this->receipent);
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('Imported ' . $this->fileName . '.')
                ->body($e->getMessage())
                ->sendToDatabase($this->receipent);
        }
        unlink($this->file);
    }
}