<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Reservation') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('admin.reservations.update', $reservation) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Expert -->
                        <div class="mb-4">
                            <label for="expert_id" class="block text-gray-700 text-sm font-bold mb-2">Expert:</label>
                            <select name="expert_id" id="expert_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                @foreach ($experts as $expert)
                                    <option value="{{ $expert->id }}" {{ old('expert_id', $reservation->expert_id) == $expert->id ? 'selected' : '' }}>
                                        {{ $expert->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Date, Start and End Time -->
                        <div class="mb-4">
                            <label for="start_time" class="block text-gray-700 text-sm font-bold mb-2">Start Time:</label>
                            <input type="datetime-local" name="start_time" id="start_time" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" value="{{ old('start_time', $reservation->start_time->format('Y-m-d\TH:i')) }}">
                        </div>
                        <div class="mb-4">
                            <label for="end_time" class="block text-gray-700 text-sm font-bold mb-2">End Time:</label>
                            <input type="datetime-local" name="end_time" id="end_time" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" value="{{ old('end_time', $reservation->end_time->format('Y-m-d\TH:i')) }}">
                        </div>


                        <div class="flex items-center justify-between">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Update Reservation
                            </button>
                            <a href="{{ route('admin.reservations.index') }}" class="text-gray-600">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
