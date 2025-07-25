<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">

    <style>
        /* Professional Odontogram CSS - Ultra Modern Design */
:root {
    /* Primary Color Palette - Medical Blue */
    --odont-primary: #0066cc;
    --odont-primary-light: #3389db;
    --odont-primary-dark: #004499;
    --odont-primary-alpha: rgba(0, 102, 204, 0.1);
    
    /* Secondary Colors */
    --odont-secondary: #6366f1;
    --odont-success: #10b981;
    --odont-warning: #f59e0b;
    --odont-danger: #ef4444;
    --odont-info: #3b82f6;
    
    /* Neutral Colors */
    --odont-white: #ffffff;
    --odont-gray-50: #f9fafb;
    --odont-gray-100: #f3f4f6;
    --odont-gray-200: #e5e7eb;
    --odont-gray-300: #d1d5db;
    --odont-gray-400: #9ca3af;
    --odont-gray-500: #6b7280;
    --odont-gray-600: #4b5563;
    --odont-gray-700: #374151;
    --odont-gray-800: #1f2937;
    --odont-gray-900: #111827;
    
    /* Glass Effects */
    --glass-bg: rgba(255, 255, 255, 0.25);
    --glass-border: rgba(255, 255, 255, 0.18);
    --glass-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
    
    /* Spacing */
    --spacing-xs: 0.25rem;
    --spacing-sm: 0.5rem;
    --spacing-md: 1rem;
    --spacing-lg: 1.5rem;
    --spacing-xl: 2rem;
    --spacing-2xl: 3rem;
    
    /* Typography */
    --font-xs: 0.75rem;
    --font-sm: 0.875rem;
    --font-base: 1rem;
    --font-lg: 1.125rem;
    --font-xl: 1.25rem;
    --font-2xl: 1.5rem;
    --font-3xl: 1.875rem;
    
    /* Shadows */
    --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    
    /* Borders */
    --border-radius-sm: 0.375rem;
    --border-radius-md: 0.5rem;
    --border-radius-lg: 0.75rem;
    --border-radius-xl: 1rem;
    --border-radius-2xl: 1.5rem;
    
    /* Transitions */
    --transition-fast: 0.15s ease-in-out;
    --transition-normal: 0.25s ease-in-out;
    --transition-slow: 0.35s ease-in-out;
}

/* Base Professional Container */
.professional-odontogram {
    width: 100%;
    max-width: 1400px;
    margin: 0 auto;
    padding: var(--spacing-xl);
    background: linear-gradient(135deg, var(--odont-gray-50) 0%, var(--odont-white) 100%);
    border-radius: var(--border-radius-2xl);
    box-shadow: var(--shadow-xl);
    position: relative;
    overflow: hidden;
}

.professional-odontogram::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--odont-primary), var(--odont-secondary), var(--odont-success));
    border-radius: var(--border-radius-2xl) var(--border-radius-2xl) 0 0;
}

/* Elegant Header with Glass Effect */
.odontogram-header-professional {
    position: relative;
    margin-bottom: var(--spacing-2xl);
    background: var(--glass-bg);
    backdrop-filter: blur(20px);
    border: 1px solid var(--glass-border);
    border-radius: var(--border-radius-xl);
    padding: var(--spacing-xl);
    box-shadow: var(--glass-shadow);
    overflow: hidden;
}

.header-glass-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(0, 102, 204, 0.05) 0%, rgba(99, 102, 241, 0.05) 100%);
    pointer-events: none;
}

.header-content-professional {
    position: relative;
    display: flex;
    justify-content: space-between;
    align-items: center;
    z-index: 2;
}

.header-left {
    display: flex;
    align-items: center;
    gap: var(--spacing-lg);
}

.header-icon-wrapper {
    width: 4rem;
    height: 4rem;
    background: linear-gradient(135deg, var(--odont-primary), var(--odont-primary-light));
    border-radius: var(--border-radius-xl);
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: var(--shadow-lg);
}

.header-icon {
    width: 2rem;
    height: 2rem;
    color: var(--odont-white);
}

