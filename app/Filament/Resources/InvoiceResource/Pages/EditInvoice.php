<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Enums\InvoiceState;
use App\Filament\Resources\InvoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInvoice extends EditRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('view_pdf')
                ->label('Ver PDF')
                ->icon('heroicon-o-eye')
                ->color('info')
                ->url(fn() => route('invoices.pdf', $this->record))
                ->openUrlInNewTab(),

            Actions\Action::make('download_pdf')
                ->label('Descargar PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->url(fn() => route('invoices.download', $this->record))
                ->openUrlInNewTab(),

            Actions\Action::make('mark_paid')
                ->label('Marcar como Pagada')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->action(function () {
                    $this->record->update([
                        'state' => InvoiceState::Paid,
                        'paid_at' => now(),
                    ]);

                    $this->redirect($this->getResource()::getUrl('edit', ['record' => $this->record]));
                })
                ->requiresConfirmation()
                ->visible(fn() => $this->record->state !== InvoiceState::Paid),

            Actions\Action::make('mark_sent')
                ->label('Marcar como Enviada')
                ->icon('heroicon-o-paper-airplane')
                ->color('warning')
                ->action(function () {
                    $this->record->update([
                        'state' => InvoiceState::Sent,
                    ]);

                    $this->redirect($this->getResource()::getUrl('edit', ['record' => $this->record]));
                })
                ->requiresConfirmation()
                ->visible(fn() => $this->record->state === InvoiceState::Draft),

            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Actualizar información del comprador desde el cliente seleccionado si cambió
        if (!empty($data['buyer_id']) && $data['buyer_id'] !== $this->record->buyer_id) {
            $client = \App\Models\Client::find($data['buyer_id']);
            if ($client) {
                $data['buyer_information'] = [
                    'company' => $client->company ?? null,
                    'name' => $client->name,
                    'email' => $client->email,
                    'phone' => $client->phone,
                    'address' => [
                        'street' => $client->address,
                        'city' => $client->city ?? null,
                        'postal_code' => $client->postal_code ?? null,
                        'state' => $client->state ?? null,
                        'country' => 'Colombia',
                    ],
                    'fields' => [
                        'Documento' => $client->document_number,
                        'Tipo de Cliente' => $client->client_type ?? 'Natural',
                    ],
                ];
            }
        }

        return $data;
    }
}
