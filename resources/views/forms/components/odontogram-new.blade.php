<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div
        x-data="odontogramComponent('{{ $getStatePath() }}')"
        x-init="initOdontogram()"
        class="odontogram-container">

        <!-- Header -->
        <div class="odontogram-header">
            <h3 class="odontogram-title">Odontograma Digital Profesional</h3>
            <p class="odontogram-subtitle">Sistema de notación FDI - Haz clic en los dientes para cambiar su estado</p>

            <div class="odontogram-controls">
                <button type="button"
                        class="odontogram-btn"
                        x-on:click="resetOdontogram()">
                    🔄 Limpiar Todo
                </button>
                <button type="button"
                        class="odontogram-btn"
                        x-on:click="exportOdontogram()">
                    📋 Exportar JSON
                </button>
            </div>
        </div>

        <!-- Legend -->
        <div class="odontogram-legend">
            <h4 class="legend-title">Leyenda de Estados Dentales</h4>
            <div class="legend-grid">
                <template x-for="(config, status) in statusConfig" :key="status">
                    <div class="legend-item">
                        <div class="legend-color" :style="`background-color: ${config.color}`"></div>
                        <span class="legend-label" x-text="config.label"></span>
                    </div>
                </template>
            </div>
        </div>

        <!-- Permanent Teeth Section -->
        <div class="dentition-section" x-show="showPermanent">
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
                                <g class="tooth-group" transform="translate({{ ($i - 11) * 35 }}, 0)">
                                    <rect x="-12" y="0" width="24" height="25" rx="4"
                                          class="tooth-rect cursor-pointer transition-all duration-300 hover:stroke-2 hover:stroke-blue-400"
                                          :fill="getToothColor({{ $i }}, 'permanent')"
                                          stroke="#e5e7eb" stroke-width="1"
                                          x-on:click="updateTooth({{ $i }}, 'permanent')"/>
                                    <text x="0" y="17" text-anchor="middle" class="tooth-number">{{ $i }}</text>
                                    <text x="0" y="40" text-anchor="middle" class="tooth-status"
                                          x-text="getToothStatus({{ $i }}, 'permanent')"></text>
                                    <!-- Tooltip -->
                                    <text id="tooltip-permanent-{{ $i }}" x="0" y="-10" text-anchor="middle"
                                          class="tooth-tooltip opacity-0"></text>
                                </g>
                            @endfor
                        </g>

                        <!-- Quadrant 2 (Upper Left) -->
                        <g class="quadrant-2" transform="translate(300, 55)">
                            <text x="-175" y="-15" text-anchor="middle" class="quadrant-label">Cuadrante 2</text>
                            @for ($i = 21; $i <= 28; $i++)
                                <g class="tooth-group" transform="translate({{ -($i - 21 + 1) * 35 }}, 0)">
                                    <rect x="-12" y="0" width="24" height="25" rx="4"
                                          class="tooth-rect cursor-pointer transition-all duration-300 hover:stroke-2 hover:stroke-blue-400"
                                          :fill="getToothColor({{ $i }}, 'permanent')"
                                          stroke="#e5e7eb" stroke-width="1"
                                          x-on:click="updateTooth({{ $i }}, 'permanent')"/>
                                    <text x="0" y="17" text-anchor="middle" class="tooth-number">{{ $i }}</text>
                                    <text x="0" y="40" text-anchor="middle" class="tooth-status"
                                          x-text="getToothStatus({{ $i }}, 'permanent')"></text>
                                    <!-- Tooltip -->
                                    <text id="tooltip-permanent-{{ $i }}" x="0" y="-10" text-anchor="middle"
                                          class="tooth-tooltip opacity-0"></text>
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
                                <g class="tooth-group" transform="translate({{ -($i - 31 + 1) * 35 }}, 0)">
                                    <rect x="-12" y="0" width="24" height="25" rx="4"
                                          class="tooth-rect cursor-pointer transition-all duration-300 hover:stroke-2 hover:stroke-blue-400"
                                          :fill="getToothColor({{ $i }}, 'permanent')"
                                          stroke="#e5e7eb" stroke-width="1"
                                          x-on:click="updateTooth({{ $i }}, 'permanent')"/>
                                    <text x="0" y="17" text-anchor="middle" class="tooth-number">{{ $i }}</text>
                                    <text x="0" y="-10" text-anchor="middle" class="tooth-status"
                                          x-text="getToothStatus({{ $i }}, 'permanent')"></text>
                                    <!-- Tooltip -->
                                    <text id="tooltip-permanent-{{ $i }}" x="0" y="40" text-anchor="middle"
                                          class="tooth-tooltip opacity-0"></text>
                                </g>
                            @endfor
                        </g>

                        <!-- Quadrant 4 (Lower Right) -->
                        <g class="quadrant-4" transform="translate(300, 195)">
                            <text x="175" y="45" text-anchor="middle" class="quadrant-label">Cuadrante 4</text>
                            @for ($i = 41; $i <= 48; $i++)
                                <g class="tooth-group" transform="translate({{ ($i - 41) * 35 }}, 0)">
                                    <rect x="-12" y="0" width="24" height="25" rx="4"
                                          class="tooth-rect cursor-pointer transition-all duration-300 hover:stroke-2 hover:stroke-blue-400"
                                          :fill="getToothColor({{ $i }}, 'permanent')"
                                          stroke="#e5e7eb" stroke-width="1"
                                          x-on:click="updateTooth({{ $i }}, 'permanent')"/>
                                    <text x="0" y="17" text-anchor="middle" class="tooth-number">{{ $i }}</text>
                                    <text x="0" y="-10" text-anchor="middle" class="tooth-status"
                                          x-text="getToothStatus({{ $i }}, 'permanent')"></text>
                                    <!-- Tooltip -->
                                    <text id="tooltip-permanent-{{ $i }}" x="0" y="40" text-anchor="middle"
                                          class="tooth-tooltip opacity-0"></text>
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
        <div class="dentition-section" x-show="showTemporary">
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
                                <g class="tooth-group" transform="translate({{ ($i - 51) * 35 }}, 0)">
                                    <rect x="-12" y="0" width="24" height="25" rx="4"
                                          class="tooth-rect cursor-pointer transition-all duration-300 hover:stroke-2 hover:stroke-blue-400"
                                          :fill="getToothColor({{ $i }}, 'temporary')"
                                          stroke="#e5e7eb" stroke-width="1"
                                          x-on:click="updateTooth({{ $i }}, 'temporary')"/>
                                    <text x="0" y="17" text-anchor="middle" class="tooth-number">{{ $i }}</text>
                                    <text x="0" y="40" text-anchor="middle" class="tooth-status"
                                          x-text="getToothStatus({{ $i }}, 'temporary')"></text>
                                    <!-- Tooltip -->
                                    <text id="tooltip-temporary-{{ $i }}" x="0" y="-10" text-anchor="middle"
                                          class="tooth-tooltip opacity-0"></text>
                                </g>
                            @endfor
                        </g>

                        <!-- Quadrant 6 (Upper Left) -->
                        <g class="quadrant-6" transform="translate(300, 55)">
                            <text x="-87.5" y="-15" text-anchor="middle" class="quadrant-label">Cuadrante 6</text>
                            @for ($i = 61; $i <= 65; $i++)
                                <g class="tooth-group" transform="translate({{ -($i - 61 + 1) * 35 }}, 0)">
                                    <rect x="-12" y="0" width="24" height="25" rx="4"
                                          class="tooth-rect cursor-pointer transition-all duration-300 hover:stroke-2 hover:stroke-blue-400"
                                          :fill="getToothColor({{ $i }}, 'temporary')"
                                          stroke="#e5e7eb" stroke-width="1"
                                          x-on:click="updateTooth({{ $i }}, 'temporary')"/>
                                    <text x="0" y="17" text-anchor="middle" class="tooth-number">{{ $i }}</text>
                                    <text x="0" y="40" text-anchor="middle" class="tooth-status"
                                          x-text="getToothStatus({{ $i }}, 'temporary')"></text>
                                    <!-- Tooltip -->
                                    <text id="tooltip-temporary-{{ $i }}" x="0" y="-10" text-anchor="middle"
                                          class="tooth-tooltip opacity-0"></text>
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
                                <g class="tooth-group" transform="translate({{ -($i - 71 + 1) * 35 }}, 0)">
                                    <rect x="-12" y="0" width="24" height="25" rx="4"
                                          class="tooth-rect cursor-pointer transition-all duration-300 hover:stroke-2 hover:stroke-blue-400"
                                          :fill="getToothColor({{ $i }}, 'temporary')"
                                          stroke="#e5e7eb" stroke-width="1"
                                          x-on:click="updateTooth({{ $i }}, 'temporary')"/>
                                    <text x="0" y="17" text-anchor="middle" class="tooth-number">{{ $i }}</text>
                                    <text x="0" y="-10" text-anchor="middle" class="tooth-status"
                                          x-text="getToothStatus({{ $i }}, 'temporary')"></text>
                                    <!-- Tooltip -->
                                    <text id="tooltip-temporary-{{ $i }}" x="0" y="40" text-anchor="middle"
                                          class="tooth-tooltip opacity-0"></text>
                                </g>
                            @endfor
                        </g>

                        <!-- Quadrant 8 (Lower Right) -->
                        <g class="quadrant-8" transform="translate(300, 195)">
                            <text x="87.5" y="45" text-anchor="middle" class="quadrant-label">Cuadrante 8</text>
                            @for ($i = 81; $i <= 85; $i++)
                                <g class="tooth-group" transform="translate({{ ($i - 81) * 35 }}, 0)">
                                    <rect x="-12" y="0" width="24" height="25" rx="4"
                                          class="tooth-rect cursor-pointer transition-all duration-300 hover:stroke-2 hover:stroke-blue-400"
                                          :fill="getToothColor({{ $i }}, 'temporary')"
                                          stroke="#e5e7eb" stroke-width="1"
                                          x-on:click="updateTooth({{ $i }}, 'temporary')"/>
                                    <text x="0" y="17" text-anchor="middle" class="tooth-number">{{ $i }}</text>
                                    <text x="0" y="-10" text-anchor="middle" class="tooth-status"
                                          x-text="getToothStatus({{ $i }}, 'temporary')"></text>
                                    <!-- Tooltip -->
                                    <text id="tooltip-temporary-{{ $i }}" x="0" y="40" text-anchor="middle"
                                          class="tooth-tooltip opacity-0"></text>
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
                <template x-for="(config, status) in statusConfig" :key="status">
                    <div class="stat-item">
                        <div class="stat-number" x-text="getStatusCount(status)"></div>
                        <div class="stat-label" x-text="config.label"></div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <script>
        function odontogramComponent(statePath) {
            return {
                odontogramState: null,
                statuses: @js(array_keys($getToothStatuses())),
                statusConfig: @js($getToothStatuses()),
                showPermanent: {{ $getShowPermanent() ? 'true' : 'false' }},
                showTemporary: {{ $getShowTemporary() ? 'true' : 'false' }},
                statePath: statePath,

                initOdontogram() {
                    // Inicializar con Livewire wire
                    this.odontogramState = this.$wire.get(this.statePath) || { permanent: {}, temporary: {} };

                    // Asegurar estructura
                    if (!this.odontogramState.permanent) {
                        this.odontogramState.permanent = {};
                    }
                    if (!this.odontogramState.temporary) {
                        this.odontogramState.temporary = {};
                    }

                    // Observar cambios
                    this.$watch('odontogramState', (value) => {
                        this.$wire.set(this.statePath, value);
                    });
                },

                updateTooth(toothNumber, dentition) {
                    // Asegurar que el estado existe
                    if (!this.odontogramState) {
                        this.odontogramState = { permanent: {}, temporary: {} };
                    }
                    if (!this.odontogramState[dentition]) {
                        this.odontogramState[dentition] = {};
                    }

                    const currentStatus = this.odontogramState[dentition][toothNumber]?.status || 'healthy';
                    const currentIndex = this.statuses.indexOf(currentStatus);
                    const nextIndex = (currentIndex + 1) % this.statuses.length;
                    const nextStatus = this.statuses[nextIndex];

                    this.odontogramState[dentition][toothNumber] = {
                        status: nextStatus,
                        notes: this.odontogramState[dentition][toothNumber]?.notes || '',
                        updatedAt: new Date().toISOString()
                    };

                    this.showToothTooltip(toothNumber, dentition);
                },

                getToothColor(toothNumber, dentition) {
                    if (!this.odontogramState?.[dentition]?.[toothNumber]) {
                        return this.statusConfig['healthy']?.color || '#10B981';
                    }
                    const status = this.odontogramState[dentition][toothNumber].status;
                    return this.statusConfig[status]?.color || '#10B981';
                },

                getToothStatus(toothNumber, dentition) {
                    if (!this.odontogramState?.[dentition]?.[toothNumber]) {
                        return this.statusConfig['healthy']?.label || 'Sano';
                    }
                    const status = this.odontogramState[dentition][toothNumber].status;
                    return this.statusConfig[status]?.label || 'Sano';
                },

                showToothTooltip(toothNumber, dentition) {
                    const status = this.getToothStatus(toothNumber, dentition);
                    const tooltip = document.getElementById(`tooltip-${dentition}-${toothNumber}`);
                    if (tooltip) {
                        tooltip.textContent = `Diente ${toothNumber}: ${status}`;
                        tooltip.classList.remove('opacity-0');
                        setTimeout(() => {
                            tooltip.classList.add('opacity-0');
                        }, 2000);
                    }
                },

                resetOdontogram() {
                    this.odontogramState = { permanent: {}, temporary: {} };
                },

                exportOdontogram() {
                    const data = JSON.stringify(this.odontogramState, null, 2);
                    const blob = new Blob([data], { type: 'application/json' });
                    const url = URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = 'odontogram.json';
                    a.click();
                    URL.revokeObjectURL(url);
                },

                getStatusCount(status) {
                    if (!this.odontogramState) return 0;

                    const permanentCount = Object.values(this.odontogramState.permanent || {})
                        .filter(tooth => tooth.status === status).length;
                    const temporaryCount = Object.values(this.odontogramState.temporary || {})
                        .filter(tooth => tooth.status === status).length;

                    return permanentCount + temporaryCount;
                }
            }
        }
    </script>
</x-dynamic-component>
