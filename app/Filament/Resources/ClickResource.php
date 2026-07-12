<?php
// WHAT IS A RESOURCE?
// A resource is a class that defines how to display and manage a specific Eloquent model in the Filament admin panel.
// It provides a way to create, read, update, and delete records of that model through a user-friendly interface.


namespace App\Filament\Resources;

use App\Filament\Resources\ClickResource\Pages;
use App\Filament\Resources\ClickResource\RelationManagers;
use App\Models\Click;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ClickResource extends Resource
{
    // $model is a property that stores and tells Filament which Eloquent model this resource is associated with.
    // In this case, the ClickResource is associated with the Click model.
    // Filament uses this property to know which database table to interact with when performing CRUD operations through the admin panel.
    // The Click model represents the clicks table in the database, and by associating it with the ClickResource, Filament can automatically generate forms, tables, and other UI components to manage click records.
    protected static ?string $model = Click::class;

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
                //Display the value from the `id` column of the `clicks` table.
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                // This is displays the value from the `short_code` column of the `links` table, which is related to the `clicks` table through the `link_id` foreign key.
                // Use the `link()` relationship from Click.php.
                // Filament finds the related row in the `links` table where:
                // links.id = clicks.link_id
                // Then it displays the value from `links.short_code`.
                Tables\Columns\TextColumn::make('link.short_code')
                    ->label('Short Code')
                    ->searchable(),


                // This displays the value from the `link_id` column of the `clicks` table.
                // It is a foreign key that references the `id` column in the `links` table.
                Tables\Columns\TextColumn::make('link_id')
                    ->label('Link ID')
                    ->sortable(),


                // This displays the value from the `ip_address` column of the `clicks` table.
                // It is the IP address of the user who clicked the link.
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->searchable(),


                // This displays the value from the `created_at` column of the `clicks` table.
                // It is the timestamp of when the click was created.
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
            'index' => Pages\ListClicks::route('/'),
            'create' => Pages\CreateClick::route('/create'),
            'edit' => Pages\EditClick::route('/{record}/edit'),
        ];
    }
}
