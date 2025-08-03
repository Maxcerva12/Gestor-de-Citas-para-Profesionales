 @if ($address->company && $address->name)
    <p class="pb-1 text-xs"><strong>{{ $address->company }}</strong></p>
    <p class="pb-1 text-xs">{{ $address->name }}</p>
@elseif($address->company)
    <p class="pb-1 text-xs"><strong>{{ $address->company }}</strong></p>
@elseif ($address->name)
    <p class="pb-1 text-xs"><strong>{{ $address->name }}</strong></p>
@endif

@if (is_array($address->street))
    @foreach ($address->street as $line)
        <p class="pb-1 text-xs"><strong>Dirección:</strong> {{ $line }}</p>
    @endforeach
@elseif($address->street)
    <p class="pb-1 text-xs"><strong>Dirección:</strong> {{ $address->street }}</p>
@endif

@if ($address->city)
    <p class="pb-1 text-xs">
        <strong>Ciudad:</strong> {{ $address->city }}@if($address->state), {{ $address->state }}@endif@if($address->postal_code) {{ $address->postal_code }}@endif
    </p>
@endif

@if ($address->country)
    <p class="pb-1 text-xs"><strong>País:</strong> {{ $address->country }}</p>
@endif

@if ($address->fields)
    @foreach ($address->fields as $key => $value)
        <p class="pb-1 text-xs">
            @if (is_string($key))
                {{ $key }}
            @endif
            {{ $value }}
        </p>
    @endforeach
@endif
