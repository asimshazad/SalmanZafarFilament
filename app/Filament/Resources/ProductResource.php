<?php

namespace App\Filament\Resources;

use App\Filament\Exports\ProductExporter;
use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
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
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\BooleanConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\NumberConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationGroup = 'Inventory Management';
    
    // protected static bool $shouldRegisterNavigation = false; // we can use below function

    public static function shouldRegisterNavigation(): bool
    {
        return Gate::allows('View product');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->columnSpan(12)
                    ->maxLength(255),
                Forms\Components\RichEditor::make('description')
                    ->maxLength(65535)
                    ->columnSpan(12)
                    ->placeholder('Enter product description here...'),
                Forms\Components\FileUpload::make('product_photo')
                    ->image()
                    ->disk('public')
                    ->directory('product_photos')
                    ->nullable(),
                Forms\Components\Toggle::make('is_free')
                    ->label('Is Free')
                    ->columnSpan(12)
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $set('price', 0);
                            $set('discount', 0);
                        }
                    }),
                Forms\Components\TextInput::make('price')
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->required()
                    ->disabled(fn ($get) => $get('is_free'))
                    ->rules(['gte:0']),
                Forms\Components\TextInput::make('discount')
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->required()
                    ->disabled(fn ($get) => $get('is_free'))
                    ->rules(function ($get) {
                        return [
                            'gte:0',
                            Rule::when(
                                !$get('is_free'), 
                                ['lte:' . $get('price')]
                            )
                        ];
                    }),
                Forms\Components\Toggle::make('status')
                    ->label('Active')
                    ->columnSpan(12)
                    ->default(true),
                Forms\Components\Hidden::make('created_by_id')
                    ->default(auth()->id()),
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
                // 'product_photo'
                Tables\Columns\ImageColumn::make('product_photo')->label('Image')->disk('public'),
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('slug')->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->searchable()
                    ->html()
                    ->copyable()
                    ->copyMessage("Copied Successfully")
                    ->wrap(true)
                    ->lineClamp(3)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('price')->money('usd')->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('discount')
                    ->money('usd')->searchable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                Tables\Columns\IconColumn::make('is_free')->boolean()->searchable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('status')->boolean()->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->date()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                QueryBuilder::make()
                    ->constraints([
                        TextConstraint::make('name'),
                        TextConstraint::make('slug'),
                        TextConstraint::make('description'),
                        NumberConstraint::make('price')
                            ->icon('heroicon-m-currency-dollar'),
                        NumberConstraint::make('discount')
                            ->icon('heroicon-m-currency-dollar'),
                        BooleanConstraint::make('is_free'),
                        BooleanConstraint::make('status'),
                        DateConstraint::make('created_at'),
                    ])
                    ->constraintPickerColumns(2),
            ], layout: Tables\Enums\FiltersLayout::AboveContentCollapsible) // will display filters on top of table
            ->deferFilters()
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                //Tables\Actions\DeleteBulkAction::make(),
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(ProductExporter::class)
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
