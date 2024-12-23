<x-filament::page>
    <div class="p-6">
        <h2 class="text-lg font-bold">Payment Details:-</h2>
        <p><strong>Payment ID:</strong>{{ $record->payment_id }}</p>
        @if($record->plan_id)
        <p><strong>Plan ID:</strong> {{ $record->plan_id }}</p>
        @endif
        @if($record->subscription_id)
        <p><strong>Subscription ID:</strong> {{ $record->subscription_id }}</p>
        @endif
        <p><strong>User:</strong> {{ $record->user?->name }}</p>
        <p><strong>Amount:</strong> {{ $record->amount }}</p>
        <p><strong>Type:</strong> {{ $record->type }}</p>
        <p><strong>Method:</strong> {{ $record->method }}</p>
        <p><strong>Full response:</strong></p>
        <pre>
        <p>{{ print_r($record->response_json) }}</p>
    </div>
</x-filament::page>