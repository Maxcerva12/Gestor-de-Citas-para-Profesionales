<?php

namespace App\Enums;

use Elegantly\Invoices\Contracts\HasLabel;

enum InvoiceType: string implements HasLabel
{
    case Invoice = 'invoice';
    case Quote = 'quote';
    case Credit = 'credit';
    case Proforma = 'proforma';

    public function getLabel(): string
    {
        return match ($this) {
            self::Invoice => 'Factura',
            self::Quote => 'Cotización',
            self::Credit => 'Nota Crédito',
            self::Proforma => 'Factura Proforma',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Invoice => 'success',
            self::Quote => 'info',
            self::Credit => 'warning',
            self::Proforma => 'secondary',
        };
    }
}
