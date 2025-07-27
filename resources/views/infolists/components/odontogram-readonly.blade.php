@php
    $record = $getState(); // Obtener el record desde el state
    $odontogramData = $record->odontogram ?? ['permanent' => [], 'temporal' => []];

    $toothStatuses = [
        'healthy' => ['label' => 'Sano', 'color' => '#10B981', 'icon' => '✓'],
        'cavity' => ['label' => 'Caries', 'color' => '#EF4444', 'icon' => '●'],
        'treated' => ['label' => 'Tratado', 'color' => '#3B82F6', 'icon' => '■'],
        'missing' => ['label' => 'Ausente', 'color' => '#6B7280', 'icon' => '✗'],
        'implant' => ['label' => 'Implante', 'color' => '#8B5CF6', 'icon' => '◆'],
        'crown' => ['label' => 'Corona', 'color' => '#F59E0B', 'icon' => '♦'],
        'root_canal' => ['label' => 'Endodoncia', 'color' => '#EC4899', 'icon' => '◊'],
        'fracture' => ['label' => 'Fractura', 'color' => '#DC2626', 'icon' => '⚡'],
        'bridge' => ['label' => 'Puente', 'color' => '#059669', 'icon' => '⌒'],
    ];

    // Determinar qué tipo de dentición mostrar basado en los datos
    $showPermanent = !empty($odontogramData['permanent']);
    $showTemporary = !empty($odontogramData['temporal']);
    $showMixed = !empty($odontogramData['mixed']);
    $defaultView = $showMixed ? 'mixed' : ($showPermanent ? 'permanent' : ($showTemporary ? 'temporal' : 'permanent'));
@endphp


<!-- Carga directa del CSS desde la carpeta pública -->
<link rel="stylesheet" href="{{ asset('css/odontogram-professional.css') }}">
<!-- Incluir html2canvas para exportar como imagen -->
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>


<div 
    x-data="readonlyOdontogramComponent()"
    x-init="init()"
    class="professional-odontogram readonly-mode"
    id="odontogram-capture"
