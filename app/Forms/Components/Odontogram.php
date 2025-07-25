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
        'cavity' => ['label' => 'Caries', 'color' => '#EF4444', 'icon' => '●'],
        'treated' => ['label' => 'Tratado', 'color' => '#3B82F6', 'icon' => '■'],
        'missing' => ['label' => 'Ausente', 'color' => '#6B7280', 'icon' => '✗'],
        'implant' => ['label' => 'Implante', 'color' => '#8B5CF6', 'icon' => '◆'],
        'crown' => ['label' => 'Corona', 'color' => '#F59E0B', 'icon' => '♦'],
        'root_canal' => ['label' => 'Endodoncia', 'color' => '#EC4899', 'icon' => '◊'],
        'fracture' => ['label' => 'Fractura', 'color' => '#DC2626', 'icon' => '⚡'],
        'bridge' => ['label' => 'Puente', 'color' => '#059669', 'icon' => '⌒'],
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
