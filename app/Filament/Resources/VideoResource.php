<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VideoResource\Pages;
use App\Models\Video;
use App\Models\User;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class VideoResource extends Resource
{
    protected static ?string $model = Video::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Videoclipuri';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->check(); // toți utilizatorii logați pot vedea meniul
    }

    public static function canViewAny(): bool
    {
        return auth()->check(); // oricine logat poate vedea lista
    }

    public static function canView(Model $record): bool
    {
        return auth()->check(); // oricine logat poate vedea înregistrările
    }

    public static function canCreate(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')->required()->maxLength(255),
            Textarea::make('description')->rows(4)->maxLength(65535),
            TextInput::make('length')->label('Length (seconds)')->numeric()->minValue(0)->required(),
            Select::make('type')->options(Video::TYPES)->required()->searchable(),
            FileUpload::make('image')->image()->maxSize(1024)->directory('videos/images')->required(),
            FileUpload::make('video')
                ->label('Video')
                ->acceptedFileTypes(['video/mp4', 'video/avi', 'video/mpeg', 'video/webm'])
                ->directory('videos')
                ->maxSize(102400),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')->label('Thumbnail')->rounded()->width(100)->height(60),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('description')->limit(50),
                TextColumn::make('length')->label('Length (s)')->sortable(),
                TextColumn::make('type')->sortable()->formatStateUsing(fn ($state) => \App\Models\Video::TYPES[$state] ?? $state),
            ])
            ->actions([
                EditAction::make()
                    ->visible(fn () => auth()->check() && auth()->user()->isAdmin()),

                Action::make('watch')
                    ->label('Watch')
                    ->button()
                    ->color('primary')
                    ->modalHeading(fn ($record) => "Watch Video: {$record->name}")
                    ->modalContent(function ($record) {
                        return view('filament.partials.watch-video-modal', ['video' => $record]);
                    }),

                Action::make('toggleFavorite')
                    ->label(fn ($record) => auth()->user()?->favoriteVideos->contains($record) ? 'Unlike' : 'Like')
                    ->action(function ($record) {
                        $user = auth()->user();
                        if ($user->favoriteVideos->contains($record)) {
                            $user->favoriteVideos()->detach($record);
                        } else {
                            $user->favoriteVideos()->attach($record);
                        }
                    })
                    ->visible(fn () => auth()->check())
                    ->icon(fn ($record) => auth()->user()?->favoriteVideos->contains($record) ? 'heroicon-o-heart' : 'heroicon-o-heart'),

                DeleteAction::make()
                    ->visible(fn () => auth()->check() && auth()->user()->isAdmin()),
            ])

            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->visible(fn () => auth()->user()?->isAdmin()),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListVideos::route('/'),
            'create' => Pages\CreateVideo::route('/create'),
            'edit'   => Pages\EditVideo::route('/{record}/edit'),
        ];
    }
}
