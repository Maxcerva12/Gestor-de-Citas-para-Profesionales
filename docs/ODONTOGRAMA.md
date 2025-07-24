# 🦷 Odontograma Digital Profesional

Sistema completo de odontograma digital integrado con FilamentPHP para la gestión profesional de historiales dentales.

## 📋 Características Principales

### ✨ Funcionalidades Generales

-   **Sistema FDI Completo**: Soporte completo para numeración FDI de dientes permanentes (32) y temporales (20)
-   **Interfaz Interactiva**: Click para cambiar estados de dientes con animaciones suaves
-   **Estados Múltiples**: 7 estados dentales diferentes (sano, caries, tratado, ausente, implante, corona, endodoncia)
-   **Responsive Design**: Adaptado para desktop, tablet y móvil
-   **Accesibilidad**: Cumple estándares WCAG para usuarios con discapacidades
-   **Modo Oscuro**: Soporte automático para modo oscuro del sistema

### 🎨 Interfaz de Usuario

-   **Diseño Moderno**: Gradientes y sombras profesionales
-   **Animaciones Fluidas**: Transiciones CSS suaves y efectos hover
-   **Tooltips Informativos**: Información contextual al interactuar
-   **Leyenda Visual**: Código de colores claro y comprensible
-   **Estadísticas en Tiempo Real**: Contadores automáticos por estado dental

### 💾 Almacenamiento de Datos

-   **Base de Datos JSONB**: Uso eficiente de PostgreSQL JSONB para flexibilidad
-   **Metadatos Completos**: Timestamps, versiones y notas por diente
-   **Validación Automática**: Verificación de integridad de datos
-   **Observadores**: Logging automático de cambios

## 🚀 Instalación y Configuración

### Prerrequisitos

-   Laravel 12+
-   PostgreSQL 12+
-   FilamentPHP 3.x
-   Node.js 18+

### Pasos de Instalación

1. **Migración de Base de Datos**

```bash
php artisan migrate
```

2. **Compilar Assets**

```bash
npm install
npm run build
```

3. **Poblar Datos de Prueba (Opcional)**

```bash
php artisan db:seed --class=OdontogramSeeder
```

## 📖 Uso del Sistema

### En el Panel de Administración

1. **Acceder al Odontograma**

    - Ir a Clientes → Editar Cliente → Tab "Odontograma"

2. **Interactuar con Dientes**

    - Hacer clic en cualquier diente para cambiar su estado
    - Los estados rotan: Sano → Caries → Tratado → Ausente → Implante → Corona → Endodoncia → Sano

3. **Gestionar Información Dental**
    - Agregar fecha de última visita
    - Incluir notas dentales generales
    - Ver estadísticas automáticas

### Estados de Dientes Disponibles

| Estado        | Color    | Descripción                |
| ------------- | -------- | -------------------------- |
| 🟢 Sano       | Verde    | Diente en estado saludable |
| 🔴 Caries     | Rojo     | Presenta caries dental     |
| 🔵 Tratado    | Azul     | Tratamiento realizado      |
| ⚫ Ausente    | Gris     | Diente ausente             |
| 🟣 Implante   | Púrpura  | Implante dental            |
| 🟡 Corona     | Amarillo | Corona dental              |
| 🌸 Endodoncia | Rosa     | Tratamiento de conducto    |

## 🛠️ Comandos Artisan

### Gestión de Odontogramas

```bash
# Inicializar odontogramas vacíos para clientes sin odontograma
php artisan odontogram:manage init

# Validar odontogramas existentes
php artisan odontogram:manage validate

# Validar odontograma de cliente específico
php artisan odontogram:manage validate --client=1

# Exportar odontograma de un cliente
php artisan odontogram:manage export --client=1 --format=json --output=odontogram_cliente_1.json

# Ver estadísticas globales
php artisan odontogram:manage stats

# Ver estadísticas de cliente específico
php artisan odontogram:manage stats --client=1
```

## 🧩 Estructura del Código

### Componentes Principales

