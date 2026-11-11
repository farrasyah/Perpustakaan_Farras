<?php

namespace App\Filament\Resources\Books\Tables;

use App\Models\Loan;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BooksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable(),
                TextColumn::make('author')
                    ->label('Penulis')
                    ->searchable(),
                TextColumn::make('publisher')
                    ->label('Penerbit'),
                TextColumn::make('year')
                    ->label('Tahun')
                    ->numeric(thousandsSeparator: '')
                    ->sortable(),
                TextColumn::make('stock')
                    ->label('Stok')
                    ->sortable(),
            ])
            ->recordActions([
                Action::make('pinjam')
                    ->label('Pinjam')
                    ->icon('heroicon-o-book-open')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->stock > 0)
                    ->action(function ($record) {
                        Loan::create([
                            'user_id' => auth()->id(),
                            'book_id' => $record->id,
                        ]);

                        $record->decrement('stock');

                        Notification::make()
                            ->title('Buku berhasil dipinjam')
                            ->success()
                            ->send();
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}