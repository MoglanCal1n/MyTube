<?php

namespace App\Filament\Resources;

use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\UserResource\Pages;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationLabel = 'Utilizatori';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->check();
    }

    public static function canViewAny(): bool
    {
        return auth()->check(); // toți utilizatorii logați pot vedea lista
    }

    public static function canView(Model $record): bool
    {
        return auth()->check();
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
            Forms\Components\TextInput::make('name')->required(),
            Forms\Components\TextInput::make('email')->email()->required(),
            Forms\Components\Select::make('user_type')
                ->label('User Type')
                ->options(User::TYPES)
                ->required()
                ->reactive()
                ->default(User::TYPE_USER),
            Forms\Components\Select::make('role')
                ->label('Function')
                ->options(function (callable $get) {
                    return match ($get('user_type')) {
                        User::TYPE_ADMIN => User::ADMIN_ROLES,
                        User::TYPE_USER => User::WORKER_ROLES,
                        default => [],
                    };
                })
                ->required()
                ->reactive()
                ->visible(fn (callable $get) => filled($get('user_type')))
                ->searchable(),

            Forms\Components\FileUpload::make('profile_picture')
                ->label('Profile Picture')
                ->image()
                ->openable()
                ->disk('public')
                ->visibility('public')
                ->directory('profile-pictures')
                ->maxSize(2048)
                ->avatar(),
            Forms\Components\TextInput::make('password')
                ->password()
                ->dehydrateStateUsing(fn($state) => bcrypt($state))
                ->required(fn($livewire) => $livewire instanceof Pages\CreateUser),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('email')->sortable()->searchable(),
            TextColumn::make('user_type')
                ->label('Role')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    User::TYPE_ADMIN => 'danger',
                    User::TYPE_USER => 'primary',
                    default => 'secondary',
                }),
            TextColumn::make('role')
                ->label('Function')
                ->badge()
                ->color(fn ($record) => match ($record->user_type) {
                    User::TYPE_ADMIN => 'danger', // roșu pentru admini
                    User::TYPE_USER => 'success', // verde pentru workeri
                    default => 'gray',
                }),
            Tables\Columns\ImageColumn::make('profile_picture')
                ->disk('public')
                ->label('Profile Picture')
                ->height(60)
                ->circular(),
        ])
            ->actions([
                Tables\Actions\ViewAction::make(), // Permite vizualizarea
                Tables\Actions\EditAction::make()
                    ->visible(fn() => auth()->user()->isAdmin()), // doar pentru admini
                Tables\Actions\DeleteAction::make()
                    ->visible(fn() => auth()->user()->isAdmin()), // doar pentru admini
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->visible(fn() => auth()->user()->isAdmin()), // doar pentru admini
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
