<?php

namespace App\Filament\Resources;

use App\Filament\Exports\VisaTypeExporter;
use App\Filament\Resources\VisaTypeResource\Pages;
use App\Filament\Resources\VisaTypeResource\RelationManagers;
use App\Models\VisaType;
use Filament\Forms;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Gate;

class VisaTypeResource extends Resource
{
    protected static ?string $model = VisaType::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Visa';

    public static function shouldRegisterNavigation(): bool
    {
        return Gate::allows('View visatype');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('country_id')
                    ->label('Country')
                    ->columnSpan(12)
                    ->relationship('country', 'name')
                    ->required(),
                TextInput::make('title')
                    ->label('Visa Type Title')
                    ->columnSpan(12)
                    ->maxLength(255)
                    ->rules(function ($get) {
                        return [
                            'required',
                            'string',
                            'max:255',
                            Rule::unique('visa_types', 'title')
                                ->where(function ($query) use ($get) {
                                    return $query->where('country_id', $get('country_id'));
                                })
                                ->ignore($get('id')),
                        ];
                    }),
                RichEditor::make('description')
                    ->required()
                    ->columnSpan(12)
                    ->label('Description'),
                Forms\Components\Toggle::make('status')
                    ->label('Active')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('country.name')
                    ->label('Country')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('title')
                    ->label('Visa Type Title')
                    ->sortable()
                    ->searchable(),
                    IconColumn::make('status')
                    ->boolean()
                    ->label('Active'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
               // Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                //]),
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(VisaTypeExporter::class)
                    ->columnMapping(false)
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
            'index' => Pages\ListVisaTypes::route('/'),
            'create' => Pages\CreateVisaType::route('/create'),
            'edit' => Pages\EditVisaType::route('/{record}/edit'),
        ];
    }
}
