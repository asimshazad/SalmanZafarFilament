<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmailTemplateResource\Pages;
use App\Filament\Resources\EmailTemplateResource\RelationManagers;
use App\Models\EmailTemplate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Table;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Gate;

class EmailTemplateResource extends Resource
{
    protected static ?string $model = EmailTemplate::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox-stack';
    protected static ?string $navigationGroup = 'Site Management';
    
    // protected static bool $shouldRegisterNavigation = false; // we can use below function

    public static function shouldRegisterNavigation(): bool
    {
        return Gate::allows('View emailtemplate');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('key')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->columnSpan(12)
                    ->maxLength(255),
                Forms\Components\TextInput::make('email_subject')
                    ->required()
                    ->columnSpan(12)
                    ->maxLength(255),
                Forms\Components\TextInput::make('from_email')
                    ->required()
                    ->columnSpan(12)
                    ->maxLength(255),
                Forms\Components\TextInput::make('from_name')
                    ->required()
                    ->columnSpan(12)
                    ->maxLength(255),
                Forms\Components\Select::make('cc_senders')
                    ->required()
                    ->multiple()
                    ->columnSpan(12)
                    ->options([
                        'asim.shahzad20@gmail.com' => 'asim.shahzad20@gmail.com',
                        'ahmadwaqas008@gmail.com' => 'ahmadwaqas008@gmail.com',
                    ]),
                Forms\Components\Toggle::make('status')
                    ->label('Active')
                    ->columnSpan(12)
                    ->default(true),
                Forms\Components\RichEditor::make('content')
                    ->columnSpan(12)
                    ->placeholder('Enter emailtemplate content here...')
                    ->disableToolbarButtons([
                        'attachFiles',
                        'strike',
                    ]),
            ]);
    }

    // public static function getNavigationBadge(): ?string
    // {
    //     return static::getModel()::count();
    // }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')->searchable(),
                Tables\Columns\TextColumn::make('email_subject'),
                Tables\Columns\BooleanColumn::make('status'),
                Tables\Columns\TextColumn::make('created_at')->dateTime(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
               // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                //Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListEmailTemplates::route('/'),
            'create' => Pages\CreateEmailTemplate::route('/create'),
            'edit' => Pages\EditEmailTemplate::route('/{record}/edit'),
        ];
    }
}