.header-text {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-xs);
}

.header-title {
    font-size: var(--font-2xl);
    font-weight: 700;
    color: var(--odont-gray-900);
    margin: 0;
    background: linear-gradient(135deg, var(--odont-gray-900), var(--odont-gray-700));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.header-subtitle {
    font-size: var(--font-sm);
    color: var(--odont-gray-600);
    margin: 0;
}

.header-actions-professional {
    display: flex;
    gap: var(--spacing-md);
}

/* Professional Buttons */
.btn-professional {
    display: inline-flex;
    align-items: center;
    gap: var(--spacing-sm);
    padding: var(--spacing-sm) var(--spacing-lg);
    border: none;
    border-radius: var(--border-radius-lg);
    font-weight: 600;
    font-size: var(--font-sm);
    cursor: pointer;
    transition: all var(--transition-normal);
    position: relative;
    overflow: hidden;
}

.btn-professional::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left var(--transition-slow);
}

.btn-professional:hover::before {
    left: 100%;
}

.btn-primary {
    background: linear-gradient(135deg, var(--odont-primary), var(--odont-primary-light));
    color: var(--odont-white);
    box-shadow: var(--shadow-md);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

.btn-secondary {
    background: var(--odont-white);
    color: var(--odont-gray-700);
    border: 1px solid var(--odont-gray-300);
    box-shadow: var(--shadow-sm);
}

.btn-secondary:hover {
    background: var(--odont-gray-50);
    border-color: var(--odont-gray-400);
    transform: translateY(-1px);
}

.btn-danger {
    background: linear-gradient(135deg, var(--odont-danger), #dc2626);
    color: var(--odont-white);
    box-shadow: var(--shadow-md);
}

.btn-danger:hover {
    transform: translateY(-2px);
    opacity: 0.9;
}

.btn-icon {
    width: 1rem;
    height: 1rem;
}

/* Professional Type Selector */
.odontogram-types-professional {
    margin-bottom: var(--spacing-2xl);
}

.types-header {
    text-align: center;
    margin-bottom: var(--spacing-xl);
}

.types-title {
    font-size: var(--font-xl);
    font-weight: 700;
    color: var(--odont-gray-900);
    margin: 0 0 var(--spacing-sm);
}

.types-description {
    font-size: var(--font-base);
    color: var(--odont-gray-600);
    margin: 0;
}

.types-cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: var(--spacing-lg);
}

.type-card-professional {
    background: var(--odont-white);
    border: 2px solid var(--odont-gray-200);
    border-radius: var(--border-radius-xl);
    padding: var(--spacing-xl);
    cursor: pointer;
    transition: all var(--transition-normal);
    position: relative;
    overflow: hidden;
    box-shadow: var(--shadow-sm);
}

.type-card-professional::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--odont-primary), var(--odont-secondary));
    transform: scaleX(0);
    transform-origin: left;
    transition: transform var(--transition-normal);
}

.type-card-professional:hover::before,
.type-card-professional.active::before {
    transform: scaleX(1);
}

.type-card-professional:hover {
    border-color: var(--odont-primary);
    box-shadow: var(--shadow-lg);
    transform: translateY(-4px);
}

.type-card-professional.active {
    border-color: var(--odont-primary);
    background: linear-gradient(135deg, var(--odont-primary-alpha), rgba(99, 102, 241, 0.05));
    box-shadow: var(--shadow-lg);
}

.type-card-icon {
    width: 3rem;
    height: 3rem;
    background: linear-gradient(135deg, var(--odont-primary), var(--odont-primary-light));
    border-radius: var(--border-radius-lg);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: var(--spacing-lg);
    box-shadow: var(--shadow-md);
}

.type-card-icon svg {
    width: 1.5rem;
    height: 1.5rem;
    color: var(--odont-white);
}

.type-card-content {
    margin-bottom: var(--spacing-lg);
}

.type-card-title {
    font-size: var(--font-lg);
    font-weight: 700;
    color: var(--odont-gray-900);
    margin: 0 0 var(--spacing-sm);
}

