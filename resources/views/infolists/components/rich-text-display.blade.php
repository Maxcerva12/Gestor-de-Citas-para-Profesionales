<div class="rich-text-display">
    @if(!empty($getState()))
        <div class="prose prose-sm max-w-none dark:prose-invert">
            {!! $getState() !!}
        </div>
    @else
        <div class="text-gray-500 dark:text-gray-400 italic">
            No hay notas registradas
        </div>
    @endif
</div>

<style>
/* Estilos específicos para el contenido del RichEditor */
.rich-text-display .prose {
    color: inherit;
}

.rich-text-display .prose h1 {
    font-size: 1.5rem;
    font-weight: 700;
    margin-top: 1rem;
    margin-bottom: 0.5rem;
    color: inherit;
}

.rich-text-display .prose h2 {
    font-size: 1.25rem;
    font-weight: 600;
    margin-top: 1rem;
    margin-bottom: 0.5rem;
    color: inherit;
}

.rich-text-display .prose h3 {
    font-size: 1.125rem;
    font-weight: 600;
    margin-top: 0.75rem;
    margin-bottom: 0.5rem;
    color: inherit;
}

.rich-text-display .prose p {
    margin-top: 0.5rem;
    margin-bottom: 0.5rem;
    line-height: 1.6;
    color: inherit;
}

.rich-text-display .prose strong {
    font-weight: 700;
    color: inherit;
}

.rich-text-display .prose em {
    font-style: italic;
    color: inherit;
}

.rich-text-display .prose u {
    text-decoration: underline;
    color: inherit;
}

.rich-text-display .prose del,
.rich-text-display .prose s {
    text-decoration: line-through;
    color: inherit;
}

.rich-text-display .prose a {
    color: #3b82f6;
    text-decoration: underline;
}

.rich-text-display .prose a:hover {
    color: #1d4ed8;
}

.rich-text-display .prose ul {
    list-style-type: disc;
    margin-left: 1.5rem;
    margin-top: 0.5rem;
    margin-bottom: 0.5rem;
}

.rich-text-display .prose ol {
    list-style-type: decimal;
    margin-left: 1.5rem;
    margin-top: 0.5rem;
    margin-bottom: 0.5rem;
}

.rich-text-display .prose li {
    margin-top: 0.25rem;
    margin-bottom: 0.25rem;
    color: inherit;
}

.rich-text-display .prose blockquote {
    border-left: 4px solid #e5e7eb;
    padding-left: 1rem;
    margin-left: 0;
    font-style: italic;
    color: #6b7280;
}

.rich-text-display .prose code {
    background-color: #f3f4f6;
    padding: 0.125rem 0.25rem;
    border-radius: 0.25rem;
    font-size: 0.875rem;
    font-family: ui-monospace, SFMono-Regular, "SF Mono", Consolas, "Liberation Mono", Menlo, monospace;
}

.rich-text-display .prose pre {
    background-color: #1f2937;
    color: #f9fafb;
    padding: 1rem;
    border-radius: 0.5rem;
    overflow-x: auto;
    margin-top: 0.5rem;
    margin-bottom: 0.5rem;
}

.rich-text-display .prose pre code {
    background-color: transparent;
    padding: 0;
    color: inherit;
}

/* Estilos para modo oscuro */
.dark .rich-text-display .prose blockquote {
    border-left-color: #374151;
    color: #9ca3af;
}

.dark .rich-text-display .prose code {
    background-color: #374151;
    color: #f9fafb;
}

.dark .rich-text-display .prose a {
    color: #60a5fa;
}

.dark .rich-text-display .prose a:hover {
    color: #3b82f6;
}
</style>
