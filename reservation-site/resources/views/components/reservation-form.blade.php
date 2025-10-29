@props(['reservation' => null])

<form {{ $attributes }}>
    @csrf

    <!-- Expert -->
    <div class="mb-4">
        <label for="expert_id" class="block text-gray-700 text-sm font-bold mb-2">Expert:</label>
        <select name="expert_id" id="expert_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            @foreach ($experts as $expert)
                <option value="{{ $expert->id }}" {{ old('expert_id', $reservation?->expert_id) == $expert->id ? 'selected' : '' }}>
                    {{ $expert->name }}
                </option>
            @endforeach
        </select>
        @error('expert_id')
            <p class="text-red-500 text-xs italic">{{ $message }}</p>
        @enderror
    </div>

    <!-- Start Time -->
    <div class="mb-4">
        <label for="start_time" class="block text-gray-700 text-sm font-bold mb-2">Start Time:</label>
        <input type="datetime-local" name="start_time" id="start_time" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" value="{{ old('start_time', $reservation?->start_time->format('Y-m-d\TH:i')) }}">
        @error('start_time')
            <p class="text-red-500 text-xs italic">{{ $message }}</p>
        @enderror
    </div>

    <!-- End Time -->
    <div class="mb-4">
        <label for="end_time" class="block text-gray-700 text-sm font-bold mb-2">End Time:</label>
        <input type="datetime-local" name="end_time" id="end_time" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" value="{{ old('end_time', $reservation?->end_time->format('Y-m-d\TH:i')) }}">
        @error('end_time')
            <p class="text-red-500 text-xs italic">{{ $message }}</p>
        @enderror
    </div>

    {{ $slot }}
</form>
