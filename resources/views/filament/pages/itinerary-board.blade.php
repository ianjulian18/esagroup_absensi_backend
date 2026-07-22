<x-filament-panels::page>
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-bold">Itinerary Planning</h2>
        <div class="flex space-x-2">
            <x-filament::button wire:click="prevWeek" color="gray" size="sm">
                Previous Week
            </x-filament::button>
            <x-filament::button wire:click="nextWeek" color="gray" size="sm">
                Next Week
            </x-filament::button>
        </div>
    </div>

    <div class="overflow-x-auto bg-white dark:bg-gray-900 rounded-lg shadow ring-1 ring-gray-950/5 dark:ring-white/10">
        <table class="w-full text-left text-sm border-collapse">
            <thead class="bg-gray-50 dark:bg-white/5 border-b border-gray-200 dark:border-white/10">
                <tr>
                    <th class="px-4 py-4 font-semibold text-gray-950 dark:text-white whitespace-nowrap border-r border-gray-200 dark:border-white/10 w-48">
                        Employee
                    </th>
                    @foreach($this->dates as $date)
                        <th class="px-4 py-4 font-semibold text-gray-950 dark:text-white whitespace-nowrap text-center border-r border-gray-200 dark:border-white/10 min-w-[140px]">
                            <div class="text-sm">{{ $date->format('M d, Y') }}</div>
                            <div class="text-xs text-gray-500 font-normal mt-1">{{ $date->format('l') }}</div>
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($this->employees as $employee)
                    <tr class="border-b border-gray-200 dark:border-white/10 hover:bg-gray-50 dark:hover:bg-white/5 transition">
                        <td class="px-4 py-3 align-top whitespace-nowrap border-r border-gray-200 dark:border-white/10">
                            <div class="font-medium text-gray-950 dark:text-white">{{ $employee->name }}</div>
                            <div class="text-xs text-gray-500 mt-0.5">{{ $employee->nik ?? $employee->email }}</div>
                        </td>
                        @foreach($this->dates as $date)
                            @php
                                $itinerary = $this->itineraries->first(function($i) use ($employee, $date) {
                                    return $i->user_id === $employee->id && $i->date->format('Y-m-d') === $date->format('Y-m-d');
                                });
                            @endphp
                            <td class="p-2 align-top border-r border-gray-200 dark:border-white/10 cursor-pointer hover:bg-gray-100 dark:hover:bg-white/10 transition-colors" 
                                wire:click="mountAction('editItinerary', { userId: {{ $employee->id }}, date: '{{ $date->format('Y-m-d') }}' })">
                                
                                @if($itinerary)
                                    <div class="p-3 bg-success-50 dark:bg-success-900/20 text-success-600 dark:text-success-400 rounded-md border border-success-200 dark:border-success-800 flex flex-col gap-1 shadow-sm h-full">
                                        <div class="font-bold text-xs uppercase tracking-wider">Planned</div>
                                        <div class="text-sm font-medium">{{ $itinerary->workingHour->start_time ?? '00:00' }} - {{ $itinerary->workingHour->end_time ?? '00:00' }}</div>
                                        @if(!empty($itinerary->stores))
                                            <div class="text-xs flex items-center gap-1.5 mt-1 opacity-90">
                                                <x-heroicon-s-building-storefront class="w-3.5 h-3.5"/>
                                                {{ count($itinerary->stores) }} Stores
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <div class="flex items-center justify-center h-full min-h-[70px] text-gray-400 text-xs font-medium border-2 border-dashed border-transparent hover:border-gray-300 dark:hover:border-gray-700 rounded-md">
                                        + No Plan
                                    </div>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
                
                @if($this->employees->isEmpty())
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                            No employees found.
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</x-filament-panels::page>
