@extends(backpack_view('blank'))

@section('content')
<div class="container-fluid py-3">
    <!-- Header / Actions -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
        <div>
            <a href="{{ backpack_url('product') }}" class="btn btn-outline-secondary btn-sm me-2">
                <i class="la la-arrow-left"></i> Back to Products
            </a>
            <span class="fs-4 fw-bold text-dark">{{ $product->name }}</span>
            <span class="badge bg-light text-dark border ms-2 font-monospace">{{ $product->sku ?? 'NO-SKU' }}</span>
        </div>

        <div class="d-flex gap-2">
            <button type="button" class="btn btn-warning btn-sm" onclick="openQuickRestockModal({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->quantity }}, {{ $product->restock_level }})">
                <i class="la la-cubes"></i> Quick Restock
            </button>
            <a href="{{ route('product.barcode.sheet', $product->id) }}" target="_blank" class="btn btn-outline-dark btn-sm">
                <i class="la la-barcode"></i> Print Barcode Labels
            </a>
            <a href="{{ route('product.export.csv') }}" class="btn btn-success btn-sm">
                <i class="la la-download"></i> Export CSV
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Main Product Information -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold"><i class="la la-info-circle me-1"></i> Product Specifications</h5>
                    <span class="badge {{ $product->is_available ? 'bg-success' : 'bg-danger' }}">
                        {{ $product->is_available ? 'Active & Available' : 'Unavailable' }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center mb-3">
                            @if($product->image)
                                <img src="{{ asset('storage/'.$product->image) }}" class="img-fluid rounded border shadow-sm" style="max-height: 220px; object-fit: contain;">
                            @else
                                <div class="bg-light border rounded d-flex align-items-center justify-content-center text-muted" style="height: 180px;">
                                    <i class="la la-image fs-1 d-block"></i> No Image
                                </div>
                            @endif

                            <div class="mt-3 p-2 bg-light rounded border text-center">
                                <small class="text-muted d-block font-monospace">SKU: {{ $product->sku ?? 'N/A' }}</small>
                                <small class="text-muted d-block font-monospace">BARCODE: {{ $product->barcode ?? 'N/A' }}</small>
                            </div>
                        </div>

                        <div class="col-md-8">
                            <table class="table table-bordered align-middle">
                                <tr>
                                    <th style="width: 35%" class="bg-light">Product Name</th>
                                    <td><strong class="text-dark">{{ $product->name }}</strong></td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Category</th>
                                    <td><span class="badge bg-info">{{ $product->category->name ?? 'N/A' }}</span></td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Primary Supplier</th>
                                    <td>
                                        @if($product->supplier)
                                            <a href="{{ backpack_url('supplier/' . $product->supplier->id . '/show') }}" class="fw-bold text-decoration-none">
                                                <i class="la la-truck"></i> {{ $product->supplier->name }}
                                            </a>
                                            <small class="text-muted d-block">Lead Time: {{ $product->supplier->lead_time_days }} days | Terms: {{ $product->supplier->payment_terms }}</small>
                                        @else
                                            <span class="text-muted">No supplier assigned</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Unit Price</th>
                                    <td><span class="fs-5 fw-bold text-success">₹{{ number_format($product->price, 2) }}</span></td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Current Stock</th>
                                    <td>
                                        <span class="fs-5 fw-bold">{{ $product->quantity }} units</span>
                                        @if($product->quantity == 0)
                                            <span class="badge bg-danger ms-2">Out Of Stock</span>
                                        @elseif($product->quantity <= $product->restock_level)
                                            <span class="badge bg-warning text-dark ms-2">Low Stock Threshold: {{ $product->restock_level }}</span>
                                        @else
                                            <span class="badge bg-success ms-2">Healthy Stock</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Restock Threshold</th>
                                    <td>{{ $product->restock_level }} units</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Description</th>
                                    <td>{{ $product->description ?: 'No detailed description provided.' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stock Movement & Adjustment Ledger -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-dark"><i class="la la-history me-1"></i> Stock Adjustments & Movement Ledger</h6>
                    <span class="badge bg-secondary">{{ $product->stockAdjustments->count() }} Records</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Before</th>
                                    <th>Adjustment</th>
                                    <th>After</th>
                                    <th>Reason / Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($product->stockAdjustments as $adj)
                                <tr>
                                    <td><small class="text-muted">{{ $adj->created_at->format('d M Y, H:i') }}</small></td>
                                    <td>
                                        <span class="badge {{ $adj->quantity_change >= 0 ? 'bg-success' : 'bg-warning text-dark' }}">
                                            {{ strtoupper($adj->type) }}
                                        </span>
                                    </td>
                                    <td>{{ $adj->quantity_before }}</td>
                                    <td class="fw-bold {{ $adj->quantity_change >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $adj->quantity_change >= 0 ? '+' : '' }}{{ $adj->quantity_change }}
                                    </td>
                                    <td class="fw-bold">{{ $adj->quantity_after }}</td>
                                    <td><small>{{ $adj->reason ?: 'Manual update' }}</small></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-3">No stock adjustment movements recorded yet.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar: Barcode / QR Scannable Cards & Audit Trail -->
        <div class="col-lg-4">
            <!-- Scannable Code Card -->
            <div class="card shadow-sm border-0 mb-4 text-center">
                <div class="card-header bg-dark text-white">
                    <h6 class="mb-0 fw-bold"><i class="la la-barcode me-1"></i> Live Barcode & Mobile 2D QR</h6>
                </div>
                <div class="card-body">
                    <!-- Code-128 SVG -->
                    <div class="mb-3">
                        <svg id="previewBarcodeSvg"
                             jsbarcode-format="CODE128"
                             jsbarcode-value="{{ $product->barcode ?? ('890' . str_pad($product->id, 9, '0', STR_PAD_LEFT)) }}"
                             jsbarcode-width="1.6"
                             jsbarcode-height="45"
                             jsbarcode-fontSize="12">
                        </svg>
                    </div>

                    <hr>

                    <!-- 2D QR Code -->
                    <div class="d-flex flex-column align-items-center mb-2">
                        <div id="previewQrBox" class="p-2 border rounded bg-white shadow-sm mb-2"></div>
                        <small class="text-muted">
                            <i class="la la-camera"></i> Scan with mobile camera to view product specs
                        </small>
                    </div>
                </div>
            </div>

            <!-- Activity Audit Trail -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light">
                    <h6 class="mb-0 fw-bold text-dark"><i class="la la-user-clock me-1"></i> Recent Audit Log Trail</h6>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush small">
                        @forelse($product->auditLogs->take(6) as $log)
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="badge {{ $log->action === 'CREATED' ? 'bg-success' : ($log->action === 'STOCK_ADJUSTED' ? 'bg-warning text-dark' : 'bg-primary') }}">
                                    {{ $log->action }}
                                </span>
                                <span class="text-muted">{{ $log->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="text-dark">{{ $log->description }}</div>
                            <div class="text-muted" style="font-size: 11px;">By: {{ $log->user_name ?? 'System' }} (IP: {{ $log->ip_address }})</div>
                        </li>
                        @empty
                        <li class="list-group-item text-muted text-center py-3">No audit activity logged.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin.widgets.quick_restock_modal')

<!-- JsBarcode & QRCode.js scripts -->
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    try {
        JsBarcode("#previewBarcodeSvg").init();
    } catch(e) {
        console.error("Barcode initialization error:", e);
    }

    try {
        new QRCode(document.getElementById("previewQrBox"), {
            text: "{{ url()->current() }}",
            width: 110,
            height: 110,
            colorDark: "#1f2937",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.M
        });
    } catch(e) {
        console.error("QR initialization error:", e);
    }
});
</script>
@endsection
