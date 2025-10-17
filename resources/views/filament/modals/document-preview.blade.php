<div class="p-4">
    @if($record->is_image)
        <div class="flex justify-center">
            <img src="{{ Storage::url($record->archivo_path) }}" 
                 alt="{{ $record->nombre_documento }}" 
                 class="max-w-full h-auto rounded-lg shadow-lg">
        </div>
    @elseif(str_contains($record->archivo_mime_type, 'pdf'))
        <div class="w-full h-[600px]">
            <iframe src="{{ Storage::url($record->archivo_path) }}" 
                    class="w-full h-full border-0 rounded-lg"
                    title="{{ $record->nombre_documento }}">
            </iframe>
        </div>
    @else
        <div class="text-center py-8">
            <x-heroicon-o-document class="w-16 h-16 mx-auto text-gray-400 mb-4"/>
            <p class="text-gray-600">Vista previa no disponible para este tipo de archivo.</p>
            <p class="text-sm text-gray-500 mt-2">{{ $record->archivo_nombre_original }}</p>
        </div>
    @endif
    
    <div class="mt-6 border-t pt-4">
        <dl class="grid grid-cols-2 gap-4">
            <div>
                <dt class="text-sm font-medium text-gray-500">Tipo de Documento</dt>
                <dd class="text-sm text-gray-900">{{ App\Models\ClinicalDocument::TIPOS_DOCUMENTO[$record->tipo_documento] ?? $record->tipo_documento }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Fecha</dt>
                <dd class="text-sm text-gray-900">{{ $record->fecha_documento->format('d/m/Y') }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Tamaño</dt>
                <dd class="text-sm text-gray-900">{{ $record->file_size_formatted }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Subido por</dt>
                <dd class="text-sm text-gray-900">{{ $record->uploader?->name ?? 'N/A' }}</dd>
            </div>
            @if($record->descripcion)
            <div class="col-span-2">
                <dt class="text-sm font-medium text-gray-500">Descripción</dt>
                <dd class="text-sm text-gray-900">{{ $record->descripcion }}</dd>
            </div>
            @endif
        </dl>
    </div>
</div>
