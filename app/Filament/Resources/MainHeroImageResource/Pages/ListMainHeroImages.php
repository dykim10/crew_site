<?php

namespace App\Filament\Resources\MainHeroImageResource\Pages;

use App\Filament\Resources\MainHeroImageResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMainHeroImages extends ListRecords
{
    protected static string $resource = MainHeroImageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('메인 이미지 등록')
                ->visible(fn () => MainHeroImageResource::canCreate()),
        ];
    }
}
