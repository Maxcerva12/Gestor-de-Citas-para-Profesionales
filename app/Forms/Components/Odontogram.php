<?php

namespace App\Forms\Components;

use Filament\Forms\Components\Field;

class Odontogram extends Field
{
    protected string $view = 'forms.components.odontogram-professional';

    protected bool $showPermanent = true;
    protected bool $showTemporary = true;
    protected bool $showMixed = false;
    protected string $defaultOdontogramView = 'permanent';

    protected array $toothStatuses = [
        'healthy' => ['label' => 'Sano', 'color' => '#10B981', 'icon' => '✓'],
        'cavity' => ['label' => 'Caries', 'color' => '#EF4444', 'icon' => '<svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><rect x="4" y="4" width="13" height="13" rx="1"/></svg>'],
        'treated' => ['label' => 'Tratado', 'color' => '#3B82F6', 'icon' => '■'],
        'missing' => ['label' => 'Ausente', 'color' => '#1e3a8a', 'icon' => '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 20 20"><line x1="10" y1="2" x2="10" y2="18" stroke-width="2"/></svg>'],
        'implant' => ['label' => 'Implante', 'color' => '#8B5CF6', 'icon' => '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 20 20" stroke-width="2"><line x1="6" y1="3" x2="14" y2="3"/><line x1="10" y1="3" x2="10" y2="17"/><line x1="6" y1="17" x2="14" y2="17"/></svg>'],
        'crown' => ['label' => 'Corona', 'color' => '#F59E0B', 'icon' => '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 20 20" stroke-width="2"><circle cx="10" cy="10" r="6"/></svg>'],
        'root_canal' => ['label' => 'Endodoncia', 'color' => '#EC4899', 'icon' => '<svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M10 3 L16 15 L4 15 Z"/></svg>'],
        'fracture' => ['label' => 'Fractura', 'color' => '#DC2626', 'icon' => '<svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M14 0.5 L3 11.5 L7.5 11.5 L6.5 19.5 L17 6.5 L12.5 6.5 Z"/></svg>'],
        'bridge' => ['label' => 'Puente', 'color' => '#059669', 'icon' => '⌒'],
        'extraction_indicated' => ['label' => 'Extracción', 'color' => '#F97316', 'icon' => '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>'],
    ];

    protected array $toothFaces = [
        'oclusal' => ['label' => 'Oclusal', 'description' => 'Superficie de masticación'],
        'vestibular' => ['label' => 'Vestibular', 'description' => 'Cara hacia la mejilla'],
        'central' => ['label' => 'Central', 'description' => 'Centro del diente'],
        'lingual' => ['label' => 'Lingual', 'description' => 'Cara hacia la lengua'],
        'mesial' => ['label' => 'Mesial', 'description' => 'Superficie hacia la línea media'],
    ];

    public function showPermanent(bool $show = true): static
    {
        $this->showPermanent = $show;
        return $this;
    }

    public function showTemporary(bool $show = true): static
    {
        $this->showTemporary = $show;
        return $this;
    }

    public function showMixed(bool $show = true): static
    {
        $this->showMixed = $show;
        return $this;
    }

    public function setDefaultView(string $view = 'permanent'): static
    {
        $this->defaultOdontogramView = $view;
        return $this;
    }

    public function getShowPermanent(): bool
    {
        return $this->showPermanent;
    }

    public function getShowTemporary(): bool
    {
        return $this->showTemporary;
    }

    public function getShowMixed(): bool
    {
        return $this->showMixed;
    }

    public function getDefaultView(): string
    {
        return $this->defaultOdontogramView;
    }

    public function getToothStatuses(): array
    {
        return $this->toothStatuses;
    }

    public function getToothFaces(): array
    {
        return $this->toothFaces;
    }
}
