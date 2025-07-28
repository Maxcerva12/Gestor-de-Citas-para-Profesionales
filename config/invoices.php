<?php

declare(strict_types=1);

use Elegantly\Invoices\Enums\InvoiceType;
use Elegantly\Invoices\InvoiceDiscount;
use Elegantly\Invoices\Models\Invoice;
use Elegantly\Invoices\Models\InvoiceItem;

return [

    'model_invoice' => \App\Models\Invoice::class,
    'model_invoice_item' => \App\Models\InvoiceItem::class,

    'discount_class' => InvoiceDiscount::class,

    'cascade_invoice_delete_to_invoice_items' => true,

    'serial_number' => [
        /**
         * If true, will generate a serial number on creation
         * If false, you will have to set the serial_number yourself
         */
        'auto_generate' => true,

        /**
         * Define the serial number format used for each invoice type
         *
         * P: Prefix
         * S: Serie
         * M: Month
         * Y: Year
         * C: Count
         * Example: IN0012-220234
         * Repeat letter to set the length of each information
         * Examples of formats:
         * - PPYYCCCC : IN220123 (default)
         * - PPPYYCCCC : INV220123
         * - PPSSSS-YYCCCC : INV0001-220123
         * - SSSS-CCCC: 0001-0123
         * - YYCCCC: 220123
         */
        'format' => 'PPYYCCCC',

        /**
         * Define the default prefix used for each invoice type
         */
        'prefix' => [
            'invoice' => 'FAC',
            'quote' => 'COT',
            'credit' => 'NC',
            'proforma' => 'PF',
        ],

    ],

    'date_format' => 'd/m/Y',

    'default_seller' => [
        'company' => 'Mi Empresa Colombia',
        'name' => null,
        'address' => [
            'street' => 'Carrera 7 #32-16',
            'city' => 'Bogotá',
            'postal_code' => '110311',
            'state' => 'Cundinamarca',
            'country' => 'Colombia',
        ],
        'email' => 'contacto@miempresa.com.co',
        'phone' => '+57 (1) 234-5678',
        'tax_number' => '900.123.456-7', // NIT
        'fields' => [
            'Régimen' => 'Común',
            'Actividad Económica' => 'Servicios Profesionales',
        ],
    ],

    /**
     * ISO 4217 currency code
     */
    'default_currency' => 'COP',

    'pdf' => [

        'paper' => [
            'size' => 'a4',
            'orientation' => 'portrait',
        ],

        /**
         * Default DOM PDF options
         *
         * @see Available options https://github.com/barryvdh/laravel-dompdf#configuration
         */
        'options' => [
            'isRemoteEnabled' => true,
            'isPhpEnabled' => false,
            'fontHeightRatio' => 1,
            /**
             * Supported values are: 'DejaVu Sans', 'Helvetica', 'Courier', 'Times', 'Symbol', 'ZapfDingbats'
             */
            'defaultFont' => 'Helvetica',

            'fontDir' => storage_path('fonts'), // advised by dompdf (https://github.com/dompdf/dompdf/pull/782)
            'fontCache' => storage_path('fonts'),
            'tempDir' => sys_get_temp_dir(),
            'chroot' => realpath(base_path()),
        ],

        /**
         * The logo displayed in the PDF
         */
        'logo' => null,

        /**
         * The template used to render the PDF
         */
        'template' => 'colombia.layout',

        'template_data' => [
            /**
             * The color displayed at the top of the PDF
             */
            'color' => '#050038',
        ],

    ],

];