```
app/
├── Forms/Components/Odontogram.php          # Componente Filament personalizado
├── Services/OdontogramService.php           # Lógica de negocio
├── Observers/ClientOdontogramObserver.php   # Observer para eventos
├── Console/Commands/OdontogramCommand.php   # Comandos Artisan
└── Providers/OdontogramServiceProvider.php  # Service Provider

resources/
├── views/forms/components/odontogram.blade.php  # Vista del componente
└── css/odontogram.css                           # Estilos específicos

database/
└── seeders/OdontogramSeeder.php             # Datos de prueba
```

### Estructura de Datos JSON

```json
{
    "permanent": {
        "11": {
            "status": "healthy",
            "notes": "Observaciones específicas",
            "updatedAt": "2025-01-15T10:30:00Z"
        }
    },
    "temporary": {
        "51": {
            "status": "cavity",
            "notes": "Caries inicial",
            "updatedAt": "2025-01-15T10:30:00Z"
        }
    },
    "metadata": {
        "created_at": "2025-01-01T00:00:00Z",
        "last_updated": "2025-01-15T10:30:00Z",
        "version": "1.0"
    }
}
```

## 🎯 Personalización

### Agregar Nuevos Estados

1. **Actualizar el Componente**

```php
// En app/Forms/Components/Odontogram.php
protected array $toothStatuses = [
    'healthy' => ['label' => 'Sano', 'color' => '#10B981'],
    'custom_state' => ['label' => 'Estado Personalizado', 'color' => '#FF6B6B'],
    // ... otros estados
];
```

2. **Actualizar Estilos CSS**

```css
/* En resources/css/odontogram.css */
.tooth-custom-state {
    fill: #ff6b6b;
}
```

### Modificar Colores y Estilos

Los colores y estilos están centralizados en:

-   `resources/css/odontogram.css` - Estilos principales
-   `app/Forms/Components/Odontogram.php` - Configuración de colores

### Personalizar Funcionalidades

```php
// Ejemplo: Agregar validaciones personalizadas
// En app/Services/OdontogramService.php

public static function customValidation(array $odontogram): array
{
    $errors = [];

    // Tu lógica de validación personalizada

    return $errors;
}
```

## 📊 Estadísticas y Reportes

### Datos Disponibles

El sistema proporciona automáticamente:

-   Total de dientes por estado
-   Histórico de cambios (via observadores)
-   Metadatos de última actualización
-   Notas por diente individual

### Exportación de Datos

```bash
# Exportar como JSON
php artisan odontogram:manage export --client=1 --format=json

# Exportar como CSV
php artisan odontogram:manage export --client=1 --format=csv
```

## 🔧 Solución de Problemas

### Problemas Comunes

1. **Los estilos no se cargan**

    - Verificar que `npm run build` haya ejecutado correctamente
    - Confirmar que `resources/css/odontogram.css` esté importado

2. **Datos no se guardan**

    - Verificar que la migración se haya ejecutado
    - Confirmar que el campo `odontogram` está en `$fillable` del modelo

3. **Errores de JavaScript**
    - Verificar que Alpine.js esté cargado
    - Confirmar sintaxis en la vista Blade

### Logs de Depuración

Los cambios en odontogramas se registran automáticamente:

```bash
tail -f storage/logs/laravel.log | grep "Odontograma"
```

## 🤝 Contribución

### Estructura de Commits

-   `feat: ` - Nuevas características
-   `fix: ` - Correcciones de errores
-   `docs: ` - Actualización de documentación
-   `style: ` - Cambios de estilo/formato
-   `refactor: ` - Refactorización de código

### Testing

```bash
# Ejecutar tests relacionados con odontograma
php artisan test --filter=Odontogram
```

## 📄 Licencia

Este sistema de odontograma está desarrollado para uso profesional en aplicaciones médicas/dentales. Asegúrate de cumplir con las regulaciones locales de datos médicos (HIPAA, GDPR, etc.).

## 🆘 Soporte

Para soporte técnico:

1. Revisar esta documentación
2. Verificar logs de Laravel
3. Revisar configuración de base de datos
4. Confirmar permisos de FilamentPHP

---

**Desarrollado con ❤️ para profesionales de la salud dental**