.type-card-description {
    font-size: var(--font-sm);
    color: var(--odont-gray-600);
    margin: 0 0 var(--spacing-sm);
}

.type-card-detail {
    font-size: var(--font-xs);
    color: var(--odont-gray-500);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.type-card-indicator {
    position: absolute;
    top: var(--spacing-lg);
    right: var(--spacing-lg);
    width: 1rem;
    height: 1rem;
    border-radius: 50%;
    background: var(--odont-gray-300);
    transition: all var(--transition-normal);
}

.type-card-professional.active .type-card-indicator {
    background: var(--odont-primary);
    box-shadow: 0 0 0 4px rgba(0, 102, 204, 0.2);
}

/* Professional Legend */
.legend-professional {
    margin-bottom: var(--spacing-2xl);
    background: var(--odont-white);
    border: 1px solid var(--odont-gray-200);
    border-radius: var(--border-radius-xl);
    overflow: hidden;
    box-shadow: var(--shadow-sm);
}

.legend-header {
    padding: var(--spacing-lg) var(--spacing-xl);
    background: linear-gradient(135deg, var(--odont-gray-50), var(--odont-white));
    border-bottom: 1px solid var(--odont-gray-200);
}

.legend-title {
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
    font-size: var(--font-lg);
    font-weight: 700;
    color: var(--odont-gray-900);
    margin: 0 0 var(--spacing-xs);
}

.legend-icon {
    width: 1.25rem;
    height: 1.25rem;
    color: var(--odont-primary);
}

.legend-description {
    font-size: var(--font-sm);
    color: var(--odont-gray-600);
    margin: 0;
}

.legend-grid-professional {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: var(--spacing-md);
    padding: var(--spacing-xl);
}

.legend-item-professional {
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
    padding: var(--spacing-md);
    border: 1px solid var(--odont-gray-200);
    border-radius: var(--border-radius-lg);
    cursor: pointer;
    transition: all var(--transition-normal);
    position: relative;
    overflow: hidden;
}

.legend-item-professional:hover {
    border-color: var(--odont-primary);
    box-shadow: var(--shadow-md);
    transform: translateY(-2px);
}

.legend-item-professional.active {
    border-color: var(--odont-primary);
    background: var(--odont-primary-alpha);
    box-shadow: var(--shadow-md);
}

.legend-icon-wrapper {
    width: 3rem;
    height: 3rem;
    border: 2px solid;
    border-radius: var(--border-radius-lg);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.legend-status-icon {
    width: 1.5rem;
    height: 1.5rem;
    border-radius: var(--border-radius-sm);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--odont-white);
    font-size: var(--font-xs);
    font-weight: 700;
}

.legend-content {
    flex: 1;
}

.legend-item-title {
    font-size: var(--font-sm);
    font-weight: 600;
    color: var(--odont-gray-900);
    margin: 0 0 var(--spacing-xs);
}

.legend-item-description {
    font-size: var(--font-xs);
    color: var(--odont-gray-600);
    margin: 0;
}

.legend-item-indicator {
    position: absolute;
    top: var(--spacing-sm);
    right: var(--spacing-sm);
    width: 0.5rem;
    height: 0.5rem;
    border-radius: 50%;
    background: var(--odont-gray-300);
    transition: all var(--transition-normal);
}

.legend-item-professional.active .legend-item-indicator {
    background: var(--odont-primary);
    box-shadow: 0 0 0 2px rgba(0, 102, 204, 0.2);
}

/* Professional Odontogram Grid */
.odontogram-grid-professional {
    background: var(--odont-white);
    border: 1px solid var(--odont-gray-200);
    border-radius: var(--border-radius-xl);
    overflow: hidden;
    box-shadow: var(--shadow-md);
}

.grid-header {
    padding: var(--spacing-lg) var(--spacing-xl);
    background: linear-gradient(135deg, var(--odont-gray-50), var(--odont-white));
    border-bottom: 1px solid var(--odont-gray-200);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.grid-title {
    font-size: var(--font-xl);
    font-weight: 700;
    color: var(--odont-gray-900);
    margin: 0;
}

.grid-subtitle {
    font-size: var(--font-sm);
    color: var(--odont-gray-600);
    font-weight: 500;
    margin-left: var(--spacing-sm);
}

.grid-controls {
    display: flex;
    align-items: center;
    gap: var(--spacing-lg);
}

.grid-stats {
    display: flex;
    gap: var(--spacing-lg);
}

.stat-item {
    text-align: center;
}

.stat-number {
    display: block;
    font-size: var(--font-lg);
    font-weight: 700;
    color: var(--odont-primary);
}

.stat-label {
    font-size: var(--font-xs);
    color: var(--odont-gray-600);
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

/* Professional Teeth Grid */
.teeth-grid-professional {
    padding: var(--spacing-xl);
}

.jaw-section {
    margin-bottom: var(--spacing-2xl);
}

.jaw-section:last-child {
    margin-bottom: 0;
}

.jaw-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: var(--spacing-lg);
    padding-bottom: var(--spacing-md);
    border-bottom: 2px solid var(--odont-gray-200);
}

.jaw-title {
    font-size: var(--font-lg);
    font-weight: 700;
    color: var(--odont-gray-900);
    margin: 0;
}

.jaw-controls {
    display: flex;
    gap: var(--spacing-sm);
}

.jaw-control-btn {
    padding: var(--spacing-xs) var(--spacing-md);
    border: 1px solid var(--odont-gray-300);
    background: var(--odont-white);
    color: var(--odont-gray-700);
    border-radius: var(--border-radius-md);
    font-size: var(--font-xs);
    cursor: pointer;
    transition: all var(--transition-fast);
}

.jaw-control-btn:hover {
    border-color: var(--odont-primary);
    color: var(--odont-primary);
}

.teeth-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(80px, 1fr));
    gap: var(--spacing-md);
    max-width: 100%;
    margin: 0 auto;
}

