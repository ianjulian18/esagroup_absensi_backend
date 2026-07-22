<?php

namespace App\Filament\Pages;

use App\Models\Itinerary;
use App\Models\User;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use Illuminate\Support\Collection;

class ItineraryBoard extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';
    protected static string|\UnitEnum|null $navigationGroup = 'Attendance';
    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.itinerary-board';

    public $currentWeekStart;

    public function mount()
    {
        $this->currentWeekStart = now()->startOfWeek()->format('Y-m-d');
    }

    public function nextWeek()
    {
        $this->currentWeekStart = Carbon::parse($this->currentWeekStart)->addWeek()->format('Y-m-d');
    }

    public function prevWeek()
    {
        $this->currentWeekStart = Carbon::parse($this->currentWeekStart)->subWeek()->format('Y-m-d');
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('import')
                ->label('Import Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->form([
                    \Filament\Forms\Components\FileUpload::make('attachment')
                        ->label('File Excel')
                        ->required()
                        ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel']),
                ])
                ->action(function (array $data) {
                    $file = storage_path('app/public/' . $data['attachment']);
                    \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\ItineraryImport, $file);
                    \Filament\Notifications\Notification::make()
                        ->title('Berhasil Import Itinerary')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function getEmployeesProperty(): Collection
    {
        return User::where('role', 'karyawan')->get();
    }

    public function getDatesProperty(): array
    {
        $dates = [];
        $start = Carbon::parse($this->currentWeekStart);
        for ($i = 0; $i < 7; $i++) {
            $dates[] = $start->copy()->addDays($i);
        }
        return $dates;
    }

    public function getItinerariesProperty(): Collection
    {
        $dates = $this->dates;
        $start = $dates[0]->format('Y-m-d');
        $end = $dates[6]->format('Y-m-d');

        return Itinerary::with('workingHour')
            ->whereBetween('date', [$start, $end])
            ->get();
    }

    public function editItineraryAction(): Action
    {
        return Action::make('editItinerary')
            ->modalHeading(fn (array $arguments) => 'Edit Itinerary - ' . Carbon::parse($arguments['date'])->format('d M Y'))
            ->slideOver()
            ->form([
                Select::make('working_hour_id')
                    ->label('Shift (Working Hour)')
                    ->options(\App\Models\WorkingHour::pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                Select::make('routing_type')
                    ->label('Routing Type')
                    ->options([
                        'bebas_visit' => 'Bebas visit',
                        'routing_aktif' => 'Routing aktif',
                    ])
                    ->default('bebas_visit')
                    ->required(),
                \Filament\Forms\Components\Toggle::make('is_first_visit_locked')
                    ->label('Wajib Check-In di Toko Pertama')
                    ->default(false),
                Select::make('stores')
                    ->label('Stores to Visit')
                    ->multiple()
                    ->options(\App\Models\Store::pluck('name', 'name'))
                    ->searchable()
                    ->placeholder('Select stores'),
            ])
            ->fillForm(function (array $arguments) {
                $itinerary = Itinerary::where('user_id', $arguments['userId'])
                    ->where('date', $arguments['date'])
                    ->first();

                if ($itinerary) {
                    return [
                        'working_hour_id' => $itinerary->working_hour_id,
                        'routing_type' => $itinerary->routing_type,
                        'stores' => $itinerary->stores,
                        'is_first_visit_locked' => $itinerary->is_first_visit_locked,
                    ];
                }

                return [];
            })
            ->action(function (array $data, array $arguments) {
                Itinerary::updateOrCreate(
                    [
                        'user_id' => $arguments['userId'],
                        'date' => $arguments['date'],
                    ],
                    [
                        'working_hour_id' => $data['working_hour_id'],
                        'routing_type' => $data['routing_type'],
                        'stores' => $data['stores'] ?? [],
                        'is_first_visit_locked' => $data['is_first_visit_locked'] ?? false,
                    ]
                );
            });
    }
}
