<x-filament::page>
    <div class="p-6">
        <h2 class="text-lg font-bold">{{ $record->name }}</h2>
        <p><strong>Email:</strong> {{ $record->email }}</p>
        <p><strong>Phone:</strong> {{ $record->phone }}</p>
        <p><strong>Subject:</strong> {{ $record->subject }}</p>
        <p><strong>Message:</strong></p>
        <p>{{ $record->message }}</p>
    </div>
</x-filament::page>
