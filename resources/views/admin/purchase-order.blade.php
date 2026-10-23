@extends(backpack_view('blank'))

@section('content')
<style>
    @media print {
        .no-print, header, nav, .main-header, .sidebar, .app-header {
            display: none !important;
        }
        body {
            background-color: #ffffff !important;
            color: #000000 !important;
            padding: 0;
            margin: 0;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        .container-fluid {
            width: 100% !important;
            padding: 0 !important;
        }
    }
    .po-header-box {
        border-bottom: 2px solid #206bc4;
        padding-bottom: 1.5rem;
        margin-bottom: 2rem;
    }
</style>

<div class="container-fluid py-3">
    <!-- Action Toolbar (Hidden during print) -->
    <div class="card mb-3 no-print shadow-sm">
        <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <a href="{{ backpack_url('dashboard') }}" class="btn btn-outline-secondary me-2">
                    <i class="la la-arrow-left"></i> Dashboard
                </a>
                <span class="fs-5 fw-bold text-dark">📦 Automated Low-Stock Purchase Order (PO)</span>
            </div>

            <div class="d-flex align-items-center gap-2">
                <!-- Supplier Filter Form -->
                <form method="GET" action="{{ route('admin.purchase-order.generate') }}" class="d-flex align-items-center">
                    <select name="supplier_id" class="form-select form-select-sm me-2" onchange="this.form.submit()">
                        <option value="">-- All Low-Stock Suppliers --</option>
                        @foreach($suppliers as $s)
                            <option value="{{ $s->id }}" {{ request('supplier_id') == $s->id ? 'selected' : '' }}>
                                {{ $s->name }} (Lead: {{ $s->lead_time_days }}d)
                            </option>
                        @endforeach
                    </select>
                </form>

                <a href="{{ route('admin.purchase-order.csv', ['supplier_id' => request('supplier_id')]) }}" class="btn btn-sm btn-outline-success">
                    <i class="la la-download"></i> Export CSV
                </a>

                <button onclick="window.print()" class="btn btn-sm btn-primary">
                    <i class="la la-print"></i> Print Purchase Order
                </button>
            </div>
        </div>
    </div>

    <!-- Official Printable Purchase Order Document -->
    <div class="card shadow border-0 p-4">
        <div class="card-body">
            <!-- PO Header -->
            <div class="row po-header-box align-items-start">
                <div class="col-sm-7">
                    <h2 class="text-primary fw-bold mb-1">
                        <i class="la la-cubes"></i> ENTERPRISE INVENTORY SYSTEMS
                    </h2>
                    <p class="text-muted mb-0 small">
                        Central Logistics & Supply Chain Procurement Division<br>
                        Industrial Area Phase II, Electronic City<br>
                        Email: procurement@enterprise.internal | Phone: +91 (080) 4123-8900
                    </p>
                </div>
                <div class="col-sm-5 text-sm-end">
                    <h3 class="fw-bold text-dark mb-1">PURCHASE ORDER</h3>
                    <div class="badge bg-danger fs-6 mb-2">REORDER DRAFT</div>
                    <p class="mb-0 small"><strong>PO Number:</strong> <span class="font-monospace text-primary fw-bold">{{ $poNumber }}</span></p>
                    <p class="mb-0 small"><strong>Issue Date:</strong> {{ $poDate }}</p>
                    <p class="mb-0 small"><strong>Expected Delivery:</strong> Within Standard Vendor Lead Time</p>
                </div>
            </div>

            <!-- Vendor & Shipment Destination Cards -->
            <div class="row mb-4">
                <div class="col-sm-6">
                    <div class="card bg-light border-0 p-3 h-100">
                        <h6 class="text-uppercase text-muted fw-bold small mb-2">Vendor / Supplier Details</h6>
                        @if($selectedSupplier)
                            <h5 class="fw-bold mb-1">{{ $selectedSupplier->name }}</h5>
                            <p class="small mb-1"><strong>Contact Person:</strong> {{ $selectedSupplier->contact_person ?? 'Account Manager' }}</p>
                            <p class="small mb-1"><strong>Email:</strong> {{ $selectedSupplier->email ?? 'N/A' }} | <strong>Phone:</strong> {{ $selectedSupplier->phone ?? 'N/A' }}</p>
                            <p class="small mb-1"><strong>Address:</strong> {{ $selectedSupplier->address ?? 'On File' }}</p>
                            <p class="small mb-0"><strong>Payment Terms:</strong> <span class="badge bg-secondary">{{ $selectedSupplier->payment_terms }}</span></p>
                        @else
                            <h5 class="fw-bold mb-1 text-primary">Consolidated Multi-Vendor PO</h5>
                            <p class="small text-muted mb-0">Contains replenishment orders across all preferred vendor accounts mapped to low-stock items.</p>
                        @endif
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="card bg-light border-0 p-3 h-100">
                        <h6 class="text-uppercase text-muted fw-bold small mb-2">Delivery & Invoicing Address</h6>
                        <h5 class="fw-bold mb-1">Enterprise Central Distribution Center</h5>
                        <p class="small mb-1">Dock 4B - Inbound Receiving Bay</p>
                        <p class="small mb-1">Tech Corridor Highway 48, Sector 12</p>
                        <p class="small mb-0"><strong>Attn:</strong> Warehouse Inward Logistics Officer</p>
                    </div>
                </div>
            </div>

            <!-- Line Items Table -->
            <div class="table-responsive mb-4">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-primary text-dark">
                        <tr>
                            <th style="width: 5%">#</th>
                            <th style="width: 15%">SKU Code</th>
                            <th style="width: 25%">Product Name</th>
                            <th style="width: 15%">Category</th>
                            <th style="width: 10%" class="text-center">Current / Min</th>
                            <th style="width: 10%" class="text-center">Order Qty</th>
                            <th style="width: 10%" class="text-end">Est. Unit (₹)</th>
                            <th style="width: 10%" class="text-end">Line Total (₹)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($poItems as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><span class="font-monospace fw-bold">{{ $item->product->sku ?? 'N/A' }}</span></td>
                            <td>
                                <strong>{{ $item->product->name }}</strong>
                                @if($item->supplier)
                                    <br><small class="text-muted"><i class="la la-truck"></i> {{ $item->supplier->name }}</small>
                                @endif
                            </td>
                            <td>{{ $item->product->category->name ?? 'General' }}</td>
                            <td class="text-center">
                                @if($item->current_stock == 0)
                                    <span class="badge bg-danger">0 / {{ $item->restock_level }}</span>
                                @else
                                    <span class="badge bg-warning text-dark">{{ $item->current_stock }} / {{ $item->restock_level }}</span>
                                @endif
                            </td>
                            <td class="text-center fw-bold text-primary fs-6">
                                +{{ $item->recommended_qty }}
                            </td>
                            <td class="text-end">
                                ₹{{ number_format($item->unit_cost, 2) }}
                            </td>
                            <td class="text-end fw-bold text-dark">
                                ₹{{ number_format($item->subtotal, 2) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-success">
                                <i class="la la-check-circle fs-3 d-block mb-2"></i>
                                <strong>All products are currently well stocked above their reorder thresholds!</strong>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Financial Summary Box -->
            <div class="row justify-content-end mb-4">
                <div class="col-sm-5">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td class="text-muted">Total Units to Procure:</td>
                            <td class="text-end fw-bold">{{ $totalUnitsToOrder }} units</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Estimated Net Subtotal:</td>
                            <td class="text-end fw-bold">₹{{ number_format($totalEstimatedCost, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Applicable Tax / GST (Est. 18%):</td>
                            <td class="text-end fw-bold">₹{{ number_format($totalEstimatedCost * 0.18, 2) }}</td>
                        </tr>
                        <tr class="border-top border-dark">
                            <td class="fs-5 fw-bold text-primary">Estimated Grand Total:</td>
                            <td class="text-end fs-5 fw-bold text-primary">₹{{ number_format($totalEstimatedCost * 1.18, 2) }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Signatures Section -->
            <div class="row pt-4 mt-5 border-top">
                <div class="col-4 text-center">
                    <p class="mb-4">___________________________</p>
                    <p class="fw-bold mb-0">Authorized Purchasing Officer</p>
                    <small class="text-muted">Inventory Control Dept</small>
                </div>
                <div class="col-4 text-center">
                    <p class="mb-4">___________________________</p>
                    <p class="fw-bold mb-0">Finance Approver</p>
                    <small class="text-muted">Accounts Payable</small>
                </div>
                <div class="col-4 text-center">
                    <p class="mb-4">___________________________</p>
                    <p class="fw-bold mb-0">Vendor Acceptance</p>
                    <small class="text-muted">Signed & Stamped</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