/* Professional Tooth Design */
.tooth-professional {
    background: var(--odont-white);
    border: 2px solid var(--odont-gray-200);
    border-radius: var(--border-radius-lg);
    padding: var(--spacing-sm);
    cursor: pointer;
    transition: all var(--transition-normal);
    position: relative;
    min-height: 120px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    box-shadow: var(--shadow-sm);
}

.tooth-professional:hover {
    border-color: var(--odont-primary);
    box-shadow: var(--shadow-lg);
    transform: translateY(-2px);
}

.tooth-professional.has-selections {
    border-color: var(--odont-primary);
    background-color: var(--odont-primary-alpha);
}

.tooth-number-professional {
    font-size: var(--font-xs);
    font-weight: 700;
    color: var(--odont-gray-700);
    margin-bottom: var(--spacing-xs);
    text-align: center;
}

.tooth-faces-professional {
    display: grid;
    grid-template-columns: 1fr 1fr;
    grid-template-rows: auto auto auto;
    gap: 2px;
    width: 100%;
    max-width: 60px;
    margin-bottom: var(--spacing-xs);
}

.tooth-face {
    width: 100%;
    height: 16px;
    border: 1px solid var(--odont-gray-300);
    border-radius: var(--border-radius-sm);
    background: var(--odont-gray-100);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all var(--transition-fast);
    position: relative;
    overflow: hidden;
}

.tooth-face:hover {
    border-color: var(--odont-primary);
    transform: scale(1.05);
}

.tooth-face.selected {
    border-width: 2px;
    box-shadow: 0 0 0 2px rgba(0, 102, 204, 0.2);
}

.oclusal-face {
    grid-column: 1 / -1;
    grid-row: 1;
}

.vestibular-face {
    grid-column: 1;
    grid-row: 2;
}

.central-face {
    grid-column: 2;
    grid-row: 2;
}

.lingual-face {
    grid-column: 1;
    grid-row: 3;
}

.mesial-face {
    grid-column: 2;
    grid-row: 3;
}

.face-label {
    font-size: 8px;
    font-weight: 700;
    color: var(--odont-gray-600);
    pointer-events: none;
}

