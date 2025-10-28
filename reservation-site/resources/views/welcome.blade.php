<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Book a Reservation</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-gray-100">
    <div class="container mx-auto mt-12">
        <div class="max-w-2xl mx-auto bg-white p-8 rounded-lg shadow-md">
            <h1 class="text-2xl font-bold mb-6 text-center">Book Your Appointment</h1>

            <form action="{{ route('book-appointment') }}" method="POST">
                @csrf

                <!-- Expert -->
                <div class="mb-4">
                    <label for="expert_id" class="block text-gray-700 text-sm font-bold mb-2">Choose an Expert:</label>
                    <select name="expert_id" id="expert_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        @foreach ($experts as $expert)
                            <option value="{{ $expert->id }}">{{ $expert->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Reservation Date -->
                <div class="mb-4">
                    <label for="reservation_date" class="block text-gray-700 text-sm font-bold mb-2">Date:</label>
                    <input type="date" id="reservation_date" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <!-- Start Time -->
                <div class="mb-4">
                    <label for="start_time_picker" class="block text-gray-700 text-sm font-bold mb-2">Start Time:</label>
                    <input type="time" id="start_time_picker" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <!-- End Time -->
                <div class="mb-4">
                    <label for="end_time_picker" class="block text-gray-700 text-sm font-bold mb-2">End Time:</label>
                    <input type="time" id="end_time_picker" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <input type="hidden" name="start_time" id="start_time">
                <input type="hidden" name="end_time" id="end_time">

                <div class="text-center">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Book Now
                    </button>
                </div>
            </form>
        </div>
    </div>

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
            });
        });
    </script>
</body>
</html>
