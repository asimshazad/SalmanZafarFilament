<?php

namespace App\Filament\Resources;

use App\Filament\Exports\UserExporter;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\Role;
use App\Models\User;
use Filament\Tables\Actions\ExportAction;
use Filament\Actions\Exports\ExportColumn;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Actions\Exports\Enums\ExportFormat;
use Illuminate\Support\Facades\Gate;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationLabel = 'Users';
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'User Management';

    public static function shouldRegisterNavigation(): bool
    {
        return Gate::allows('View user');
    }

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            // User Name
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255)
                ->label('Name'),

            // User Email
            Forms\Components\TextInput::make('email')
                ->required()
                ->email()
                ->maxLength(255)
                ->label('Email'),

            Forms\Components\FileUpload::make('user_photo')
                ->image()
                ->disk('public')
                ->directory('user_photos')
                ->nullable(),
            // User Password (only for create)
            Forms\Components\TextInput::make('password')
                ->required(fn ($context) => $context === 'create')
                ->password()
                ->minLength(8)
                ->same('password_confirmation')
                ->label('Password')
                ->revealable(),

            // Password Confirmation (only for create)
            Forms\Components\TextInput::make('password_confirmation')
                ->required(fn ($context) => $context === 'create')
                ->password()
                ->minLength(8)
                ->label('Password Confirmation')
                ->revealable(),

            // Role Selection

            Forms\Components\Select::make('roles')
                ->multiple()
                ->relationship('roles', 'name')
                ->options(Role::all()->pluck('name', 'id'))
                ->label('Assign Roles')
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('email')->sortable()->searchable(),
                TextColumn::make('roles.name')->label('Roles')->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            //    Tables\Actions\DeleteAction::make(),
            ])
            // ->bulkActions([
            //     // Tables\Actions\BulkActionGroup::make([
            //         Tables\Actions\DeleteBulkAction::make(),
            //     // ]),
            // ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(UserExporter::class)
                    ->columnMapping(false)
                    // ->formats([
                    //     ExportFormat::Xlsx,
                    //     ExportFormat::Csv,
                    // ])
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