>

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
                    <h2 class="header-title">Odontograma Digital - {{ $record->name }}</h2>
                    <p class="header-subtitle">Sistema FDI • Vista de solo lectura • Registro profesional</p>
                </div>
            </div>
            <div class="header-actions-professional">
                <button type="button" class="btn-professional btn-secondary" x-on:click="showSummary = !showSummary">
                    <svg class="btn-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"/>
                    </svg>
                    <span>Resumen</span>
                </button>
                <button type="button" class="btn-professional btn-primary" x-on:click="exportOdontogramData()">
                    <svg class="btn-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"/>
                    </svg>
                    <span>Exportar</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Professional Type Selector (Readonly) -->
    <div class="odontogram-types-professional">
        <div class="types-header">
            <h3 class="types-title">Tipos de Odontograma Registrados</h3>
            <p class="types-description">Información dental disponible para {{ $record->name }}</p>
        </div>
        <div class="types-cards-grid">

            @if($showMixed)
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
                        <p class="type-card-description">Permanentes + Temporales • Registros disponibles</p>
                        <div class="type-card-detail">Transición gradual</div>
                    </div>
                    <div class="type-card-indicator"></div>
                </div>
            @endif
            @if($showPermanent)
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
                        <p class="type-card-description">32 dientes • Registros disponibles</p>
                        <div class="type-card-detail">Numeración FDI: 11-48</div>
                    </div>
                    <div class="type-card-indicator"></div>
                </div>
            @endif
            @if($showTemporary)
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
                        <p class="type-card-description">20 dientes • Registros disponibles</p>
                        <div class="type-card-detail">Numeración FDI: 51-85</div>
                    </div>
                    <div class="type-card-indicator"></div>
                </div>
            @endif

            @if(!$showPermanent && !$showTemporary && !$showMixed)
                <div class="type-card-professional">
                    <div class="type-card-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                    <div class="type-card-content">
                        <h4 class="type-card-title">Sin Registros</h4>
                        <p class="type-card-description">No hay información dental registrada</p>
                        <div class="type-card-detail">Crear un registro para comenzar</div>
                    </div>
                </div>
            @endif
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
            <p class="legend-description">Leyenda de estados registrados en el odontograma</p>
        </div>
        
        <div class="legend-grid-professional">
            @foreach ($toothStatuses as $status => $config)
                <div class="legend-item-professional">
                    <div class="legend-icon-wrapper" 
                         style="background-color: {{ $config['color'] }}15; border-color: {{ $config['color'] }};">
                        <div class="legend-status-icon" 
                             style="background-color: {{ $config['color'] }};">
                            {{ $config['icon'] ?? '' }}
                        </div>
                    </div>
                    <div class="legend-content">
                        <h4 class="legend-item-title">{{ $config['label'] }}</h4>
                        <p class="legend-item-description">Estado registrado en el odontograma</p>
                    </div>
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
                        <span class="stat-number" x-text="getAffectedFacesCount()"></span>
                        <span class="stat-label">Caras con Estado</span>
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
                    <div class="jaw-info">
                        <span class="jaw-control-btn readonly">Solo lectura</span>
                    </div>
                </div>
                <div class="teeth-row">
                    <template x-for="tooth in getTeethForView('upper')" :key="tooth.number">
                        <div class="tooth-professional readonly" 
                             :class="{ 'has-selections': hasAffectedFaces(tooth.number) }">
                            
                            <!-- Número del diente -->
                            <div class="tooth-number-professional" x-text="tooth.number"></div>
                            
                            <!-- Diseño circular con 5 caras -->
                            <div class="tooth-faces-professional">
                                <!-- Círculo base -->
                                <div class="tooth-circle"></div>
                                
                                <!-- Cara Oclusal (Centro) -->
                                <div class="tooth-face oclusal-face readonly" 
                                     :class="getFaceClass(tooth.number, 'oclusal')"
                                     :style="getFaceStyle(tooth.number, 'oclusal')"
                                     x-on:mouseover="showFaceTooltip($event, 'oclusal', tooth.number)"
                                     x-on:mouseout="hideFaceTooltip()">
                                    <span class="face-label">O</span>
                                </div>
                                
                                <!-- Cara Vestibular (Arriba) -->
                                <div class="tooth-face vestibular-face readonly" 
                                     :class="getFaceClass(tooth.number, 'vestibular')"
                                     :style="getFaceStyle(tooth.number, 'vestibular')"
                                     x-on:mouseover="showFaceTooltip($event, 'vestibular', tooth.number)"
                                     x-on:mouseout="hideFaceTooltip()">
                                    <span class="face-label">V</span>
                                </div>
                                
                                <!-- Cara Lingual (Abajo) -->
                                <div class="tooth-face lingual-face readonly" 
                                     :class="getFaceClass(tooth.number, 'lingual')"
                                     :style="getFaceStyle(tooth.number, 'lingual')"
                                     x-on:mouseover="showFaceTooltip($event, 'lingual', tooth.number)"
                                     x-on:mouseout="hideFaceTooltip()">
                                    <span class="face-label">L</span>
                                </div>
                                
                                <!-- Cara Mesial (Derecha) -->
                                <div class="tooth-face mesial-face readonly" 
                                     :class="getFaceClass(tooth.number, 'mesial')"
                                     :style="getFaceStyle(tooth.number, 'mesial')"
                                     x-on:mouseover="showFaceTooltip($event, 'mesial', tooth.number)"
                                     x-on:mouseout="hideFaceTooltip()">
                                    <span class="face-label">M</span>
                                </div>
                                
                                <!-- Cara Central/Distal (Izquierda) -->
                                <div class="tooth-face central-face readonly" 
                                     :class="getFaceClass(tooth.number, 'central')"
                                     :style="getFaceStyle(tooth.number, 'central')"
                                     x-on:mouseover="showFaceTooltip($event, 'central', tooth.number)"
                                     x-on:mouseout="hideFaceTooltip()">
                                    <span class="face-label">C</span>
                                </div>
                            </div>
                            
                            <!-- Indicador del tipo de diente -->
                            <div class="tooth-type-indicator" x-text="getToothType(tooth.number)"></div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Lower Jaw -->
            <div class="jaw-section lower-jaw">
                <div class="jaw-header">
                    <h4 class="jaw-title">Maxilar Inferior</h4>
                    <div class="jaw-info">
                        <span class="jaw-control-btn readonly">Solo lectura</span>
                    </div>
                </div>
                <div class="teeth-row">
                    <template x-for="tooth in getTeethForView('lower')" :key="tooth.number">
                        <div class="tooth-professional readonly" 
                             :class="{ 'has-selections': hasAffectedFaces(tooth.number) }">
                            
                            <!-- Número del diente -->
                            <div class="tooth-number-professional" x-text="tooth.number"></div>
                            
                            <!-- Diseño circular con 5 caras -->
                            <div class="tooth-faces-professional">
                                <!-- Círculo base -->
                                <div class="tooth-circle"></div>
                                
                                <!-- Cara Oclusal (Centro) -->
                                <div class="tooth-face oclusal-face readonly" 
                                     :class="getFaceClass(tooth.number, 'oclusal')"
                                     :style="getFaceStyle(tooth.number, 'oclusal')"
                                     x-on:mouseover="showFaceTooltip($event, 'oclusal', tooth.number)"
                                     x-on:mouseout="hideFaceTooltip()">
                                    <span class="face-label">O</span>
                                </div>
                                
                                <!-- Cara Vestibular (Arriba) -->
                                <div class="tooth-face vestibular-face readonly" 
                                     :class="getFaceClass(tooth.number, 'vestibular')"
                                     :style="getFaceStyle(tooth.number, 'vestibular')"
                                     x-on:mouseover="showFaceTooltip($event, 'vestibular', tooth.number)"
                                     x-on:mouseout="hideFaceTooltip()">
                                    <span class="face-label">V</span>
                                </div>
                                
                                <!-- Cara Lingual (Abajo) -->
                                <div class="tooth-face lingual-face readonly" 
                                     :class="getFaceClass(tooth.number, 'lingual')"
                                     :style="getFaceStyle(tooth.number, 'lingual')"
                                     x-on:mouseover="showFaceTooltip($event, 'lingual', tooth.number)"
                                     x-on:mouseout="hideFaceTooltip()">
                                    <span class="face-label">L</span>
                                </div>
                                
                                <!-- Cara Mesial (Derecha) -->
                                <div class="tooth-face mesial-face readonly" 
                                     :class="getFaceClass(tooth.number, 'mesial')"
                                     :style="getFaceStyle(tooth.number, 'mesial')"
                                     x-on:mouseover="showFaceTooltip($event, 'mesial', tooth.number)"
                                     x-on:mouseout="hideFaceTooltip()">
                                    <span class="face-label">M</span>
                                </div>
                                
                                <!-- Cara Central/Distal (Izquierda) -->
                                <div class="tooth-face central-face readonly" 
                                     :class="getFaceClass(tooth.number, 'central')"
                                     :style="getFaceStyle(tooth.number, 'central')"
                                     x-on:mouseover="showFaceTooltip($event, 'central', tooth.number)"
                                     x-on:mouseout="hideFaceTooltip()">
                                    <span class="face-label">C</span>
                                </div>
                            </div>
                            
                            <!-- Indicador del tipo de diente -->
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
            <div class="tooltip-status" x-show="tooltipStatus" x-text="tooltipStatus"></div>
        </div>
    </div>

    <!-- Professional Summary Panel -->
    <div class="summary-panel-professional" x-show="showSummary" x-transition>
        <div class="summary-header">
            <h3 class="summary-title">Resumen del Odontograma - {{ $record->name }}</h3>
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
                    <div class="stat-number" x-text="getAffectedFacesCount()"></div>
                    <div class="stat-label">Caras con Estado</div>
                </div>
                @foreach ($toothStatuses as $status => $config)
                    <div class="stat-card">
                        <div class="stat-number" x-text="getStatusCount('{{ $status }}')"></div>
                        <div class="stat-label">{{ $config['label'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <script>
        function readonlyOdontogramComponent() {
            return {
                selectedType: '{{ $defaultView }}',
                odontogramData: (() => {
                    const data = @json($odontogramData);
                    if (data.mixed) {
                        return { mixed: data.mixed };
                    }
                    return data;
                })(),
                toothStatuses: @json($toothStatuses),
                showTooltip: false,
                tooltipTitle: '',
                tooltipDescription: '',
                tooltipStatus: '',
                tooltipStyle: '',
                showSummary: false,

                init() {
                    // Inicialización para modo readonly
                    console.log('Odontograma readonly inicializado', this.odontogramData);
                },

                changeType(type) {
                    this.selectedType = type;
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
                        },
                        mixed: {
                            upper: [18, 17, 16, 15, 14, 13, 12, 11, 21, 22, 23, 24, 25, 26, 27, 28, 55, 54, 53, 52, 51, 61, 62, 63, 64, 65],
                            lower: [48, 47, 46, 45, 44, 43, 42, 41, 31, 32, 33, 34, 35, 36, 37, 38, 85, 84, 83, 82, 81, 71, 72, 73, 74, 75]
                        }
                    };
                    const teeth = teethMap[this.selectedType] || teethMap.permanent;
                    return (teeth[jaw] || []).map(number => ({ number }));
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
                    return this.odontogramData[this.selectedType]?.[toothNumber]?.faces?.[face];
                },

                hasAffectedFaces(toothNumber) {
                    const tooth = this.odontogramData[this.selectedType]?.[toothNumber];
                    return tooth && Object.keys(tooth.faces || {}).length > 0;
                },

                getStatusConfig(status) {
                    return this.toothStatuses[status] || { color: '#E5E7EB', label: 'Sin estado' };
                },

                showFaceTooltip(event, face, toothNumber) {
                    const faceNames = {
                        oclusal: { title: 'Cara Oclusal', description: 'Superficie de masticación del diente' },
                        vestibular: { title: 'Cara Vestibular', description: 'Superficie externa hacia los labios/mejillas' },
                        central: { title: 'Cara Central', description: 'Superficie central del diente' },
                        lingual: { title: 'Cara Lingual', description: 'Superficie interna hacia la lengua' },
                        mesial: { title: 'Cara Mesial', description: 'Superficie hacia el centro de la arcada' }
                    };

                    const status = this.getFaceStatus(toothNumber, face);
                    const statusConfig = status ? this.getStatusConfig(status) : null;

                    this.tooltipTitle = `${faceNames[face].title} - Diente ${toothNumber}`;
                    this.tooltipDescription = faceNames[face].description;
                    this.tooltipStatus = statusConfig ? `Estado: ${statusConfig.label}` : 'Estado: Normal';
                    this.showTooltip = true;

                    const rect = event.target.getBoundingClientRect();
                    this.tooltipStyle = `left: ${rect.left + rect.width / 2}px; top: ${rect.top - 10}px;`;
                },

                hideFaceTooltip() {
                    this.showTooltip = false;
                },

                getTotalTeeth() {
                    const counts = { permanent: 32, temporal: 20 };
                    return counts[this.selectedType] || 0;
                },

                getAffectedFacesCount() {
                    let count = 0;
                    const typeData = this.odontogramData[this.selectedType] || {};
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
                        temporal: 'Dentición Temporal'
                    };
                    return titles[this.selectedType] || 'Odontograma';
                },

                getGridSubtitle() {
                    const subtitles = {
                        permanent: '32 dientes adultos - Vista de solo lectura',
                        temporal: '20 dientes infantiles - Vista de solo lectura'
                    };
                    return subtitles[this.selectedType] || '';
                },

                getStatusCount(status) {
                    let count = 0;
                    Object.values(this.odontogramData).forEach(typeData => {
                        Object.values(typeData).forEach(tooth => {
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

function exportOdontogramData() {
    // Selecciona el contenedor visual del odontograma
    const odontogramElement = document.getElementById('odontogram-capture');
    if (!odontogramElement) {
        alert('No se encontró el odontograma para exportar.');
        return;
    }
    // Opciones para mejor calidad
    const options = {
        scale: 2,
        backgroundColor: '#ffffff',
        logging: false,
        allowTaint: true,
        useCORS: true
    };
    html2canvas(odontogramElement, options).then(canvas => {
        const link = document.createElement('a');
        link.download = `odontograma_{{ $record->name }}_{{ now()->format('Y-m-d') }}.png`;
        link.href = canvas.toDataURL('image/png');
        link.click();
    }).catch(error => {
        alert('Error al generar la imagen. Inténtalo de nuevo.');
        console.error(error);
    });
}
    </script>
</div>
