@php
    $isEmitter = $isEmitter ?? false;
@endphp

@if ($party->company && $party->name)
    <p class="pb-1 text-sm font-bold">{{ $party->company }}</p>
    <p class="pb-1 text-sm">{{ $party->name }}</p>
@elseif($party->company)
    <p class="pb-1 text-sm font-bold">{{ $party->company }}</p>
@elseif ($party->name)
    <p class="pb-1 text-sm font-bold">{{ $party->name }}</p>
@endif

@if ($party->tax_number)
    <p class="pb-1 text-xs"><strong>{{ $isEmitter ? 'NIT:' : 'Tipo y No. de Identificación:' }}</strong> {{ $party->tax_number }}</p>
@endif

@if ($party->address)
    @include('invoices::default.includes.address', [
        'address' => $party->address,
    ])
@endif

@if ($party->phone)
    <p class="pb-1 text-xs"><strong>Teléfono:</strong> {{ $party->phone }}</p>
@endif

@if ($party->email)
    <p class="pb-1 text-xs"><strong>Correo Electrónico:</strong> {{ $party->email }}</p>
@endif

@if ($party->fields)
    @foreach ($party->fields as $key => $value)
        @if (!empty($value))
            <p class="pb-1 text-xs">
                <strong>
                    @if (is_string($key))
                        {{ $key }}:
                    @endif
                </strong>
                {{ $value }}
            </p>
        @endif
    @endforeach
@endif
