<?php

namespace Workbench\App\Filament\Resources;

use Devletes\FilamentProgressBar\Infolists\Components\ProgressBarEntry;
use Devletes\FilamentProgressBar\Tables\Columns\ProgressBarColumn;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontFamily;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;
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
            ->modifyQueryUsing(fn (Builder $query) => $query->where('demo_variant', '!=', 'infolist_demo'))
            ->paginated(false)
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Configuration')
                    ->fontFamily(FontFamily::Mono)
                    ->width('1%'),

                ProgressBarColumn::make('tasks_completed')
                    ->label('Tasks')
                    ->maxValue(fn (User $record): int => (int) $record->tasks_total)
                    ->size(fn (User $record): string => match ($record->demo_variant) {
                        'medium_size' => 'md',
                        'large_size' => 'lg',
                        default => 'sm',
                    })
                    ->textPosition(fn (User $record): string => $record->demo_variant === 'outside_text'
                        ? 'outside'
                        : 'inside'
                    )
                    ->thresholdDirection(fn (User $record): string => $record->demo_variant === 'descending'
                        ? 'descending'
                        : 'ascending'
                    )
                    ->warningThreshold(fn (User $record): int => $record->demo_variant === 'descending' ? 30 : 70)
                    ->dangerThreshold(fn (User $record): int => $record->demo_variant === 'descending' ? 10 : 90)
                    ->borderRadius(fn (User $record): ?string => $record->demo_variant === 'border_radius'
                        ? '4px'
                        : null
                    )
                    ->thresholds(fn (User $record): ?array => $record->demo_variant === 'threshold_map'
                        ? [80 => 'success', 60 => 'warning', 40 => 'info', 0 => 'danger']
                        : null
                    ),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('User Progress')
                    ->schema([
                        TextEntry::make('name')
                            ->hiddenLabel()
                            ->formatStateUsing(fn (string $state) => new HtmlString(
                                '<div style="display:flex;align-items:center;gap:0.875rem;">'
                                .'<img src="'.asset('devletes.png').'" alt="Devletes" style="width:2.75rem;height:2.75rem;border-radius:9999px;flex-shrink:0;object-fit:cover;">'
                                .'<span style="font-weight:600;font-size:1.125rem;">'.e($state).'</span>'
                                .'</div>'
                            )),
                        ProgressBarEntry::make('tasks_completed')
                            ->label('Tasks')
                            ->icon('heroicon-o-check-circle')
                            ->iconColor('primary')
                            ->maxValue(fn (User $record): int => (int) $record->tasks_total)
                            ->warningThreshold(85),
                        ProgressBarEntry::make('leave_progress')
                            ->label('Leave balance')
                            ->icon('heroicon-o-calendar-days')
                            ->iconColor('warning')
                            ->getStateUsing(fn (): array => ['progress' => 15, 'total' => 20])
                            ->size('sm')
                            ->textPosition('outside'),
                        ProgressBarEntry::make('stock_remaining')
                            ->label('Stock remaining')
                            ->icon('heroicon-o-cube')
                            ->iconColor('danger')
                            ->getStateUsing(fn (): array => ['progress' => 35, 'total' => 250])
                            ->thresholdDirection('descending')
                            ->warningThreshold(40)
                            ->dangerThreshold(15)
                            ->borderRadius('4px')
                            ->size('lg')
                            ->successLabel('Healthy')
                            ->warningLabel('Reorder soon')
                            ->dangerLabel('Restock now'),
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
