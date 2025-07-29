@php
    $color = data_get($invoice->templateData, 'color', '#1e40af');
    $font = data_get($invoice->templateData, 'font', 'Helvetica');
@endphp

<style type="text/css">
    @page {
        margin: 48px 48px 56px 48px;
    }

    body {
        line-height: 1.5;
        margin: 0;
        color: #374151;
        background-color: #fff;
        text-align: left;
        font-feature-settings: normal;
        font-variation-settings: normal;
        font-family: {{ $font }}, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif !important;
    }

    * {
        font-family: {{ $font }}, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif !important;
    }

    h1,
    h2,
    h3,
    h4,
    h5,
    h6 {
        font-size: inherit;
        font-weight: inherit;
    }

    b,
    strong {
        font-weight: bolder;
    }

    small {
        font-size: 80%;
    }

    sub,
    sup {
        font-size: 75%;
        line-height: 0;
        position: relative;
        vertical-align: baseline;
    }

    sub {
        bottom: -0.25em;
    }

    sup {
        top: -0.5em;
    }

    img {
        display: block;
        vertical-align: middle;
        border-style: none;
    }

    table {
        text-indent: 0;
        border-color: inherit;
        border-collapse: collapse;
    }

    blockquote,
    dl,
    dd,
    h1,
    h2,
    h3,
    h4,
    h5,
    h6,
    hr,
    figure,
    p,
    pre {
        margin: 0;
    }

    img,
    svg,
    video,
    canvas,
    audio,
    iframe,
    embed,
    object {
        display: block;
        vertical-align: middle;
    }

    img,
    video {
        max-width: 100%;
        height: auto;
    }

    a {
        color: #1d4ed8;
        text-decoration: none,
    }

    .font-normal {
        font-weight: normal;
    }

    .text-xs {
        font-size: 12px;
        line-height: 16px;
    }

    .text-sm {
        font-size: 14px;
        line-height: 20px;
    }

    .text-2xl {
        font-size: 24px;
        line-height: 32px;
    }

    .text-3xl {
        font-size: 30px;
        line-height: 36px;
    }

    .text-right {
        text-align: right;
    }

    .text-left {
        text-align: left;
    }

    .align-top {
        vertical-align: top;
    }

    .whitespace-nowrap {
        white-space: nowrap;
    }

    .whitespace-pre-line {
        white-space: pre-line;
    }

    .w-full {
        width: 100%;
    }

    .h-2 {
        height: 8px;
    }

    .h-3 {
        height: 12px;
    }

    .m-12 {
        margin: 48px;
    }

    .mx-12 {
        margin-left: 48px;
        margin-right: 48px;
    }

    .mb-12 {
        margin-bottom: 48px;
    }

    .mt-12 {
        margin-top: 48px;
    }

    .-ml-12 {
        margin-left: -48px;
    }

    .-mr-12 {
        margin-right: -48px;
    }

    .p-0 {
        padding: 0;
    }

    .pr-0,
    .px-0 {
        padding-right: 0;
    }

    .pl-0,
    .px-0 {
        padding-left: 0;
    }

    .py-0.5,
    .pt-0.5 {
        padding-top: 2px;
    }

    .py-0.5,
    .pb-0.5 {
        padding-bottom: 2px;
    }

    .pb-1 {
        padding-bottom: 4px;
    }

    .pr-1 {
        padding-right: 4px;
    }

    .p-1 {
        padding: 4px;
    }

    .py-1,
    .pt-1 {
        padding-top: 4px;
    }

    .pr-2,
    .p-2 {
        padding-right: 0.5rem;
    }

    .pl-2,
    .p-2 {
        padding-left: 0.5rem;
    }

    .py-2,
    .pb-2,
    .p-2 {
        padding-bottom: 0.5rem;
    }

    .py-2,
    .pt-2,
    .p-2 {
        padding-top: 0.5rem;
    }

    .pt-5,
    .p-5 {
        padding-top: 1.25rem;
    }

    .pb-5,
    .p-5 {
        padding-bottom: 1.25rem;
    }

    .pl-5,
    .p-5 {
        padding-left: 1.25rem;
    }

    .pr-5,
    .p-5 {
        padding-right: 1.25rem;
    }

    .pt-6,
    .py-6,
    .p-6 {
        padding-top: 1.5rem;
    }

    .pb-6,
    .py-6,
    .p-6 {
        padding-bottom: 1.5rem;
    }

    .pl-6,
    .px-6,
    .p-6 {
        padding-left: 1.5rem;
    }

    .pr-6,
    .px-6,
    .p-6 {
        padding-right: 1.5rem;
    }

    .px-12,
    .pl-12,
    .p-12 {
        padding-left: 48px;
    }

    .px-12,
    .pr-12,
    .p-12 {
        padding-right: 48px;
    }

    .mb-1 {
        margin-bottom: 4px;
    }

    .mt-1 {
        margin-top: 4px;
    }

    .mb-2 {
        margin-bottom: 8px;
    }

    .mb-3 {
        margin-bottom: 0.75rem;
    }

    .mt-3 {
        margin-top: 0.75rem;
    }

    .mb-5 {
        margin-bottom: 1.5rem;
    }

    .mt-5 {
        margin-top: 1.5rem;
    }

    .mb-6 {
        margin-bottom: 24px;
    }

    .mb-8 {
        margin-bottom: 32px;
    }

    .border-b {
        border-bottom: 1px solid #e5e7eb;
    }

    .fixed {
        position: fixed;
    }

    .-left-12 {
        left: -48px;
    }

    .-right-12 {
        right: -48px;
    }

    .-top-12 {
        top: -48px;
    }

    .-bottom-12 {
        bottom: -48px;
    }

    .-bottom-14 {
        bottom: -56px;
    }

    .left-0 {
        left: 0;
    }

    .right-0 {
        right: 0;
    }

    .bottom-0 {
        bottom: 0;
    }

    .top-0 {
        top: 0;
    }

    .min-w-28 {
        min-width: 7rem;
    }

    .w-28 {
        width: 7rem;
    }

    .text-gray-500 {
        color: #6b7280;
    }

    .text-gray-600 {
        color: #4b5563;
    }

    .text-gray-700 {
        color: #374151;
    }

    .text-gray-800 {
        color: #1f2937;
    }

    .text-gray-900 {
        color: #111827;
    }

    .text-red-600 {
        color: #dc2626;
    }

    .text-gray-400 {
        color: #9ca3af;
    }

    .bg-white {
        background-color: #fff;
    }

    .bg-gray-50 {
        background-color: #f9fafb;
    }

    .bg-gray-100 {
        background-color: #f3f4f6;
    }

    .bg-zinc-50 {
        background-color: #fafafa
    }

    .bg-zinc-100 {
        background-color: #f4f4f5;
    }

    .border-gray-100 {
        border-color: #f3f4f6;
    }

    .border-gray-200 {
        border-color: #e5e7eb;
    }

    .border-gray-300 {
        border-color: #d1d5db;
    }

    .divide-y > * + * {
        border-top-width: 1px;
        border-color: #e5e7eb;
    }

    .divide-gray-100 > * + * {
        border-top-color: #f3f4f6;
    }

    .divide-gray-200 > * + * {
        border-top-color: #e5e7eb;
    }

    .rounded-lg {
        border-radius: 0.5rem;
    }

    .rounded-full {
        border-radius: 9999px;
    }

    .shadow-sm {
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }

    .border {
        border-width: 1px;
        border-color: #e5e7eb;
    }

    .border-l-4 {
        border-left-width: 4px;
    }

    .border-t {
        border-top-width: 1px;
        border-color: #e5e7eb;
    }

    .border-t-2 {
        border-top-width: 2px;
    }

    .overflow-hidden {
        overflow: hidden;
    }

    .overflow-x-auto {
        overflow-x: auto;
    }

    .font-semibold {
        font-weight: 600;
    }

    .font-bold {
        font-weight: 700;
    }

    .font-medium {
        font-weight: 500;
    }

    .text-lg {
        font-size: 1.125rem;
        line-height: 1.75rem;
    }

    .flex {
        display: flex;
    }

    .items-center {
        align-items: center;
    }

    .items-start {
        align-items: flex-start;
    }

    .justify-between {
        justify-content: space-between;
    }

    .flex-1 {
        flex: 1 1 0%;
    }

    .flex-shrink-0 {
        flex-shrink: 0;
    }

    .grid {
        display: grid;
    }

    .grid-cols-1 {
        grid-template-columns: repeat(1, minmax(0, 1fr));
    }

    .grid-cols-2 {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .grid-cols-3 {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .gap-4 {
        gap: 1rem;
    }

    .gap-6 {
        gap: 1.5rem;
    }

    .space-y-2 > * + * {
        margin-top: 0.5rem;
    }

    .space-y-3 > * + * {
        margin-top: 0.75rem;
    }

    .max-w-md {
        max-width: 28rem;
    }

    .max-w-none {
        max-width: none;
    }

    .ml-auto {
        margin-left: auto;
    }

    .ml-2 {
        margin-left: 0.5rem;
    }

    .ml-4 {
        margin-left: 1rem;
    }

    .ml-6 {
        margin-left: 1.5rem;
    }

    .mr-2 {
        margin-right: 0.5rem;
    }

    .mr-6 {
        margin-right: 1.5rem;
    }

    .h-4 {
        height: 1rem;
    }

    .h-5 {
        height: 1.25rem;
    }

    .h-20 {
        height: 5rem;
    }

    .h-32 {
        height: 8rem;
    }

    .w-4 {
        width: 1rem;
    }

    .w-5 {
        width: 1.25rem;
    }

    .w-auto {
        width: auto;
    }

    .w-32 {
        width: 8rem;
    }

    .px-2\.5 {
        padding-left: 0.625rem;
        padding-right: 0.625rem;
    }

    .px-3 {
        padding-left: 0.75rem;
        padding-right: 0.75rem;
    }

    .px-4 {
        padding-left: 1rem;
        padding-right: 1rem;
    }

    .py-1 {
        padding-top: 0.25rem;
        padding-bottom: 0.25rem;
    }

    .py-3 {
        padding-top: 0.75rem;
        padding-bottom: 0.75rem;
    }

    .py-4 {
        padding-top: 1rem;
        padding-bottom: 1rem;
    }

    .py-0\.5 {
        padding-top: 0.125rem;
        padding-bottom: 0.125rem;
    }

    .text-center {
        text-align: center;
    }

    .inline-block {
        display: inline-block;
    }

    .inline-flex {
        display: inline-flex;
    }

    .prose {
        color: #374151;
        max-width: 65ch;
    }

    .prose-sm {
        font-size: 0.875rem;
        line-height: 1.7142857;
    }

    .transition-colors {
        transition-property: color, background-color, border-color, text-decoration-color, fill, stroke;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 150ms;
    }

    .duration-150 {
        transition-duration: 150ms;
    }

    .hover\:bg-gray-50:hover {
        background-color: #f9fafb;
    }

    @media (min-width: 768px) {
        .md\:grid-cols-2 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .md\:grid-cols-3 {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    @media (min-width: 1024px) {
        .lg\:grid-cols-3 {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    .dompdf-page:after {
        content: counter(page);
    }
</style>