.tooth-type-indicator {
    font-size: var(--font-xs);
    color: var(--odont-gray-500);
    text-align: center;
    font-weight: 500;
}

/* Professional Face Tooltip */
.face-tooltip-professional {
    position: fixed;
    z-index: 1000;
    padding: var(--spacing-sm) var(--spacing-md);
    background: var(--odont-gray-900);
    color: var(--odont-white);
    border-radius: var(--border-radius-md);
    font-size: var(--font-sm);
    box-shadow: var(--shadow-xl);
    pointer-events: none;
    transform: translateX(-50%) translateY(-100%);
    margin-top: -8px;
}

.face-tooltip-professional::after {
    content: '';
    position: absolute;
    top: 100%;
    left: 50%;
    transform: translateX(-50%);
    border: 4px solid transparent;
    border-top-color: var(--odont-gray-900);
}

.tooltip-content {
    text-align: center;
}

.tooltip-title {
    font-weight: 700;
    margin: 0 0 var(--spacing-xs);
}

.tooltip-description {
    font-size: var(--font-xs);
    opacity: 0.9;
    margin: 0;
}

/* Professional Summary Panel */
.summary-panel-professional {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 500px;
    max-width: 90vw;
    max-height: 80vh;
    background: var(--odont-white);
    border-radius: var(--border-radius-xl);
    box-shadow: var(--shadow-xl);
    z-index: 1000;
    overflow: hidden;
}

.summary-header {
    padding: var(--spacing-lg) var(--spacing-xl);
    background: linear-gradient(135deg, var(--odont-primary), var(--odont-primary-light));
    color: var(--odont-white);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.summary-title {
    font-size: var(--font-lg);
    font-weight: 700;
    margin: 0;
}

.close-summary-btn {
    width: 2rem;
    height: 2rem;
    border: none;
    background: rgba(255, 255, 255, 0.2);
    color: var(--odont-white);
    border-radius: var(--border-radius-md);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all var(--transition-fast);
}

.close-summary-btn:hover {
    background: rgba(255, 255, 255, 0.3);
}

.close-summary-btn svg {
    width: 1rem;
    height: 1rem;
}

.summary-content {
    padding: var(--spacing-xl);
}

.summary-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: var(--spacing-lg);
}

.stat-card {
    text-align: center;
    padding: var(--spacing-lg);
    background: var(--odont-gray-50);
    border-radius: var(--border-radius-lg);
    border: 1px solid var(--odont-gray-200);
}

.stat-number {
    font-size: var(--font-2xl);
    font-weight: 700;
    color: var(--odont-primary);
    display: block;
    margin-bottom: var(--spacing-xs);
}

.stat-label {
    font-size: var(--font-sm);
    color: var(--odont-gray-600);
    font-weight: 500;
}

/* Responsive Design */
@media (max-width: 768px) {
    .professional-odontogram {
        padding: var(--spacing-lg);
    }
    
    .header-content-professional {
        flex-direction: column;
        gap: var(--spacing-lg);
        text-align: center;
    }
    
    .types-cards-grid {
        grid-template-columns: 1fr;
    }
    
    .legend-grid-professional {
        grid-template-columns: 1fr;
    }
    
    .teeth-row {
        grid-template-columns: repeat(auto-fit, minmax(60px, 1fr));
        gap: var(--spacing-sm);
    }
    
    .tooth-professional {
        min-height: 100px;
        padding: var(--spacing-xs);
    }
    
    .tooth-faces-professional {
        max-width: 50px;
    }
    
    .tooth-face {
        height: 14px;
    }
    
    .grid-header {
        flex-direction: column;
        gap: var(--spacing-md);
        text-align: center;
    }
}

/* Animation Keyframes */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.7;
    }
}

