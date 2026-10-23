<button type="button" 
    class="btn btn-sm btn-outline-warning text-dark me-1" 
    title="Quick Stock Adjustment"
    onclick="openQuickRestockModal({{ $entry->id }}, '{{ addslashes($entry->name) }}', {{ $entry->quantity }}, {{ $entry->restock_level }})">
    <i class="la la-cubes"></i> Restock
</button>
