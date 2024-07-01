<?php

namespace App\Filament\Resources\LoadplanResource\Pages;

use App\Filament\Resources\LoadplanResource;
use App\Jobs\LoadplanImportJob;
use App\Models\User;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications;
use Filament\Resources\Pages\ListRecords;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ListLoadplans extends ListRecords
{
    protected static string $resource = LoadplanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('New loadplan import')
                ->color('primary')
                ->modalWidth('lg')
                ->modalSubmitActionLabel('Import')
                ->form(function (Forms\Form $form) {
                    return $form
                        ->schema([
                            Forms\Components\FileUpload::make('file_loadplans')
                                ->hiddenLabel()
                                ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'])
                                ->multiple()
                                ->directory('imports')
                                ->getUploadedFileNameForStorageUsing(
                                    fn (TemporaryUploadedFile $file): string => (string) $file->getClientOriginalName(),
                                )
                                ->required()
                        ]);
                })
                ->action(function (array $data) {
                    $receipent = User::find(auth()->user()->id);
                    sort($data['file_loadplans']);
                    foreach ($data['file_loadplans'] as $file) {
                        LoadplanImportJob::dispatch($receipent, storage_path('app/public/' . $file), str_replace('imports/', '', $file));
                    }

                    Notifications\Notification::make()
                        ->success()
                        ->title('Import loadplan on background.')
                        ->body('After import finished notification send.')
                        ->send();
                })
                ->hidden(auth()->user()->id !== 1),
        ];
    }
}