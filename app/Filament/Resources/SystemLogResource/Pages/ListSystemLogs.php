<?php

namespace App\Filament\Resources\SystemLogResource\Pages;

use App\Filament\Resources\SystemLogResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListSystemLogs extends ListRecords
{
    protected static string $resource = SystemLogResource::class;

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('전체'),
            'errors' => Tab::make('에러만')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('level', 'error')),
            'scheduler' => Tab::make('스케줄러')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('category', 'scheduler')),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
