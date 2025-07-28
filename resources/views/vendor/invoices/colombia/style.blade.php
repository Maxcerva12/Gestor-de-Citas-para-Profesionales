@php
    $color = data_get($invoice->templateData, 'color', '#1e40af');
    $font = data_get($invoice->templateData, 'font', 'Helvetica');
@endphp

<style>
    @page {
        margin: 1cm;
        @bottom-center {
            content: "Página " counter(page) " de " counter(pages);
        }
    }

    * {
        font-family: {{ $font }}, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    }

    body {
        background: white;
        font-size: 12px;
        line-height: 1.4;
        color: #374151;
    }

    .colombia-invoice {
        max-width: 100%;
        margin: 0 auto;
    }

    h1, h2, h3, h4, h5, h6 {
        font-weight: bold;
        margin: 0;
        color: {{ $color }};
    }

    p {
        margin: 0 0 0.5rem 0;
    }

    table {
        border-collapse: collapse;
        width: 100%;
    }

    .w-full { width: 100%; }
    .w-1\/2 { width: 50%; }
    .w-1\/3 { width: 33.333333%; }
    .w-1\/4 { width: 25%; }

    .text-xs { font-size: 0.75rem; }
    .text-sm { font-size: 0.875rem; }
    .text-base { font-size: 1rem; }
    .text-lg { font-size: 1.125rem; }
    .text-xl { font-size: 1.25rem; }
    .text-2xl { font-size: 1.5rem; }
    .text-3xl { font-size: 1.875rem; }

    .font-normal { font-weight: 400; }
    .font-medium { font-weight: 500; }
    .font-semibold { font-weight: 600; }
    .font-bold { font-weight: 700; }

    .text-left { text-align: left; }
    .text-center { text-align: center; }
    .text-right { text-align: right; }

    .text-gray-500 { color: #6b7280; }
    .text-gray-600 { color: #4b5563; }
    .text-gray-700 { color: #374151; }
    .text-white { color: #ffffff; }
    .text-red-600 { color: #dc2626; }

    .bg-gray-50 { background-color: #f9fafb; }
    .bg-gray-100 { background-color: #f3f4f6; }
    .bg-white { background-color: #ffffff; }
    .bg-yellow-400 { background-color: #fbbf24; }
    .bg-blue-600 { background-color: #2563eb; }
    .bg-red-600 { background-color: #dc2626; }

    .border { border: 1px solid #d1d5db; }
    .border-t { border-top: 1px solid #d1d5db; }
    .border-b { border-bottom: 1px solid #d1d5db; }
    .border-t-2 { border-top: 2px solid #9ca3af; }
    .border-gray-300 { border-color: #d1d5db; }
    .border-gray-400 { border-color: #9ca3af; }

    .p-0 { padding: 0; }
    .p-1 { padding: 0.25rem; }
    .p-2 { padding: 0.5rem; }
    .p-3 { padding: 0.75rem; }
    .px-2 { padding-left: 0.5rem; padding-right: 0.5rem; }
    .py-1 { padding-top: 0.25rem; padding-bottom: 0.25rem; }
    .py-2 { padding-top: 0.5rem; padding-bottom: 0.5rem; }

    .m-0 { margin: 0; }
    .mb-1 { margin-bottom: 0.25rem; }
    .mb-2 { margin-bottom: 0.5rem; }
    .mb-3 { margin-bottom: 0.75rem; }
    .mb-4 { margin-bottom: 1rem; }
    .mb-6 { margin-bottom: 1.5rem; }
    .mb-8 { margin-bottom: 2rem; }
    .mt-1 { margin-top: 0.25rem; }
    .mt-2 { margin-top: 0.5rem; }
    .mt-8 { margin-top: 2rem; }
    .ml-4 { margin-left: 1rem; }
    .ml-auto { margin-left: auto; }
    .mx-auto { margin-left: auto; margin-right: auto; }

    .align-top { vertical-align: top; }
    .align-middle { vertical-align: middle; }

    .whitespace-nowrap { white-space: nowrap; }

    .fixed {
        position: fixed;
    }

    .-left-12 { left: -3rem; }
    .-right-12 { right: -3rem; }
    .-top-12 { top: -3rem; }
    .-bottom-14 { bottom: -3.5rem; }

    .h-1 { height: 0.25rem; }
    .h-2 { height: 0.5rem; }

    .flex { display: flex; }
    .flex-1 { flex: 1 1 0%; }

    .dompdf-page:before {
        content: counter(page);
    }

    /* Estilos específicos para Colombia */
    .colombia-flag {
        background: linear-gradient(to bottom, #fbbf24 33%, #2563eb 33%, #2563eb 66%, #dc2626 66%);
    }

    .colombia-header {
        border-left: 4px solid {{ $color }};
        padding-left: 1rem;
    }

    /* Mejorar contraste en tablas */
    table th {
        font-weight: bold;
    }

    table td, table th {
        padding: 0.5rem;
        border: 1px solid #d1d5db;
    }

    /* Estilos de impresión */
    @media print {
        body {
            font-size: 11px;
        }
        
        .colombia-invoice {
            break-inside: avoid;
        }
        
        table {
            page-break-inside: auto;
        }
        
        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
    }
</style>
