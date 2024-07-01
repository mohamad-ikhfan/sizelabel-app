<?php

namespace App\Filament\Resources\ReportPrintResource\Pages;

use App\Filament\Resources\ReportPrintResource;
use App\Jobs\ReportPrintImportJob;
use App\Models\User;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications;
use Filament\Resources\Pages\ListRecords;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ListReportPrints extends ListRecords
{
    protected static string $resource = ReportPrintResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('New report print import')
                ->color('primary')
                ->modalWidth('lg')
                ->modalSubmitActionLabel('Import')
                ->form(function (Forms\Form $form) {
                    return $form
                        ->schema([
                            Forms\Components\FileUpload::make('file_report')
                                ->hiddenLabel()
                                ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'])
                                ->directory('imports')
                                ->getUploadedFileNameForStorageUsing(
                                    fn (TemporaryUploadedFile $file): string => (string) $file->getClientOriginalName(),
                                )
                                ->required()
                        ]);
                })
                ->action(function (array $data) {
                    $receipent = User::find(auth()->user()->id);
                    $file = $data['file_report'];
                    ReportPrintImportJob::dispatch($receipent, storage_path('app/public/' . $file), str_replace('imports/', '', $file));

                    Notifications\Notification::make()
                        ->success()
                        ->title('Import report print on background.')
                        ->body('After import finished notification send.')
                        ->send();
                })
                ->hidden(auth()->user()->id !== 1),
        ];
    }
}