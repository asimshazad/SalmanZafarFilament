<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettingResource\Pages;
use App\Filament\Resources\SettingResource\RelationManagers;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Gate;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Settings';
    protected static ?string $navigationGroup = 'Site Management';
    // protected static ?string $slug = 'settings';

    public static function shouldRegisterNavigation(): bool
    {
        return Gate::allows('View setting');
    }
    
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->required()
                ->label('Name'),
            Forms\Components\TextInput::make('email')
                ->email()
                ->required(),
            Forms\Components\TextInput::make('site_title')
                ->required(),
            Forms\Components\FileUpload::make('site_logo')
            ->image()
            ->disk('public')
            ->directory('site_logo')
            ->nullable(),
            Forms\Components\FileUpload::make('footer_logo')
            ->image()
            ->disk('public')
            ->directory('footer_logo')
            ->nullable(),
            Forms\Components\FileUpload::make('favicon')
            ->image()
            ->disk('public')
            ->directory('favicon')
            ->nullable(),
            Forms\Components\Select::make('mail_mailer')
                ->options([
                    'smtp' => 'SMTP',
                ])
                ->default('sandbox'),
            Forms\Components\TextInput::make('mail_host'),
            Forms\Components\TextInput::make('mail_port'),
            Forms\Components\TextInput::make('mail_username'),
            Forms\Components\TextInput::make('mail_password'),
            Forms\Components\TextInput::make('mail_from_name'),
            Forms\Components\TextInput::make('mail_from_address'),
            Forms\Components\Select::make('paypal_mode')
                ->options([
                    'sandbox' => 'Sandbox',
                    'live' => 'Live',
                ])
                ->default('sandbox'),
            Forms\Components\TextInput::make('paypal_client_id'),
            Forms\Components\TextInput::make('paypal_client_secret'),
            Forms\Components\TextInput::make('paypal_webhook_secret'),
            Forms\Components\TextInput::make('paypal_app_id'),
            Forms\Components\TextInput::make('stripe_key'),
            Forms\Components\TextInput::make('stripe_secret'),
            Forms\Components\TextInput::make('stripe_webhook_secret'),
            Forms\Components\TextInput::make('open_api_assistant_id'),
            Forms\Components\TextInput::make('open_api_key'),
            Forms\Components\TextInput::make('open_api_organiztion'),
            Forms\Components\TextInput::make('gemini_api_key'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
            // ->bulkActions([
            //     Tables\Actions\BulkActionGroup::make([
            //         Tables\Actions\DeleteBulkAction::make(),
            //     ]),
            // ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getNavigationUrl(): string
    {
        $setting = Setting::first();
        if ($setting) {
            return static::getUrl('edit', ['record' => $setting->id]);
        }

        return static::getUrl('create');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSettings::route('/'),
            'create' => Pages\CreateSetting::route('/create'),
            'edit' => Pages\EditSetting::route('/{record}/edit'),
        ];
    }
}
