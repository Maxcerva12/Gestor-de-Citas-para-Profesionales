<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div>
        <!-- Carga directa del CSS desde la carpeta pública -->
        <link rel="stylesheet" href="{{ asset('css/odontogram-professional.css') }}">
        <!-- Incluir html2canvas para exportar como imagen -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
        
        <div
            x-data="professionalOdontogramComponent('{{ $getStatePath() }}')"
            x-init="init()"
            class="professional-odontogram">

        <!-- Elegant Header with Glass Effect -->
        <div class="odontogram-header-professional">
            <div class="header-glass-overlay"></div>
            <div class="header-content-professional">
                <div class="header-left">
                    <div class="header-icon-wrapper">
                        <svg class="header-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="header-text">
                        <h2 class="header-title">Odontograma Digital Profesional</h2>
                        <p class="header-subtitle">Sistema FDI • Registro profesional de hallazgos dentales</p>
                    </div>
                </div>
                <div class="header-actions-professional">
                    <button type="button" class="btn-professional btn-secondary" x-on:click="showSummary = !showSummary">
                        <svg class="btn-icon" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"/>
                        </svg>
                        <span>Resumen</span>
                    </button>
                    <button type="button" class="btn-professional btn-danger" x-on:click="confirmReset()">
                        <svg class="btn-icon" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z"/>
                        </svg>
                        <span>Limpiar</span>
                    </button>
                    <button type="button" class="btn-professional btn-primary" x-on:click="exportData()">
                        <svg class="btn-icon" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"/>
                        </svg>
                        <span>Exportar</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Professional Type Selector -->
        <div class="odontogram-types-professional">
            <div class="types-header">
                <h3 class="types-title">Tipos de Odontograma</h3>
                <p class="types-description">Selecciona el tipo de dentición según la edad del paciente</p>
            </div>
            <div class="types-cards-grid">
                <div class="type-card-professional" 
                     :class="{ 'active': selectedType === 'permanent' }"
                     x-on:click="changeType('permanent')">
                    <div class="type-card-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="type-card-content">
                        <h4 class="type-card-title">Dentición Permanente</h4>
                        <p class="type-card-description">32 dientes • Adultos (17+ años)</p>
                        <div class="type-card-detail">Numeración FDI: 11-48</div>
                    </div>
                    <div class="type-card-indicator"></div>
                </div>

                <div class="type-card-professional" 
                     :class="{ 'active': selectedType === 'temporal' }"
                     x-on:click="changeType('temporal')">
                    <div class="type-card-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <div class="type-card-content">
                        <h4 class="type-card-title">Dentición Temporal</h4>
                        <p class="type-card-description">20 dientes • Niños (6 meses - 6 años)</p>
                        <div class="type-card-detail">Numeración FDI: 51-85</div>
                    </div>
                    <div class="type-card-indicator"></div>
                </div>

                <div class="type-card-professional" 
                     :class="{ 'active': selectedType === 'mixed' }"
                     x-on:click="changeType('mixed')">
                    <div class="type-card-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="type-card-content">
                        <h4 class="type-card-title">Dentición Mixta</h4>
                        <p class="type-card-description">Temporales + Permanentes • Niños (6-12 años)</p>
                        <div class="type-card-detail">Transición gradual</div>
                    </div>
                    <div class="type-card-indicator"></div>
                </div>
            </div>
        </div>

        <!-- Professional Legend -->
        <div class="legend-professional">
            <div class="legend-header">
                <h3 class="legend-title">
                    <svg class="legend-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Estados Dentales
                </h3>
                <p class="legend-description">Selecciona un estado y haz clic en las caras del diente</p>
            </div>
            
            <div class="legend-grid-professional">
                @foreach (($toothStatuses ?? $getToothStatuses()) as $status => $config)
                    <div class="legend-item-professional" 
                         :class="{ 'active': selectedStatus === '{{ $status }}' }"
                         x-on:click="selectStatus('{{ $status }}')">
                        <div class="legend-icon-wrapper" 
                             style="background-color: {{ $config['color'] }}15; border-color: {{ $config['color'] }};">
                            <div class="legend-status-icon" 
                                 style="background-color: {{ $config['color'] }};">
                                @if ($config['icon'])
                                    {!! $config['icon'] !!}
                                @endif
                            </div>
                        </div>
                        <div class="legend-content">
                            <h4 class="legend-item-title">{{ $config['label'] }}</h4>
                            <p class="legend-item-description">{{ $config['description'] ?? 'Estado dental' }}</p>
                        </div>
                        <div class="legend-item-indicator"></div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Professional Odontogram Grid -->
        <div class="odontogram-grid-professional">
            <div class="grid-header">
                <h3 class="grid-title">
                    <span x-text="getGridTitle()"></span>
                    <span class="grid-subtitle" x-text="getGridSubtitle()"></span>
                </h3>
                <div class="grid-controls">
                    <div class="grid-stats">
                        <div class="stat-item">
                            <span class="stat-number" x-text="getTotalTeeth()"></span>
                            <span class="stat-label">Dientes</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number" x-text="getSelectedFacesCount()"></span>
                            <span class="stat-label">Caras</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Professional Teeth Grid -->
            <div class="teeth-grid-professional">
                <!-- Upper Jaw -->
                <div class="jaw-section upper-jaw">
                    <div class="jaw-header">
                        <h4 class="jaw-title">Maxilar Superior</h4>
                        <div class="jaw-controls">
                            <button type="button" class="jaw-control-btn" x-on:click="selectAllJaw('upper')">
                                Seleccionar todo
                            </button>
                            <button type="button" class="jaw-control-btn" x-on:click="clearAllJaw('upper')">
                                Limpiar
                            </button>
                        </div>
                    </div>
                    <div class="teeth-row">
                        <template x-for="tooth in getTeethForView('upper')" :key="tooth.number">
                            <div class="tooth-professional" 
                                 :class="{ 'has-selections': hasSelectedFaces(tooth.number) }"
                                 x-on:click="handleToothClick(tooth.number)">
                                <!-- Tooth Number -->
                                <div class="tooth-number-professional" x-text="tooth.number"></div>
                                
                                <!-- 5 Tooth Faces with Professional Design -->
                                <div class="tooth-faces-professional">
                                    <!-- Oclusal/Incisal Face -->
                                    <div class="tooth-face oclusal-face" 
                                         :class="getFaceClass(tooth.number, 'oclusal')"
                                         :style="getFaceStyle(tooth.number, 'oclusal')"
                                         x-on:click.stop="toggleFace(tooth.number, 'oclusal')"
                                         x-on:mouseover="showFaceTooltip($event, 'oclusal')"
                                         x-on:mouseout="hideFaceTooltip()">
                                        <span class="face-label">O</span>
                                    </div>
                                    
                                    <!-- Vestibular Face -->
                                    <div class="tooth-face vestibular-face" 
                                         :class="getFaceClass(tooth.number, 'vestibular')"
                                         :style="getFaceStyle(tooth.number, 'vestibular')"
                                         x-on:click.stop="toggleFace(tooth.number, 'vestibular')"
                                         x-on:mouseover="showFaceTooltip($event, 'vestibular')"
                                         x-on:mouseout="hideFaceTooltip()">
                                        <span class="face-label">V</span>
                                    </div>
                                    
                                    <!-- Central Face -->
                                    <div class="tooth-face central-face" 
                                         :class="getFaceClass(tooth.number, 'central')"
                                         :style="getFaceStyle(tooth.number, 'central')"
                                         x-on:click.stop="toggleFace(tooth.number, 'central')"
                                         x-on:mouseover="showFaceTooltip($event, 'central')"
                                         x-on:mouseout="hideFaceTooltip()">
                                        <span class="face-label">C</span>
                                    </div>
                                    
                                    <!-- Lingual Face -->
                                    <div class="tooth-face lingual-face" 
                                         :class="getFaceClass(tooth.number, 'lingual')"
                                         :style="getFaceStyle(tooth.number, 'lingual')"
                                         x-on:click.stop="toggleFace(tooth.number, 'lingual')"
                                         x-on:mouseover="showFaceTooltip($event, 'lingual')"
                                         x-on:mouseout="hideFaceTooltip()">
                                        <span class="face-label">L</span>
                                    </div>
                                    
                                    <!-- Mesial Face -->
                                    <div class="tooth-face mesial-face" 
                                         :class="getFaceClass(tooth.number, 'mesial')"
                                         :style="getFaceStyle(tooth.number, 'mesial')"
                                         x-on:click.stop="toggleFace(tooth.number, 'mesial')"
                                         x-on:mouseover="showFaceTooltip($event, 'mesial')"
                                         x-on:mouseout="hideFaceTooltip()">
                                        <span class="face-label">M</span>
                                    </div>
                                </div>
                                
                                <!-- Tooth Type Indicator -->
                                <div class="tooth-type-indicator" x-text="getToothType(tooth.number)"></div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Lower Jaw -->
                <div class="jaw-section lower-jaw">
                    <div class="jaw-header">
                        <h4 class="jaw-title">Maxilar Inferior</h4>
                        <div class="jaw-controls">
                            <button type="button" class="jaw-control-btn" x-on:click="selectAllJaw('lower')">
                                Seleccionar todo
                            </button>
                            <button type="button" class="jaw-control-btn" x-on:click="clearAllJaw('lower')">
                                Limpiar
                            </button>
                        </div>
                    </div>
                    <div class="teeth-row">
                        <template x-for="tooth in getTeethForView('lower')" :key="tooth.number">
                            <div class="tooth-professional" 
                                 :class="{ 'has-selections': hasSelectedFaces(tooth.number) }"
                                 x-on:click="handleToothClick(tooth.number)">
                                <!-- Tooth Number -->
                                <div class="tooth-number-professional" x-text="tooth.number"></div>
                                
                                <!-- 5 Tooth Faces with Professional Design -->
                                <div class="tooth-faces-professional">
                                    <!-- Oclusal/Incisal Face -->
                                    <div class="tooth-face oclusal-face" 
                                         :class="getFaceClass(tooth.number, 'oclusal')"
                                         :style="getFaceStyle(tooth.number, 'oclusal')"
                                         x-on:click.stop="toggleFace(tooth.number, 'oclusal')"
                                         x-on:mouseover="showFaceTooltip($event, 'oclusal')"
                                         x-on:mouseout="hideFaceTooltip()">
                                        <span class="face-label">O</span>
                                    </div>
                                    
                                    <!-- Vestibular Face -->
                                    <div class="tooth-face vestibular-face" 
                                         :class="getFaceClass(tooth.number, 'vestibular')"
                                         :style="getFaceStyle(tooth.number, 'vestibular')"
                                         x-on:click.stop="toggleFace(tooth.number, 'vestibular')"
                                         x-on:mouseover="showFaceTooltip($event, 'vestibular')"
                                         x-on:mouseout="hideFaceTooltip()">
                                        <span class="face-label">V</span>
                                    </div>
                                    
                                    <!-- Central Face -->
                                    <div class="tooth-face central-face" 
                                         :class="getFaceClass(tooth.number, 'central')"
                                         :style="getFaceStyle(tooth.number, 'central')"
                                         x-on:click.stop="toggleFace(tooth.number, 'central')"
                                         x-on:mouseover="showFaceTooltip($event, 'central')"
                                         x-on:mouseout="hideFaceTooltip()">
                                        <span class="face-label">C</span>
                                    </div>
                                    
                                    <!-- Lingual Face -->
                                    <div class="tooth-face lingual-face" 
                                         :class="getFaceClass(tooth.number, 'lingual')"
                                         :style="getFaceStyle(tooth.number, 'lingual')"
                                         x-on:click.stop="toggleFace(tooth.number, 'lingual')"
                                         x-on:mouseover="showFaceTooltip($event, 'lingual')"
                                         x-on:mouseout="hideFaceTooltip()">
                                        <span class="face-label">L</span>
                                    </div>
                                    
                                    <!-- Mesial Face -->
                                    <div class="tooth-face mesial-face" 
                                         :class="getFaceClass(tooth.number, 'mesial')"
                                         :style="getFaceStyle(tooth.number, 'mesial')"
                                         x-on:click.stop="toggleFace(tooth.number, 'mesial')"
                                         x-on:mouseover="showFaceTooltip($event, 'mesial')"
                                         x-on:mouseout="hideFaceTooltip()">
                                        <span class="face-label">M</span>
                                    </div>
                                </div>
                                
                                <!-- Tooth Type Indicator -->
                                <div class="tooth-type-indicator" x-text="getToothType(tooth.number)"></div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- Professional Face Tooltip -->
        <div class="face-tooltip-professional" 
             x-show="showTooltip" 
             x-transition
             :style="tooltipStyle">
            <div class="tooltip-content">
                <h5 class="tooltip-title" x-text="tooltipTitle"></h5>
                <p class="tooltip-description" x-text="tooltipDescription"></p>
            </div>
        </div>

        <!-- Professional Summary Panel -->
        <div class="summary-panel-professional" x-show="showSummary" x-transition>
            <div class="summary-header">
                <h3 class="summary-title">Resumen del Odontograma</h3>
                <button type="button" class="close-summary-btn" x-on:click="showSummary = false">
                    <svg viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"/>
                    </svg>
                </button>
            </div>
            <div class="summary-content">
                <div class="summary-stats">
                    <div class="stat-card">
                        <div class="stat-number" x-text="getTotalTeeth()"></div>
                        <div class="stat-label">Total Dientes</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" x-text="getSelectedFacesCount()"></div>
                        <div class="stat-label">Caras Seleccionadas</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" x-text="getStatusCount('healthy')"></div>
                        <div class="stat-label">Dientes Sanos</div>
                    </div>
                </div>
            </div>
        </div>
        
        <script>
            function professionalOdontogramComponent(state) {
            return {
                selectedType: 'permanent',
                selectedStatus: 'healthy',
                odontogramState: {},
                showTooltip: false,
                tooltipTitle: '',
                tooltipDescription: '',
                tooltipStyle: '',
                showSummary: false,

                init() {
                    this.odontogramState = this.$wire.get(state) || {};
                    this.watchStateChanges(state);
                },

                watchStateChanges(state) {
                    this.$watch(() => this.$wire.get(state), (newValue) => {
                        this.odontogramState = newValue || {};
                    });
                },

                changeType(type) {
                    this.selectedType = type;
                },

                selectStatus(status) {
                    this.selectedStatus = status;
                },

                getTeethForView(jaw) {
                    const teethMap = {
                        permanent: {
                            upper: [18, 17, 16, 15, 14, 13, 12, 11, 21, 22, 23, 24, 25, 26, 27, 28],
                            lower: [48, 47, 46, 45, 44, 43, 42, 41, 31, 32, 33, 34, 35, 36, 37, 38]
                        },
                        temporal: {
                            upper: [55, 54, 53, 52, 51, 61, 62, 63, 64, 65],
                            lower: [85, 84, 83, 82, 81, 71, 72, 73, 74, 75]
                        }
                    };

                    const teeth = teethMap[this.selectedType] || teethMap.permanent;
                    return (teeth[jaw] || []).map(number => ({ number }));
                },

                toggleFace(toothNumber, face) {
                    if (!this.odontogramState[this.selectedType]) {
                        this.odontogramState[this.selectedType] = {};
                    }
                    if (!this.odontogramState[this.selectedType][toothNumber]) {
                        this.odontogramState[this.selectedType][toothNumber] = { faces: {} };
                    }

                    const currentStatus = this.odontogramState[this.selectedType][toothNumber].faces[face];
                    if (currentStatus === this.selectedStatus) {
                        delete this.odontogramState[this.selectedType][toothNumber].faces[face];
                    } else {
                        this.odontogramState[this.selectedType][toothNumber].faces[face] = this.selectedStatus;
                    }

                    this.updateWireState(state);
                },

                getFaceClass(toothNumber, face) {
                    const status = this.getFaceStatus(toothNumber, face);
                    return status ? `selected status-${status}` : '';
                },

                getFaceStyle(toothNumber, face) {
                    const status = this.getFaceStatus(toothNumber, face);
                    if (!status) return '';

                    const config = this.getStatusConfig(status);
                    return `background-color: ${config.color}; border-color: ${config.color};`;
                },

                getFaceStatus(toothNumber, face) {
                    return this.odontogramState[this.selectedType]?.[toothNumber]?.faces?.[face];
                },

                hasSelectedFaces(toothNumber) {
                    const tooth = this.odontogramState[this.selectedType]?.[toothNumber];
                    return tooth && Object.keys(tooth.faces || {}).length > 0;
                },

                getStatusConfig(status) {
                    const configs = @json($getToothStatuses());
                    return configs[status] || { color: '#E5E7EB', label: 'Sin estado' };
                },

                showFaceTooltip(event, face) {
                    const faceNames = {
                        oclusal: { title: 'Cara Oclusal', description: 'Superficie de masticación del diente' },
                        vestibular: { title: 'Cara Vestibular', description: 'Superficie externa hacia los labios/mejillas' },
                        central: { title: 'Cara Central', description: 'Superficie central del diente' },
                        lingual: { title: 'Cara Lingual', description: 'Superficie interna hacia la lengua' },
                        mesial: { title: 'Cara Mesial', description: 'Superficie hacia el centro de la arcada' }
                    };

                    this.tooltipTitle = faceNames[face].title;
                    this.tooltipDescription = faceNames[face].description;
                    this.showTooltip = true;

                    const rect = event.target.getBoundingClientRect();
                    this.tooltipStyle = `left: ${rect.left + rect.width / 2}px; top: ${rect.top - 10}px;`;
                },

                hideFaceTooltip() {
                    this.showTooltip = false;
                },

                getTotalTeeth() {
                    const counts = { permanent: 32, temporal: 20, mixed: 52 };
                    return counts[this.selectedType] || 0;
                },

                getSelectedFacesCount() {
                    let count = 0;
                    const typeData = this.odontogramState[this.selectedType] || {};
                    Object.values(typeData).forEach(tooth => {
                        if (tooth.faces) {
                            count += Object.keys(tooth.faces).length;
                        }
                    });
                    return count;
                },

                getToothType(number) {
                    if (number >= 11 && number <= 18) return 'Sup. Der.';
                    if (number >= 21 && number <= 28) return 'Sup. Izq.';
                    if (number >= 31 && number <= 38) return 'Inf. Izq.';
                    if (number >= 41 && number <= 48) return 'Inf. Der.';
                    if (number >= 51 && number <= 55) return 'Temp. S.D.';
                    if (number >= 61 && number <= 65) return 'Temp. S.I.';
                    if (number >= 71 && number <= 75) return 'Temp. I.I.';
                    if (number >= 81 && number <= 85) return 'Temp. I.D.';
                    return '';
                },

                getGridTitle() {
                    const titles = {
                        permanent: 'Dentición Permanente',
                        temporal: 'Dentición Temporal',
                        mixed: 'Dentición Mixta'
                    };
                    return titles[this.selectedType] || 'Odontograma';
                },

                getGridSubtitle() {
                    const subtitles = {
                        permanent: '32 dientes adultos',
                        temporal: '20 dientes infantiles',
                        mixed: 'Dentición en transición'
                    };
                    return subtitles[this.selectedType] || '';
                },

                updateWireState(state) {
                    this.$wire.set(state, this.odontogramState);
                },

                confirmReset() {
                    if (confirm('¿Estás seguro de que deseas limpiar todo el odontograma?')) {
                        this.odontogramState = {};
                        this.updateWireState(state);
                    }
                },

                exportData() {
                    // Mostrar un mensaje de carga mientras se genera la imagen
                    const loadingToast = this.showToast('Generando imagen del odontograma...', 'info');
                    
                    // Capturar todo el odontograma como una imagen
                    const odontogramaElement = document.querySelector('.professional-odontogram');
                    
                    // Configuración para mejor calidad de imagen
                    const options = {
                        scale: 2, // Escala x2 para mejor calidad
                        backgroundColor: '#ffffff', // Fondo blanco
                        logging: false, // Deshabilitar logs
                        allowTaint: true, // Permitir elementos de origen cruzado
                        useCORS: true // Usar CORS para imágenes externas
                    };
                    
                    html2canvas(odontogramaElement, options).then(canvas => {
                        // Convertir el canvas a una URL de datos
                        const imageUrl = canvas.toDataURL('image/png');
                        
                        // Crear un enlace para descargar la imagen
                        const a = document.createElement('a');
                        a.href = imageUrl;
                        a.download = 'odontograma-profesional.png';
                        a.click();
                        
                        // Ocultar el mensaje de carga y mostrar un mensaje de éxito
                        loadingToast.remove();
                        this.showToast('Imagen del odontograma exportada con éxito', 'success');
                    }).catch(error => {
                        console.error('Error al generar la imagen:', error);
                        loadingToast.remove();
                        this.showToast('Error al generar la imagen. Inténtalo de nuevo.', 'error');
                    });
                },
                
                // Función auxiliar para mostrar mensajes de notificación
                showToast(message, type = 'info') {
                    const toast = document.createElement('div');
                    toast.className = `toast-notification toast-${type}`;
                    toast.innerHTML = `
                        <div class="toast-icon">
                            ${type === 'success' ? '<svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>' :
                             type === 'error' ? '<svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>' :
                             '<svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zm-1 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/></svg>'}
                        </div>
                        <div class="toast-message">${message}</div>
                    `;
                    
                    document.body.appendChild(toast);
                    
                    // Mostrar animación del toast
                    setTimeout(() => {
                        toast.classList.add('show');
                    }, 10);
                    
                    // Si no es un toast de carga, ocultarlo automáticamente después de 3 segundos
                    if (type !== 'info' || message.includes('Generando')) {
                        setTimeout(() => {
                            toast.classList.remove('show');
                            setTimeout(() => {
                                if (document.body.contains(toast)) {
                                    document.body.removeChild(toast);
                                }
                            }, 300);
                        }, 3000);
                    }
                    
                    return toast; // Devolver el elemento toast para poder eliminarlo más tarde
                },

                selectAllJaw(jaw) {
                    const teeth = this.getTeethForView(jaw);
                    teeth.forEach(tooth => {
                        ['oclusal', 'vestibular', 'central', 'lingual', 'mesial'].forEach(face => {
                            this.toggleFace(tooth.number, face);
                        });
                    });
                },

                clearAllJaw(jaw) {
                    const teeth = this.getTeethForView(jaw);
                    teeth.forEach(tooth => {
                        if (this.odontogramState[this.selectedType]?.[tooth.number]) {
                            delete this.odontogramState[this.selectedType][tooth.number];
                        }
                    });
                    this.updateWireState(state);
                },

                handleToothClick(toothNumber) {
                    // Handle general tooth click if needed
                },

                getStatusCount(status) {
                    let count = 0;
                    Object.values(this.odontogramState).forEach(typeData => {
                        Object.values(typeData).forEach(tooth => {
                            // Comprobar si alguna cara del diente tiene el estado específico
                            // y contar el diente una sola vez, no cada cara individual
                            if (tooth.faces) {
                                const hasStatus = Object.values(tooth.faces).some(faceStatus => faceStatus === status);
                                if (hasStatus) count++;
                            }
                        });
                    });
                    return count;
                }
            }
        }
    </script>
    </div>
</x-dynamic-component>
