<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Enums\InvoiceType;
use App\Filament\Resources\InvoiceResource;
use App\Models\Invoice;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateInvoice extends CreateRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Asignar el usuario actual
        $data['user_id'] = Auth::id();

        // Configurar información del vendedor si no está presente
        if (empty($data['seller_information'])) {
            $data['seller_information'] = config('invoices.default_seller');
        }

        // Configurar información del comprador desde el cliente seleccionado
        if (!empty($data['buyer_id'])) {
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

    protected function afterCreate(): void
    {
        $invoice = $this->record;

        // Configurar el número de serie
        $type = $invoice->type instanceof InvoiceType ? $invoice->type->value : $invoice->type;
        $prefix = config("invoices.serial_number.prefix.{$type}", 'FAC');

        $invoice->configureSerialNumber(
            prefix: $prefix,
            year: now()->format('Y'),
            month: now()->format('m')
        );

        $invoice->save();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->getRecord()]);
    }
}
