<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div x-data="{
        odontogramState: $wire.entangle('{{ $getStatePath() }}').defer || { permanent: {}, temporary: {} },
        statuses: @js(array_keys($getToothStatuses())),
        statusConfig: @js($getToothStatuses()),
        showPermanent: {{ $getShowPermanent() ? 'true' : 'false' }},
        showTemporary: {{ $getShowTemporary() ? 'true' : 'false' }},
        
        init() {
            // Asegurar que el estado inicial esté bien configurado
            if (!this.odontogramState || this.odontogramState === null) {
                this.odontogramState = { permanent: {}, temporary: {} };
            }
            if (!this.odontogramState.permanent) {
                this.odontogramState.permanent = {};
            }
            if (!this.odontogramState.temporary) {
                this.odontogramState.temporary = {};
            }
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
            if (!this.odontogramState || !this.odontogramState[dentition]) {
                return this.statusConfig['healthy']?.color || '#10B981';
            }
            const status = this.odontogramState[dentition][toothNumber]?.status || 'healthy';
            return this.statusConfig[status]?.color || '#10B981';
        },
        
        getToothStatus(toothNumber, dentition) {
            if (!this.odontogramState || !this.odontogramState[dentition]) {
                return this.statusConfig['healthy']?.label || 'Sano';
            }
            const status = this.odontogramState[dentition][toothNumber]?.status || 'healthy';
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
    }" 
    class="odontogram-container space-y-6 p-6">
        
        <!-- Header with controls -->
        <div class="odontogram-header">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center backdrop-blur-lg">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1.01M15 10h1.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="odontogram-title">Odontograma Digital Profesional</h3>
                        <p class="odontogram-subtitle">Sistema de notación FDI - Haz clic en los dientes para cambiar su estado</p>
                    </div>
                </div>
                
                <div class="odontogram-controls">
                    <button type="button" 
                            x-on:click="resetOdontogram()"
                            class="odontogram-btn danger">
                        <svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Limpiar Todo
                    </button>
                    <button type="button" 
                            x-on:click="exportOdontogram()"
                            class="odontogram-btn">
                        <svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Exportar JSON
                    </button>
                </div>
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

        <!-- Permanent Dentition -->
        <div x-show="showPermanent" class="dentition-section">
            <div class="dentition-header">
                <h4 class="dentition-title">Dentición Permanente (32 dientes)</h4>
                <span class="dentition-info">Sistema FDI</span>
            </div>
            
            <div class="relative">
                <svg class="odontogram-svg" height="400" viewBox="0 0 800 400">
                    <!-- Upper jaw -->
                    <g class="upper-jaw">
                        <!-- Quadrant 1 (Upper Right) -->
                        <g class="quadrant-1" transform="translate(400, 50)">
                            <text x="0" y="-10" class="quadrant-label">Cuadrante 1</text>
                            @for ($i = 18; $i >= 11; $i--)
                                <g class="tooth-group" transform="translate({{ (18 - $i) * 35 }}, 0)">
                                    <rect x="-12" y="0" width="24" height="30" rx="4" 
                                          class="tooth-rect" 
                                          :fill="getToothColor({{ $i }}, 'permanent')"
                                          stroke="#e5e7eb" stroke-width="1"
                                          x-on:click="updateTooth({{ $i }}, 'permanent')"/>
                                    <text x="0" y="20" class="tooth-number">{{ $i }}</text>
                                    <text x="0" y="45" class="tooth-status" 
                                          x-text="getToothStatus({{ $i }}, 'permanent')"></text>
                                    <!-- Tooltip -->
                                    <text id="tooltip-permanent-{{ $i }}" x="0" y="-15" 
                                          class="tooth-tooltip"></text>
                                </g>
                            @endfor
                        </g>
                        
                        <!-- Quadrant 2 (Upper Left) -->
                        <g class="quadrant-2" transform="translate(400, 50)">
                            <text x="-280" y="-10" text-anchor="middle" class="text-xs fill-gray-600 font-semibold">Cuadrante 2</text>
                            @for ($i = 21; $i <= 28; $i++)
                                <g class="tooth-group" transform="translate({{ -($i - 21 + 1) * 35 }}, 0)">
                                    <rect x="-12" y="0" width="24" height="30" rx="4" 
                                          class="tooth-rect cursor-pointer transition-all duration-300 hover:stroke-2 hover:stroke-blue-400" 
                                          :fill="getToothColor({{ $i }}, 'permanent')"
                                          stroke="#e5e7eb" stroke-width="1"
                                          x-on:click="updateTooth({{ $i }}, 'permanent')"/>
                                    <text x="0" y="20" text-anchor="middle" class="text-xs fill-white font-bold pointer-events-none">{{ $i }}</text>
                                    <text x="0" y="45" text-anchor="middle" class="text-xs fill-gray-600 font-medium pointer-events-none" 
                                          x-text="getToothStatus({{ $i }}, 'permanent')"></text>
                                    <!-- Tooltip -->
                                    <text id="tooltip-permanent-{{ $i }}" x="0" y="-15" text-anchor="middle" 
                                          class="text-xs fill-blue-600 font-medium opacity-0 transition-opacity duration-300"></text>
                                </g>
                            @endfor
                        </g>
                    </g>

                    <!-- Lower jaw -->
                    <g class="lower-jaw">
                        <!-- Quadrant 3 (Lower Left) -->
                        <g class="quadrant-3" transform="translate(400, 320)">
                            <text x="-280" y="25" text-anchor="middle" class="text-xs fill-gray-600 font-semibold">Cuadrante 3</text>
                            @for ($i = 31; $i <= 38; $i++)
                                <g class="tooth-group" transform="translate({{ -($i - 31 + 1) * 35 }}, 0)">
                                    <rect x="-12" y="-30" width="24" height="30" rx="4" 
                                          class="tooth-rect cursor-pointer transition-all duration-300 hover:stroke-2 hover:stroke-blue-400" 
                                          :fill="getToothColor({{ $i }}, 'permanent')"
                                          stroke="#e5e7eb" stroke-width="1"
                                          x-on:click="updateTooth({{ $i }}, 'permanent')"/>
                                    <text x="0" y="-10" text-anchor="middle" class="text-xs fill-white font-bold pointer-events-none">{{ $i }}</text>
                                    <text x="0" y="-40" text-anchor="middle" class="text-xs fill-gray-600 font-medium pointer-events-none" 
                                          x-text="getToothStatus({{ $i }}, 'permanent')"></text>
                                    <!-- Tooltip -->
                                    <text id="tooltip-permanent-{{ $i }}" x="0" y="20" text-anchor="middle" 
                                          class="text-xs fill-blue-600 font-medium opacity-0 transition-opacity duration-300"></text>
                                </g>
                            @endfor
                        </g>
                        
                        <!-- Quadrant 4 (Lower Right) -->
                        <g class="quadrant-4" transform="translate(400, 320)">
                            <text x="0" y="25" text-anchor="middle" class="text-xs fill-gray-600 font-semibold">Cuadrante 4</text>
                            @for ($i = 41; $i <= 48; $i++)
                                <g class="tooth-group" transform="translate({{ ($i - 41) * 35 }}, 0)">
                                    <rect x="-12" y="-30" width="24" height="30" rx="4" 
                                          class="tooth-rect cursor-pointer transition-all duration-300 hover:stroke-2 hover:stroke-blue-400" 
                                          :fill="getToothColor({{ $i }}, 'permanent')"
                                          stroke="#e5e7eb" stroke-width="1"
                                          x-on:click="updateTooth({{ $i }}, 'permanent')"/>
                                    <text x="0" y="-10" text-anchor="middle" class="text-xs fill-white font-bold pointer-events-none">{{ $i }}</text>
                                    <text x="0" y="-40" text-anchor="middle" class="text-xs fill-gray-600 font-medium pointer-events-none" 
                                          x-text="getToothStatus({{ $i }}, 'permanent')"></text>
                                    <!-- Tooltip -->
                                    <text id="tooltip-permanent-{{ $i }}" x="0" y="20" text-anchor="middle" 
                                          class="text-xs fill-blue-600 font-medium opacity-0 transition-opacity duration-300"></text>
                                </g>
                            @endfor
                        </g>
                    </g>
                    
                    <!-- Central dividing line -->
                    <line x1="400" y1="40" x2="400" y2="340" class="divider-line"/>
                </svg>
            </div>
        </div>

        <!-- Temporary Dentition -->
        <div x-show="showTemporary" class="dentition-section">
            <div class="dentition-header">
                <h4 class="dentition-title">Dentición Temporal (20 dientes)</h4>
                <span class="dentition-info">Sistema FDI</span>
            </div>
            
            <div class="relative">
                <svg class="odontogram-svg" height="300" viewBox="0 0 600 300">
                    <!-- Upper jaw -->
                    <g class="upper-jaw">
                        <!-- Quadrant 5 (Upper Right) -->
                        <g class="quadrant-5" transform="translate(300, 50)">
                            <text x="0" y="-10" text-anchor="middle" class="text-xs fill-gray-600 font-semibold">Cuadrante 5</text>
                            @for ($i = 55; $i >= 51; $i--)
                                <g class="tooth-group" transform="translate({{ (55 - $i) * 35 }}, 0)">
                                    <rect x="-12" y="0" width="24" height="25" rx="4" 
                                          class="tooth-rect cursor-pointer transition-all duration-300 hover:stroke-2 hover:stroke-blue-400" 
                                          :fill="getToothColor({{ $i }}, 'temporary')"
                                          stroke="#e5e7eb" stroke-width="1"
                                          x-on:click="updateTooth({{ $i }}, 'temporary')"/>
                                    <text x="0" y="17" text-anchor="middle" class="text-xs fill-white font-bold pointer-events-none">{{ $i }}</text>
                                    <text x="0" y="40" text-anchor="middle" class="text-xs fill-gray-600 font-medium pointer-events-none" 
                                          x-text="getToothStatus({{ $i }}, 'temporary')"></text>
                                    <!-- Tooltip -->
                                    <text id="tooltip-temporary-{{ $i }}" x="0" y="-15" text-anchor="middle" 
                                          class="text-xs fill-blue-600 font-medium opacity-0 transition-opacity duration-300"></text>
                                </g>
                            @endfor
                        </g>
                        
                        <!-- Quadrant 6 (Upper Left) -->
                        <g class="quadrant-6" transform="translate(300, 50)">
                            <text x="-175" y="-10" text-anchor="middle" class="text-xs fill-gray-600 font-semibold">Cuadrante 6</text>
                            @for ($i = 61; $i <= 65; $i++)
                                <g class="tooth-group" transform="translate({{ -($i - 61 + 1) * 35 }}, 0)">
                                    <rect x="-12" y="0" width="24" height="25" rx="4" 
                                          class="tooth-rect cursor-pointer transition-all duration-300 hover:stroke-2 hover:stroke-blue-400" 
                                          :fill="getToothColor({{ $i }}, 'temporary')"
                                          stroke="#e5e7eb" stroke-width="1"
                                          x-on:click="updateTooth({{ $i }}, 'temporary')"/>
                                    <text x="0" y="17" text-anchor="middle" class="text-xs fill-white font-bold pointer-events-none">{{ $i }}</text>
                                    <text x="0" y="40" text-anchor="middle" class="text-xs fill-gray-600 font-medium pointer-events-none" 
                                          x-text="getToothStatus({{ $i }}, 'temporary')"></text>
                                    <!-- Tooltip -->
                                    <text id="tooltip-temporary-{{ $i }}" x="0" y="-15" text-anchor="middle" 
                                          class="text-xs fill-blue-600 font-medium opacity-0 transition-opacity duration-300"></text>
                                </g>
                            @endfor
                        </g>
                    </g>

                    <!-- Lower jaw -->
                    <g class="lower-jaw">
                        <!-- Quadrant 7 (Lower Left) -->
                        <g class="quadrant-7" transform="translate(300, 225)">
                            <text x="-175" y="25" text-anchor="middle" class="text-xs fill-gray-600 font-semibold">Cuadrante 7</text>
                            @for ($i = 71; $i <= 75; $i++)
                                <g class="tooth-group" transform="translate({{ -($i - 71 + 1) * 35 }}, 0)">
                                    <rect x="-12" y="-25" width="24" height="25" rx="4" 
                                          class="tooth-rect cursor-pointer transition-all duration-300 hover:stroke-2 hover:stroke-blue-400" 
                                          :fill="getToothColor({{ $i }}, 'temporary')"
                                          stroke="#e5e7eb" stroke-width="1"
                                          x-on:click="updateTooth({{ $i }}, 'temporary')"/>
                                    <text x="0" y="-8" text-anchor="middle" class="text-xs fill-white font-bold pointer-events-none">{{ $i }}</text>
                                    <text x="0" y="-35" text-anchor="middle" class="text-xs fill-gray-600 font-medium pointer-events-none" 
                                          x-text="getToothStatus({{ $i }}, 'temporary')"></text>
                                    <!-- Tooltip -->
                                    <text id="tooltip-temporary-{{ $i }}" x="0" y="15" text-anchor="middle" 
                                          class="text-xs fill-blue-600 font-medium opacity-0 transition-opacity duration-300"></text>
                                </g>
                            @endfor
                        </g>
                        
                        <!-- Quadrant 8 (Lower Right) -->
                        <g class="quadrant-8" transform="translate(300, 225)">
                            <text x="0" y="25" text-anchor="middle" class="text-xs fill-gray-600 font-semibold">Cuadrante 8</text>
                            @for ($i = 81; $i <= 85; $i++)
                                <g class="tooth-group" transform="translate({{ ($i - 81) * 35 }}, 0)">
                                    <rect x="-12" y="-25" width="24" height="25" rx="4" 
                                          class="tooth-rect cursor-pointer transition-all duration-300 hover:stroke-2 hover:stroke-blue-400" 
                                          :fill="getToothColor({{ $i }}, 'temporary')"
                                          stroke="#e5e7eb" stroke-width="1"
                                          x-on:click="updateTooth({{ $i }}, 'temporary')"/>
                                    <text x="0" y="-8" text-anchor="middle" class="text-xs fill-white font-bold pointer-events-none">{{ $i }}</text>
                                    <text x="0" y="-35" text-anchor="middle" class="text-xs fill-gray-600 font-medium pointer-events-none" 
                                          x-text="getToothStatus({{ $i }}, 'temporary')"></text>
                                    <!-- Tooltip -->
                                    <text id="tooltip-temporary-{{ $i }}" x="0" y="15" text-anchor="middle" 
                                          class="text-xs fill-blue-600 font-medium opacity-0 transition-opacity duration-300"></text>
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
</x-dynamic-component>