@keyframes slideInRight {
    from {
        opacity: 0;
        transform: translateX(20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

/* Animation Classes */
.animate-fade-in-up {
    animation: fadeInUp 0.5s ease-out;
}

.animate-pulse {
    animation: pulse 2s infinite;
}

.animate-slide-in-right {
    animation: slideInRight 0.3s ease-out;
}

/* Print Styles */
@media print {
    .professional-odontogram {
        box-shadow: none;
        border: 1px solid var(--odont-gray-300);
    }
    
    .header-actions-professional,
    .jaw-controls,
    .btn-professional {
        display: none !important;
    }
}

/* ============================================
   READONLY MODE SPECIFIC STYLES
   ============================================ */

.professional-odontogram.readonly-mode {
    /* Indica que es de solo lectura con un estilo más sutil */
    background: linear-gradient(135deg, var(--odont-gray-50) 0%, var(--odont-white) 100%);
    border: 2px solid var(--odont-gray-200);
}

.readonly-mode .header-subtitle::after {
    content: " (Solo Lectura)";
    color: var(--odont-warning);
    font-weight: 600;
}

.readonly-mode .types-description {
    color: var(--odont-gray-500);
    font-style: italic;
}

.readonly-mode .legend-description {
    color: var(--odont-gray-500);
    font-style: italic;
}

/* Estilos para dientes en modo readonly */
.tooth-professional.readonly {
    cursor: default;
    position: relative;
}

.tooth-professional.readonly::before {
    content: "";
    position: absolute;
    top: -2px;
    left: -2px;
    right: -2px;
    bottom: -2px;
    border: 1px dashed var(--odont-gray-300);
    border-radius: 8px;
    opacity: 0.5;
    pointer-events: none;
}

.tooth-face.readonly {
    cursor: default;
    position: relative;
}

.tooth-face.readonly:hover {
    transform: none;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.tooth-face.readonly .face-label {
    pointer-events: none;
}

/* Controles de solo lectura */
.jaw-info {
    font-size: var(--font-sm);
    color: var(--odont-gray-500);
    font-style: italic;
    padding: var(--spacing-sm) var(--spacing-md);
    background: var(--odont-gray-100);
    border-radius: var(--border-radius-sm);
}

/* Tooltip mejorado para readonly */
.readonly-mode .face-tooltip-professional {
    background: rgba(0, 0, 0, 0.9);
    color: white;
    border: none;
}

.readonly-mode .tooltip-status {
    margin-top: var(--spacing-xs);
    padding-top: var(--spacing-xs);
    border-top: 1px solid rgba(255, 255, 255, 0.2);
    font-weight: 600;
    color: var(--odont-primary-light);
}

/* Ajustes en la leyenda para readonly */
.readonly-mode .legend-item-professional {
    cursor: default;
    opacity: 0.9;
}

.readonly-mode .legend-item-professional:hover {
    transform: none;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

/* Cards de tipo para readonly */
.readonly-mode .type-card-professional {
    cursor: pointer;
    opacity: 0.9;
}

.readonly-mode .type-card-professional:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 102, 204, 0.15);
}

/* Botones específicos para readonly */
.readonly-mode .btn-professional.btn-secondary,
.readonly-mode .btn-professional.btn-primary {
    display: inline-flex !important;
}

.readonly-mode .btn-professional.btn-danger {
    display: none !important;
}

/* Estadísticas en readonly */
.readonly-mode .stat-label {
    color: var(--odont-gray-600);
}

.readonly-mode .stat-number {
    color: var(--odont-primary);
}

/* Indicador visual de modo readonly */
.readonly-mode::before {
    content: "SOLO LECTURA";
    position: absolute;
    top: 10px;
    right: 10px;
    background: var(--odont-warning);
    color: white;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 10px;
    font-weight: 600;
    z-index: 1000;
    opacity: 0.8;
}

    </style>
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
                    const data = JSON.stringify(this.odontogramState, null, 2);
                    const blob = new Blob([data], { type: 'application/json' });
                    const url = URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = 'odontograma-profesional.json';
                    a.click();
                    URL.revokeObjectURL(url);
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
                            if (tooth.faces) {
                                Object.values(tooth.faces).forEach(faceStatus => {
                                    if (faceStatus === status) count++;
                                });
                            }
                        });
                    });
                    return count;
                }
            }
        }
    </script>
</x-dynamic-component>
