<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\ListRecords;
use AymanAlhattami\FilamentPageWithSidebar\Traits\HasPageSidebar;
use Illuminate\Database\Eloquent\Builder;

class AdminStaffList extends ListRecords
{
    use HasPageSidebar;

    protected static string $resource = UserResource::class;

    public $record;

    public function mount($record = null): void
    {
        parent::mount();
        $this->record = $record; // Inicializa la propiedad $record
    }

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()->whereHas('roles', function (Builder $query) {
            $query->whereIn('name', [
                'recepcionista',
                'secretaria',
                'asesor-comercial',
                'contador'
            ]);
        });
    }
}
