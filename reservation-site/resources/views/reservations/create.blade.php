<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('New Reservation') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('reservations.store') }}" method="POST">
                        @csrf

                        <!-- Expert -->
                        <div class="mb-4">
                            <label for="expert_id" class="block text-gray-700 text-sm font-bold mb-2">Expert:</label>
                            <select name="expert_id" id="expert_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                @foreach ($experts as $expert)
                                <option value="{{ $expert->id }}" {{ old('expert_id', $pending['expert_id'] ?? null) == $expert->id ? 'selected' : '' }}>
                                        {{ $expert->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('expert_id')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Reservation Date -->
                        <div class="mb-4">
                            <label for="reservation_date" class="block text-gray-700 text-sm font-bold mb-2">Date:</label>
                            <input type="date" id="reservation_date" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ old('reservation_date') }}">
                        </div>

                        <!-- Start Time -->
                        <div class="mb-4">
                            <label for="start_time_picker" class="block text-gray-700 text-sm font-bold mb-2">Start Time:</label>
                            <input type="time" id="start_time_picker" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ old('start_time_picker') }}">
                            @error('start_time')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- End Time -->
                        <div class="mb-4">
                            <label for="end_time_picker" class="block text-gray-700 text-sm font-bold mb-2">End Time:</label>
                            <input type="time" id="end_time_picker" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ old('end_time_picker') }}">
                            @error('end_time')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>

                        <input type="hidden" name="start_time" id="start_time">
                        <input type="hidden" name="end_time" id="end_time">

                        <div class="flex items-center justify-between">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Create Reservation
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.querySelector('form');
            const dateInput = document.getElementById('reservation_date');
            const startTimeInput = document.getElementById('start_time_picker');
            const endTimeInput = document.getElementById('end_time_picker');
            const hiddenStartTime = document.getElementById('start_time');
            const hiddenEndTime = document.getElementById('end_time');

            function updateHiddenFields() {
                const date = dateInput.value;
                const startTime = startTimeInput.value;
                const endTime = endTimeInput.value;

                if (date && startTime) {
                    hiddenStartTime.value = `${date}T${startTime}`;
                } else {
                    hiddenStartTime.value = '';
                }

                if (date && endTime) {
                    hiddenEndTime.value = `${date}T${endTime}`;
                } else {
                    hiddenEndTime.value = '';
                }
            }

            dateInput.addEventListener('input', updateHiddenFields);
            startTimeInput.addEventListener('input', updateHiddenFields);
            endTimeInput.addEventListener('input', updateHiddenFields);

            form.addEventListener('submit', function(e) {
                updateHiddenFields();
                // Basic validation to ensure fields are not empty
                if (!hiddenStartTime.value || !hiddenEndTime.value) {
                    // This is a fallback, Laravel validation is primary
                    console.error("Date and time must be selected.");
                }
            });

            function setFormValues(start, end) {
                if (!start) return;

                const startDate = new Date(start.replace(' ', 'T'));
                const endDate = new Date(end.replace(' ', 'T'));

                dateInput.value = startDate.toISOString().split('T')[0];
                startTimeInput.value = startDate.toTimeString().substring(0, 5);
                endTimeInput.value = endDate.toTimeString().substring(0, 5);
                updateHiddenFields();
            }

            // Prioritize old() data on validation failure, then check for pending data from session
            const oldStartTime = "{{ old('start_time', $pending['start_time'] ?? '') }}";
            const oldEndTime = "{{ old('end_time', $pending['end_time'] ?? '') }}";

            setFormValues(oldStartTime, oldEndTime);
        });
    </script>
    @endpush
</x-app-layout>
