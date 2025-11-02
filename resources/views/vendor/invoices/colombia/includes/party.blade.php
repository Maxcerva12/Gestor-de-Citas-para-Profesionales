@php
    $isEmitter = $isEmitter ?? false;
@endphp

<div class="text-xs leading-relaxed">
    {{-- Nombre o Razón Social --}}
    @if ($party->company)
        <p class="font-bold text-sm mb-1">{{ $party->company }}</p>
    @elseif ($party->name)
        <p class="font-bold text-sm mb-1">{{ $party->name }}</p>
    @endif

    {{-- NIT o Tipo y Número de Identificación --}}
    @if ($isEmitter)
        {{-- Para el Emisor: Solo NIT --}}
        @if ($party->tax_number)
            <p class="mb-1"><strong>NIT:</strong> {{ $party->tax_number }}</p>
        @endif
    @else
        {{-- Para el Adquiriente: Tipo y Número de Documento separados --}}
        @php
            $tipoDocumento = $party->fields['Tipo de Documento'] ?? 'CC';
            $numeroDocumento = $party->tax_number ?? ($party->fields['Número de Documento'] ?? ($party->fields['Número de Identificación'] ?? 'N/A'));
        @endphp
        <p class="mb-1"><strong>Tipo de Documento:</strong> {{ $tipoDocumento }}</p>
        <p class="mb-1"><strong>Número de Documento:</strong> {{ $numeroDocumento }}</p>
    @endif

    {{-- Dirección Completa --}}
    @if ($party->address && $party->address->street)
        <p class="mb-1"><strong>Dirección:</strong> 
            @if (is_array($party->address->street))
                {{ implode(', ', $party->address->street) }}
            @else
                {{ $party->address->street }}
            @endif
        </p>
    @endif

    {{-- Teléfono --}}
    @if ($party->phone)
        <p class="mb-1"><strong>Teléfono:</strong> {{ $party->phone }}</p>
    @endif
    
    {{-- Correo Electrónico --}}
    @if ($party->email)
        <p class="mb-1"><strong>Correo Electrónico:</strong> {{ $party->email }}</p>
    @endif

    {{-- Responsabilidades Fiscales (solo para Emisor) --}}
    @if ($isEmitter && $party->fields)
        @if (isset($party->fields['Régimen']))
            <p class="mb-1"><strong>Régimen Tributario:</strong> {{ $party->fields['Régimen'] }}</p>
        @endif
        @if (isset($party->fields['Responsabilidades Fiscales']))
            <p class="mb-1"><strong>Responsabilidades Fiscales:</strong> {{ $party->fields['Responsabilidades Fiscales'] }}</p>
        @endif
    @endif
</div>
