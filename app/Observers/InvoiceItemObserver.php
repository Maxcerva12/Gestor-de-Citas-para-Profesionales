<?php

namespace App\Observers;

use App\Models\InvoiceItem;

class InvoiceItemObserver
{
    /**
     * Handle the InvoiceItem "creating" event.
     */
    public function creating(InvoiceItem $invoiceItem): void
    {
        // Asegurar que siempre use COP como moneda
        $invoiceItem->currency = 'COP';
    }

    /**
     * Handle the InvoiceItem "created" event.
     */
    public function created(InvoiceItem $invoiceItem): void
    {
        // Denormalizar la factura después de crear un item
        if ($invoiceItem->invoice) {
            $this->denormalizeInvoice($invoiceItem->invoice);
        }
    }

    /**
     * Handle the InvoiceItem "updating" event.
     */
    public function updating(InvoiceItem $invoiceItem): void
    {
        // Asegurar que siempre use COP como moneda
        $invoiceItem->currency = 'COP';
    }

    /**
     * Handle the InvoiceItem "updated" event.
     */
    public function updated(InvoiceItem $invoiceItem): void
    {
        // Denormalizar la factura después de actualizar un item
        if ($invoiceItem->invoice) {
            $this->denormalizeInvoice($invoiceItem->invoice);
        }
    }

    /**
     * Handle the InvoiceItem "deleted" event.
     */
    public function deleted(InvoiceItem $invoiceItem): void
    {
        // Denormalizar la factura después de eliminar un item
        if ($invoiceItem->invoice) {
            $this->denormalizeInvoice($invoiceItem->invoice);
        }
    }

    /**
     * Denormalizar factura de forma segura sin disparar eventos
     */
    private function denormalizeInvoice($invoice): void
    {
        // Usar una bandera para evitar loops infinitos
        if (!property_exists($invoice, '_denormalizing') || !$invoice->_denormalizing) {
            $invoice->_denormalizing = true;
            $invoice->denormalize();
            unset($invoice->_denormalizing);
        }
    }
}
