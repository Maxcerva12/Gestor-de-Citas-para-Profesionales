<div class="odontogram-container">
    @php
        $record = $getState(); // Obtener el record desde el state
        $odontogramData = $record->odontogram ?? ['permanent' => [], 'temporary' => []];

        $toothStatuses = [
            'healthy' => ['label' => 'Sano', 'color' => '#10B981'],
            'cavity' => ['label' => 'Caries', 'color' => '#EF4444'],
            'treated' => ['label' => 'Tratado', 'color' => '#3B82F6'],
            'missing' => ['label' => 'Ausente', 'color' => '#6B7280'],
            'implant' => ['label' => 'Implante', 'color' => '#8B5CF6'],
            'crown' => ['label' => 'Corona', 'color' => '#F59E0B'],
            'root_canal' => ['label' => 'Endodoncia', 'color' => '#EC4899'],
        ];
    @endphp

    <!-- Header -->
    <div class="odontogram-header">
        <h3 class="odontogram-title">Odontograma Digital Profesional</h3>
        <p class="odontogram-subtitle">Sistema de notación FDI - Haz clic en los dientes para cambiar su estado</p>

        <div class="odontogram-controls">
            <button type="button"
                    class="odontogram-btn"
                    onclick="exportOdontogramData()">
                📋 Exportar JSON
            </button>
        </div>
    </div>

    <!-- Legend -->
    <div class="odontogram-legend">
        <h4 class="legend-title">Leyenda de Estados Dentales</h4>
        <div class="legend-grid">
            @foreach($toothStatuses as $status => $config)
                <div class="legend-item">
                    <div class="legend-color" style="background-color: {{ $config['color'] }}"></div>
                    <span class="legend-label">{{ $config['label'] }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Permanent Teeth Section -->
    <div class="dentition-section">
        <div class="dentition-header">
            <h4 class="dentition-title">Dentición Permanente (32 dientes)</h4>
            <span class="dentition-info">Sistema FDI</span>
        </div>

        <div class="odontogram-chart">
            <svg class="odontogram-svg" viewBox="0 0 600 280" xmlns="http://www.w3.org/2000/svg">
                <!-- Upper jaw -->
                <g class="upper-jaw">
                    <!-- Quadrant 1 (Upper Right) -->
                    <g class="quadrant-1" transform="translate(300, 55)">
                        <text x="175" y="-15" text-anchor="middle" class="quadrant-label">Cuadrante 1</text>
                        @for ($i = 11; $i <= 18; $i++)
                            @php
                                $toothData = $odontogramData['permanent'][$i] ?? null;
                                $status = $toothData['status'] ?? 'healthy';
                                $color = $toothStatuses[$status]['color'] ?? '#10B981';
                                $label = $toothStatuses[$status]['label'] ?? 'Sano';
                            @endphp
                            <g class="tooth-group" transform="translate({{ ($i - 11) * 35 }}, 0)">
                                <rect x="-12" y="0" width="24" height="25" rx="4"
                                      class="tooth-rect"
                                      fill="{{ $color }}"
                                      stroke="#e5e7eb" stroke-width="1"/>
                                <text x="0" y="17" text-anchor="middle" class="tooth-number">{{ $i }}</text>
                                <text x="0" y="40" text-anchor="middle" class="tooth-status">{{ $label }}</text>
                            </g>
                        @endfor
                    </g>

                    <!-- Quadrant 2 (Upper Left) -->
                    <g class="quadrant-2" transform="translate(300, 55)">
                        <text x="-175" y="-15" text-anchor="middle" class="quadrant-label">Cuadrante 2</text>
                        @for ($i = 21; $i <= 28; $i++)
                            @php
                                $toothData = $odontogramData['permanent'][$i] ?? null;
                                $status = $toothData['status'] ?? 'healthy';
                                $color = $toothStatuses[$status]['color'] ?? '#10B981';
                                $label = $toothStatuses[$status]['label'] ?? 'Sano';
                            @endphp
                            <g class="tooth-group" transform="translate({{ -($i - 21 + 1) * 35 }}, 0)">
                                <rect x="-12" y="0" width="24" height="25" rx="4"
                                      class="tooth-rect"
                                      fill="{{ $color }}"
                                      stroke="#e5e7eb" stroke-width="1"/>
                                <text x="0" y="17" text-anchor="middle" class="tooth-number">{{ $i }}</text>
                                <text x="0" y="40" text-anchor="middle" class="tooth-status">{{ $label }}</text>
                            </g>
                        @endfor
                    </g>
                </g>

                <!-- Lower jaw -->
                <g class="lower-jaw">
                    <!-- Quadrant 3 (Lower Left) -->
                    <g class="quadrant-3" transform="translate(300, 195)">
                        <text x="-175" y="45" text-anchor="middle" class="quadrant-label">Cuadrante 3</text>
                        @for ($i = 31; $i <= 38; $i++)
                            @php
                                $toothData = $odontogramData['permanent'][$i] ?? null;
                                $status = $toothData['status'] ?? 'healthy';
                                $color = $toothStatuses[$status]['color'] ?? '#10B981';
                                $label = $toothStatuses[$status]['label'] ?? 'Sano';
                            @endphp
                            <g class="tooth-group" transform="translate({{ -($i - 31 + 1) * 35 }}, 0)">
                                <rect x="-12" y="0" width="24" height="25" rx="4"
                                      class="tooth-rect"
                                      fill="{{ $color }}"
                                      stroke="#e5e7eb" stroke-width="1"/>
                                <text x="0" y="17" text-anchor="middle" class="tooth-number">{{ $i }}</text>
                                <text x="0" y="-10" text-anchor="middle" class="tooth-status">{{ $label }}</text>
                            </g>
                        @endfor
                    </g>

                    <!-- Quadrant 4 (Lower Right) -->
                    <g class="quadrant-4" transform="translate(300, 195)">
                        <text x="175" y="45" text-anchor="middle" class="quadrant-label">Cuadrante 4</text>
                        @for ($i = 41; $i <= 48; $i++)
                            @php
                                $toothData = $odontogramData['permanent'][$i] ?? null;
                                $status = $toothData['status'] ?? 'healthy';
                                $color = $toothStatuses[$status]['color'] ?? '#10B981';
                                $label = $toothStatuses[$status]['label'] ?? 'Sano';
                            @endphp
                            <g class="tooth-group" transform="translate({{ ($i - 41) * 35 }}, 0)">
                                <rect x="-12" y="0" width="24" height="25" rx="4"
                                      class="tooth-rect"
                                      fill="{{ $color }}"
                                      stroke="#e5e7eb" stroke-width="1"/>
                                <text x="0" y="17" text-anchor="middle" class="tooth-number">{{ $i }}</text>
                                <text x="0" y="-10" text-anchor="middle" class="tooth-status">{{ $label }}</text>
                            </g>
                        @endfor
                    </g>
                </g>

                <!-- Central dividing line -->
                <line x1="300" y1="40" x2="300" y2="240" class="divider-line"/>
            </svg>
        </div>
    </div>

    <!-- Temporary Teeth Section -->
    <div class="dentition-section">
        <div class="dentition-header">
            <h4 class="dentition-title">Dentición Temporal (20 dientes)</h4>
            <span class="dentition-info">Sistema FDI</span>
        </div>

        <div class="odontogram-chart">
            <svg class="odontogram-svg" viewBox="0 0 600 280" xmlns="http://www.w3.org/2000/svg">
                <!-- Upper jaw -->
                <g class="upper-jaw">
                    <!-- Quadrant 5 (Upper Right) -->
                    <g class="quadrant-5" transform="translate(300, 55)">
                        <text x="87.5" y="-15" text-anchor="middle" class="quadrant-label">Cuadrante 5</text>
                        @for ($i = 51; $i <= 55; $i++)
                            @php
                                $toothData = $odontogramData['temporary'][$i] ?? null;
                                $status = $toothData['status'] ?? 'healthy';
                                $color = $toothStatuses[$status]['color'] ?? '#10B981';
                                $label = $toothStatuses[$status]['label'] ?? 'Sano';
                            @endphp
                            <g class="tooth-group" transform="translate({{ ($i - 51) * 35 }}, 0)">
                                <rect x="-12" y="0" width="24" height="25" rx="4"
                                      class="tooth-rect"
                                      fill="{{ $color }}"
                                      stroke="#e5e7eb" stroke-width="1"/>
                                <text x="0" y="17" text-anchor="middle" class="tooth-number">{{ $i }}</text>
                                <text x="0" y="40" text-anchor="middle" class="tooth-status">{{ $label }}</text>
                            </g>
                        @endfor
                    </g>

                    <!-- Quadrant 6 (Upper Left) -->
                    <g class="quadrant-6" transform="translate(300, 55)">
                        <text x="-87.5" y="-15" text-anchor="middle" class="quadrant-label">Cuadrante 6</text>
                        @for ($i = 61; $i <= 65; $i++)
                            @php
                                $toothData = $odontogramData['temporary'][$i] ?? null;
                                $status = $toothData['status'] ?? 'healthy';
                                $color = $toothStatuses[$status]['color'] ?? '#10B981';
                                $label = $toothStatuses[$status]['label'] ?? 'Sano';
                            @endphp
                            <g class="tooth-group" transform="translate({{ -($i - 61 + 1) * 35 }}, 0)">
                                <rect x="-12" y="0" width="24" height="25" rx="4"
                                      class="tooth-rect"
                                      fill="{{ $color }}"
                                      stroke="#e5e7eb" stroke-width="1"/>
                                <text x="0" y="17" text-anchor="middle" class="tooth-number">{{ $i }}</text>
                                <text x="0" y="40" text-anchor="middle" class="tooth-status">{{ $label }}</text>
                            </g>
                        @endfor
                    </g>
                </g>

                <!-- Lower jaw -->
                <g class="lower-jaw">
                    <!-- Quadrant 7 (Lower Left) -->
                    <g class="quadrant-7" transform="translate(300, 195)">
                        <text x="-87.5" y="45" text-anchor="middle" class="quadrant-label">Cuadrante 7</text>
                        @for ($i = 71; $i <= 75; $i++)
                            @php
                                $toothData = $odontogramData['temporary'][$i] ?? null;
                                $status = $toothData['status'] ?? 'healthy';
                                $color = $toothStatuses[$status]['color'] ?? '#10B981';
                                $label = $toothStatuses[$status]['label'] ?? 'Sano';
                            @endphp
                            <g class="tooth-group" transform="translate({{ -($i - 71 + 1) * 35 }}, 0)">
                                <rect x="-12" y="0" width="24" height="25" rx="4"
                                      class="tooth-rect"
                                      fill="{{ $color }}"
                                      stroke="#e5e7eb" stroke-width="1"/>
                                <text x="0" y="17" text-anchor="middle" class="tooth-number">{{ $i }}</text>
                                <text x="0" y="-10" text-anchor="middle" class="tooth-status">{{ $label }}</text>
                            </g>
                        @endfor
                    </g>

                    <!-- Quadrant 8 (Lower Right) -->
                    <g class="quadrant-8" transform="translate(300, 195)">
                        <text x="87.5" y="45" text-anchor="middle" class="quadrant-label">Cuadrante 8</text>
                        @for ($i = 81; $i <= 85; $i++)
                            @php
                                $toothData = $odontogramData['temporary'][$i] ?? null;
                                $status = $toothData['status'] ?? 'healthy';
                                $color = $toothStatuses[$status]['color'] ?? '#10B981';
                                $label = $toothStatuses[$status]['label'] ?? 'Sano';
                            @endphp
                            <g class="tooth-group" transform="translate({{ ($i - 81) * 35 }}, 0)">
                                <rect x="-12" y="0" width="24" height="25" rx="4"
                                      class="tooth-rect"
                                      fill="{{ $color }}"
                                      stroke="#e5e7eb" stroke-width="1"/>
                                <text x="0" y="17" text-anchor="middle" class="tooth-number">{{ $i }}</text>
                                <text x="0" y="-10" text-anchor="middle" class="tooth-status">{{ $label }}</text>
                            </g>
                        @endfor
                    </g>
                </g>

                <!-- Central dividing line -->
                <line x1="300" y1="40" x2="300" y2="240" class="divider-line"/>
            </svg>
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="stats-section">
        <h4 class="stats-title">Estadísticas del Odontograma</h4>
        <div class="stats-grid">
            @foreach($toothStatuses as $status => $config)
                @php
                    $permanentCount = collect($odontogramData['permanent'] ?? [])
                        ->where('status', $status)
                        ->count();
                    $temporaryCount = collect($odontogramData['temporary'] ?? [])
                        ->where('status', $status)
                        ->count();
                    $totalCount = $permanentCount + $temporaryCount;
                @endphp
                <div class="stat-item">
                    <div class="stat-number">{{ $totalCount }}</div>
                    <div class="stat-label">{{ $config['label'] }}</div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<script>
    function exportOdontogramData() {
        const odontogramData = @json($odontogramData);
        const data = JSON.stringify(odontogramData, null, 2);
        const blob = new Blob([data], { type: 'application/json' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'odontogram_{{ $record->name }}_{{ now()->format("Y-m-d") }}.json';
        a.click();
        URL.revokeObjectURL(url);
    }
</script>
