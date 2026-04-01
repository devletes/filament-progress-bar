<?php

namespace Workbench\App\Filament\Resources;

use Devletes\FilamentProgressBar\Infolists\Components\ProgressBarEntry;
use Devletes\FilamentProgressBar\Tables\Columns\ProgressBarColumn;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Workbench\App\Filament\Resources\UserResource\Pages\ListUsers;
use Workbench\App\Filament\Resources\UserResource\Pages\ViewUser;
use Workbench\App\Models\User;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('email'),
                ProgressBarColumn::make('tasks_completed')
                    ->label('Tasks')
                    ->maxValue(fn (User $record): int => (int) $record->tasks_total)
                    ->warningLabel(fn (int $percentage): string => "{$percentage}% done"),
                ProgressBarColumn::make('leave_progress')
                    ->label('Leave')
                    ->state(fn (User $record): array => [
                        'progress' => (float) $record->leave_used,
                        'total' => (float) $record->leave_total,
                    ])
                    ->warningLabel('Monitor')
                    ->dangerLabel('High usage'),
                ProgressBarColumn::make('inventory_progress')
                    ->label('Inventory')
                    ->state(fn (User $record): array => [
                        'used' => (int) $record->inventory_used,
                        'quota' => (int) $record->inventory_total,
                    ])
                    ->successColor('rgb(16 185 129)')
                    ->warningColor('rgb(249 115 22)')
                    ->dangerColor('rgb(220 38 38)'),
            ])
            ->actions([
                ViewAction::make(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('User Progress')
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('email'),
                        ProgressBarEntry::make('tasks_completed')
                            ->label('Tasks')
                            ->maxValue(fn (User $record): int => (int) $record->tasks_total)
                            ->successLabel('On track')
                            ->warningLabel('At risk')
                            ->dangerLabel('Critical'),
                        ProgressBarEntry::make('leave_progress')
                            ->label('Leave balance')
                            ->getStateUsing(fn (User $record): array => [
                                'progress' => (float) $record->leave_used,
                                'total' => (float) $record->leave_total,
                            ])
                            ->warningThreshold(70)
                            ->dangerThreshold(90),
                        ProgressBarEntry::make('inventory_progress')
                            ->label('Inventory usage')
                            ->getStateUsing(fn (User $record): array => [
                                'progress' => (int) $record->inventory_used,
                                'total' => (int) $record->inventory_total,
                            ])
                            ->hideProgressValue()
                            ->warningLabel(fn (int $percentage): string => "Usage {$percentage}%"),
                    ])
                    ->columns(1),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'view' => ViewUser::route('/{record}'),
        ];
    }
}
