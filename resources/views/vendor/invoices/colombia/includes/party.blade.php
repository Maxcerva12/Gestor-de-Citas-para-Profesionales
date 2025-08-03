@php
    $isEmitter = $isEmitter ?? false;
@endphp

<div class="text-xs">
    @if ($party->company)
        <p class="font-bold text-sm">{{ $party->company }}</p>
    @endif

    @if ($party->name)
        <p class="font-semibold text-sm mb-1">{{ $party->name }}</p>
    @endif

    @if ($party->address)
        <div class="mt-2">
            @if ($party->address->street)
                @if (is_array($party->address->street))
                    @foreach ($party->address->street as $line)
                        <p><strong>Dirección:</strong> {{ $line }}</p>
                    @endforeach
                @else
                    <p><strong>Dirección:</strong> {{ $party->address->street }}</p>
                @endif
            @endif

            @if ($party->address->city || $party->address->state)
                <p><strong>Ciudad:</strong> 
                    {{ $party->address->city }}
                    @if ($party->address->state), {{ $party->address->state }}@endif
                </p>
            @endif

            @if ($party->address->postal_code)
                <p><strong>CP:</strong> {{ $party->address->postal_code }}</p>
            @endif

            @if ($party->address->country)
                <p><strong>País:</strong> {{ $party->address->country }}</p>
            @endif
        </div>
    @endif

    @if ($party->email)
        <p class="mt-1"><strong>Email:</strong> {{ $party->email }}</p>
    @endif

    @if ($party->phone)
        <p><strong>Teléfono:</strong> {{ $party->phone }}</p>
    @endif

    @if ($party->tax_number)
        <p><strong>{{ $isEmitter ? 'NIT:' : 'Documento:' }}</strong> {{ $party->tax_number }}</p>
    @endif

    @if ($party->fields && count($party->fields) > 0)
        <div class="mt-2">
            @foreach ($party->fields as $key => $value)
                @if (!empty($value))
                    <p><strong>{{ $key }}:</strong> {{ $value }}</p>
                @endif
            @endforeach
        </div>
    @endif
</div>
