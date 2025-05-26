<?php
namespace App\Filament\Resources;

use App\Filament\Resources\FavoriteResource\Pages;
use App\Models\Favorite;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;

class FavoriteResource extends Resource
{
    protected static ?string $model = Favorite::class;
    protected static ?string $navigationIcon = 'heroicon-o-heart';
    protected static ?string $navigationLabel = 'Favorite';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->check();
    }

    public static function canViewAny(): bool
    {
        return auth()->check();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(Favorite::query()->where('user_id', auth()->id())) // doar favoritele userului curent
            ->actions([
                Action::make('watch')
                    ->label('Watch')
                    ->button()
                    ->color('primary')
                    ->modalHeading(fn ($record) => "Watch Video: {$record->name}")
                    ->modalContent(function ($record) {
                        return view('filament.partials.watch-video-modal', ['video' => $record]);
                    }),
            ])
            ->columns([
                ImageColumn::make('video.image')
                    ->label('Thumbnail')
                    ->height(60)
                    ->rounded(),

                TextColumn::make('video.name')
                    ->label('Video Name'),

                TextColumn::make('video.description')
                    ->limit(50)
                    ->label('Description'),
            ]);

    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFavorites::route('/'),
        ];
    }
}
