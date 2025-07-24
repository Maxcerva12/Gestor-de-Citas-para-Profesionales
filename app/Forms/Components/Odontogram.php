<?php

namespace App\Forms\Components;

use Filament\Forms\Components\Field;

class Odontogram extends Field
{
    protected string $view = 'forms.components.odontogram';

    protected bool $showPermanent = true;
    protected bool $showTemporary = true;
    protected array $toothStatuses = [
        'healthy' => ['label' => 'Sano', 'color' => '#10B981'],
        'cavity' => ['label' => 'Caries', 'color' => '#EF4444'],
        'treated' => ['label' => 'Tratado', 'color' => '#3B82F6'],
        'missing' => ['label' => 'Ausente', 'color' => '#6B7280'],
        'implant' => ['label' => 'Implante', 'color' => '#8B5CF6'],
        'crown' => ['label' => 'Corona', 'color' => '#F59E0B'],
        'root_canal' => ['label' => 'Endodoncia', 'color' => '#EC4899'],
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

    public function getShowPermanent(): bool
    {
        return $this->showPermanent;
    }

    public function getShowTemporary(): bool
    {
        return $this->showTemporary;
    }

    public function getToothStatuses(): array
    {
        return $this->toothStatuses;
    }
}
