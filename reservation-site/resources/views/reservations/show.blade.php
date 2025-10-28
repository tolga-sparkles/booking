<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Reservation Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Customer Information</h3>
                            <p class="mt-1 text-sm text-gray-600"><strong>Name:</strong> {{ $reservation->user->name }}</p>
                            <p class="mt-1 text-sm text-gray-600"><strong>Email:</strong> {{ $reservation->user->email }}</p>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Expert Information</h3>
                            <p class="mt-1 text-sm text-gray-600"><strong>Name:</strong> {{ $reservation->expert->name ?? 'N/A' }}</p>
                            <p class="mt-1 text-sm text-gray-600"><strong>Email:</strong> {{ $reservation->expert->email ?? 'N/A' }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <h3 class="text-lg font-medium text-gray-900">Reservation Timings</h3>
                            <p class="mt-1 text-sm text-gray-600"><strong>Start Time:</strong> {{ $reservation->start_time }}</p>
                            <p class="mt-1 text-sm text-gray-600"><strong>End Time:</strong> {{ $reservation->end_time }}</p>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <a href="{{ url()->previous() }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Back to List
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
