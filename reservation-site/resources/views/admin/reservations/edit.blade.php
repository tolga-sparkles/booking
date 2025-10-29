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
                    <x-reservation-form :reservation="$reservation" method="POST" :action="route('admin.reservations.update', $reservation)">
                        @method('PUT')
                        <div class="flex items-center justify-between">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Update Reservation
                            </button>
                            <a href="{{ route('admin.reservations.index') }}" class="text-gray-600">Cancel</a>
                        </div>
                    </x-reservation-form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
