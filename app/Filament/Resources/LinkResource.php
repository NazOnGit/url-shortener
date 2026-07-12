<?php
// WHAT IS A RESOURCE?
// A resource is a class that defines how to display and manage a specific Eloquent model in the Filament admin panel.
// It provides a way to create, read, update, and delete records of that model through a user-friendly interface.

namespace App\Filament\Resources;

use App\Filament\Resources\LinkResource\Pages;
use App\Filament\Resources\LinkResource\RelationManagers;
use App\Models\Link;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LinkResource extends Resource
{
    // $model is a property that stores and tells Filament which Eloquent model this resource is associated with.
    // In this case, the LinkResource is associated with the Link model.
    // Filament uses this property to know which database table to interact with when performing CRUD operations through the admin panel.
    // The Link model represents the links table in the database, and by associating it with the LinkResource, Filament can automatically generate forms, tables, and other UI components to manage link records.

    protected static ?string $model = Link::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // TextColumn::make('id') means display the value from the `id` column of the `links` table.
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                // TextColumn::make('user.name') means display the value from the `name` column of the `users` table, which is related to the `links` table through the `user_id` foreign key.
                // The `user` relationship is defined in the Link model, and it allows Filament to access the related User model and display the user's name in the table.
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Owner')
                    ->searchable(),

                Tables\Columns\TextColumn::make('original_url')
                    ->label('Original URL')
                    ->limit(50)
                    ->searchable(),

                Tables\Columns\TextColumn::make('short_code')
                    ->label('Short Code')
                    ->searchable(),

                Tables\Columns\TextColumn::make('clicks_count')
                    ->label('Clicks')
                    ->counts('clicks'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListLinks::route('/'),
            'create' => Pages\CreateLink::route('/create'),
            'edit' => Pages\EditLink::route('/{record}/edit'),
        ];
    }
}
