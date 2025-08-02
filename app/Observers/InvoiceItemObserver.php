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
     * Handle the InvoiceItem "updating" event.
     */
    public function updating(InvoiceItem $invoiceItem): void
    {
        // Asegurar que siempre use COP como moneda
        $invoiceItem->currency = 'COP';
    }
}
