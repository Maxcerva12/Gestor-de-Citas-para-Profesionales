<?php

namespace App\Observers;

use App\Models\Invoice;
use App\Models\InvoiceSettings;

class InvoiceObserver
{
    /**
     * Handle the Invoice "creating" event.
     * Este se ejecuta ANTES de guardar en la base de datos
     */
    public function creating(Invoice $invoice): void
    {
        // Guardar el estado actual del descuento en el momento de creación
        $discountEnabled = InvoiceSettings::get('discount_enabled', 'false');
        $invoice->discount_enabled = ($discountEnabled === 'true' || $discountEnabled === true || $discountEnabled === 1);

        if ($invoice->discount_enabled) {
            $invoice->discount_percentage = (float) InvoiceSettings::get('discount_percentage', 0);
        } else {
            $invoice->discount_percentage = 0;
        }

        \Log::info('Invoice creating with discount', [
            'discount_enabled' => $invoice->discount_enabled,
            'discount_percentage' => $invoice->discount_percentage,
        ]);
    }

    /**
     * Handle the Invoice "created" event.
     */
    public function created(Invoice $invoice): void
    {
        //
    }

    /**
     * Handle the Invoice "updated" event.
     */
    public function updated(Invoice $invoice): void
    {
        //
    }

    /**
     * Handle the Invoice "deleted" event.
     */
    public function deleted(Invoice $invoice): void
    {
        //
    }

    /**
     * Handle the Invoice "restored" event.
     */
    public function restored(Invoice $invoice): void
    {
        //
    }

    /**
     * Handle the Invoice "force deleted" event.
     */
    public function forceDeleted(Invoice $invoice): void
    {
        //
    }
}
