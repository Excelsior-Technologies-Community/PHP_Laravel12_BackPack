@extends(backpack_view('blank'))

@section('content')
<div class="container-fluid py-2">

    <!-- Top Executive Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <div>
            <h2 class="fw-bold text-dark mb-1">
                <i class="la la-chart-area text-primary me-1"></i> Inventory & Supply Chain Intelligence Studio
            </h2>
            <p class="text-muted small mb-0">Real-time asset valuation, predictive restock watchdog, and financial analytics.</p>
        </div>
        <div class="d-flex gap-2 mt-2 mt-sm-0">
            <a href="{{ route('admin.purchase-order.generate') }}" class="btn btn-primary btn-sm shadow-sm">
                <i class="la la-file-invoice me-1"></i> 1-Click Low-Stock PO Draft
            </a>
            <a href="{{ route('product.barcode.sheet') }}" target="_blank" class="btn btn-outline-dark btn-sm shadow-sm">
                <i class="la la-barcode me-1"></i> Print All Barcodes
            </a>
            <a href="{{ route('product.export.csv') }}" class="btn btn-outline-success btn-sm shadow-sm">
                <i class="la la-download me-1"></i> Export Inventory CSV
            </a>
        </div>
    </div>

    <!-- Row 1: Executive Financial Valuation Bar -->
    <div class="row g-3 mb-4">
        <!-- Total Inventory Valuation -->
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm border-start border-primary border-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted text-uppercase fw-bold small">Total Asset Valuation</span>
                        <div class="badge bg-primary-lt text-primary p-2 rounded-circle">
                            <i class="la la-coins fs-4"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold text-dark mb-1">₹{{ number_format($stats['total_inventory_value'], 2) }}</h3>
                    <small class="text-muted">Total retail inventory market value</small>
                </div>
            </div>
        </div>

        <!-- Estimated Procurement Cost & Margin -->
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm border-start border-success border-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted text-uppercase fw-bold small">Projected Gross Margin</span>
                        <div class="badge bg-success-lt text-success p-2 rounded-circle">
                            <i class="la la-chart-line fs-4"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold text-success mb-1">₹{{ number_format($stats['gross_margin'], 2) }}</h3>
                    <small class="text-muted">Est. Wholesale Cost: ₹{{ number_format($stats['procurement_cost'], 2) }}</small>
                </div>
            </div>
        </div>

        <!-- Stock Health Ratio -->
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm border-start border-warning border-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted text-uppercase fw-bold small">Stock Health Score</span>
                        <div class="badge bg-warning-lt text-warning p-2 rounded-circle">
                            <i class="la la-shield-alt fs-4"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold {{ $stats['stock_health_score'] >= 70 ? 'text-success' : 'text-danger' }} mb-1">
                        {{ $stats['stock_health_score'] }}%
                    </h3>
                    <small class="text-muted">
                        {{ $stats['healthy_products'] }} healthy / {{ $stats['total_products'] }} total SKUs
                    </small>
                </div>
            </div>
        </div>

        <!-- Low & Out of Stock Alerts -->
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm border-start border-danger border-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted text-uppercase fw-bold small">Critical Reorders</span>
                        <div class="badge bg-danger-lt text-danger p-2 rounded-circle">
                            <i class="la la-bell fs-4"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold text-danger mb-1">
                        {{ $stats['low_stock_products'] + $stats['out_of_stock'] }} Items
                    </h3>
                    <small class="text-muted">
                        <span class="text-danger fw-bold">{{ $stats['out_of_stock'] }} depleted</span>, {{ $stats['low_stock_products'] }} below threshold
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 2: Interactive Chart.js Studio -->
    <div class="row g-3 mb-4">
        <!-- Chart 1: Category Asset Breakdown (Donut) -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center pb-0">
                    <h5 class="fw-bold text-dark mb-0">
                        <i class="la la-pie-chart text-primary me-1"></i> Category Capital Distribution
                    </h5>
                </div>
                <div class="card-body d-flex flex-column align-items-center justify-content-center">
                    <div style="position: relative; width: 100%; max-height: 250px;">
                        <canvas id="categoryDonutChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart 2: Top 5 High-Value Inventory Lines (Horizontal Bar) -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center pb-0">
                    <h5 class="fw-bold text-dark mb-0">
                        <i class="la la-stream text-success me-1"></i> Top 5 High-Value Inventory Lines
                    </h5>
                    <span class="badge bg-light text-muted border">Valuation (₹)</span>
                </div>
                <div class="card-body d-flex flex-column justify-content-center">
                    <div style="position: relative; width: 100%; max-height: 250px;">
                        <canvas id="topProductsBarChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart 3: Stock Health Ratio (Doughnut Gauge) -->
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center pb-0">
                    <h5 class="fw-bold text-dark mb-0">
                        <i class="la la-heartbeat text-danger me-1"></i> Stock Health Ratio
                    </h5>
                </div>
                <div class="card-body d-flex flex-column align-items-center justify-content-center">
                    <div style="position: relative; width: 100%; max-height: 220px;">
                        <canvas id="stockHealthChart"></canvas>
                    </div>
                    <div class="mt-3 text-center small text-muted">
                        <span class="badge bg-success me-1">● Healthy</span>
                        <span class="badge bg-warning text-dark me-1">● Low Stock</span>
                        <span class="badge bg-danger">● Out of Stock</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 3: Automated Reorder Watchdog Widget & Low-Stock Auto-PO Generator -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center py-3">
                    <div>
                        <h5 class="fw-bold text-danger mb-0">
                            <i class="la la-exclamation-triangle me-1"></i> Automated Reorder Watchdog & Low-Stock Replenishment
                        </h5>
                        <small class="text-muted">Items running below minimum threshold. Recommended replenishment quantities auto-calculated.</small>
                    </div>
                    <div class="mt-2 mt-sm-0">
                        <a href="{{ route('admin.purchase-order.generate') }}" class="btn btn-sm btn-primary">
                            <i class="la la-file-invoice me-1"></i> Generate Consolidated Purchase Order
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>SKU</th>
                                    <th>Product</th>
                                    <th>Category</th>
                                    <th>Supplier</th>
                                    <th>Current Stock</th>
                                    <th>Threshold</th>
                                    <th>Recommended Order</th>
                                    <th>Est. Restock Cost</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($watchdogProducts as $wp)
                                <tr>
                                    <td><code>{{ $wp->sku ?? 'PRD-'.$wp->id }}</code></td>
                                    <td>
                                        <strong>{{ $wp->name }}</strong>
                                    </td>
                                    <td><span class="badge bg-light text-secondary border">{{ $wp->category->name ?? 'N/A' }}</span></td>
                                    <td>
                                        @if($wp->supplier)
                                            <a href="{{ backpack_url('supplier/' . $wp->supplier->id . '/show') }}" class="text-decoration-none">
                                                <i class="la la-truck"></i> {{ $wp->supplier->name }}
                                            </a>
                                        @else
                                            <span class="text-muted small">No supplier</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($wp->quantity == 0)
                                            <span class="badge bg-danger">🔴 0 (Depleted)</span>
                                        @else
                                            <span class="badge bg-warning text-dark">🟠 {{ $wp->quantity }} Units</span>
                                        @endif
                                    </td>
                                    <td>{{ $wp->restock_level }}</td>
                                    <td>
                                        <span class="fw-bold text-primary">+{{ $wp->recommended_order }} units</span>
                                    </td>
                                    <td class="fw-bold text-dark">
                                        ₹{{ number_format($wp->estimated_restock_cost, 2) }}
                                    </td>
                                    <td class="text-end">
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-warning text-dark"
                                                onclick="openQuickRestockModal({{ $wp->id }}, '{{ addslashes($wp->name) }}', {{ $wp->quantity }}, {{ $wp->restock_level }})">
                                            <i class="la la-cubes"></i> Quick Restock
                                        </button>
                                        <a href="{{ route('product.preview', $wp->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="la la-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-success">
                                        <i class="la la-check-circle fs-2 d-block mb-1"></i>
                                        <strong>Great news! All products are currently stocked above minimum threshold.</strong>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 4: Activity Audit Log Feed & Quick Navigation Cards -->
    <div class="row g-3">
        <!-- Audit Log Activity Feed -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="fw-bold text-dark mb-0">
                        <i class="la la-history text-primary me-1"></i> Activity Audit Log & Historical Change Ledger
                    </h5>
                    <a href="{{ backpack_url('audit-log') }}" class="btn btn-sm btn-outline-secondary">
                        View Complete Ledger <i class="la la-arrow-right"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Timestamp</th>
                                    <th>User</th>
                                    <th>Action</th>
                                    <th>Target Entity</th>
                                    <th>Summary</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentAuditLogs as $log)
                                <tr>
                                    <td><small class="text-muted">{{ $log->created_at->format('d M, H:i') }}</small></td>
                                    <td><strong>{{ $log->user_name ?? 'System' }}</strong></td>
                                    <td>
                                        <span class="badge {{ $log->action === 'CREATED' ? 'bg-success' : ($log->action === 'STOCK_ADJUSTED' ? 'bg-warning text-dark' : ($log->action === 'DELETED' ? 'bg-danger' : 'bg-primary')) }}">
                                            {{ $log->action }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">{{ class_basename($log->auditable_type) }}</span>
                                        <strong>{{ $log->auditable_name }}</strong>
                                    </td>
                                    <td><small class="text-muted">{{ $log->description }}</small></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-3 text-muted">No audit activity logged.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Supplier & Category Quick Stats Card -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="fw-bold text-dark mb-0">
                        <i class="la la-truck text-info me-1"></i> Supplier Directory
                    </h5>
                    <a href="{{ backpack_url('supplier') }}" class="btn btn-sm btn-outline-primary">
                        Manage <i class="la la-arrow-right"></i>
                    </a>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3 p-3 bg-light rounded">
                        <div>
                            <span class="text-muted small d-block">Active Suppliers</span>
                            <h4 class="fw-bold mb-0 text-dark">{{ $stats['total_suppliers'] }} Vendors</h4>
                        </div>
                        <i class="la la-warehouse fs-1 text-primary"></i>
                    </div>

                    <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded mb-3">
                        <div>
                            <span class="text-muted small d-block">Active Categories</span>
                            <h4 class="fw-bold mb-0 text-dark">{{ $stats['total_categories'] }} Categories</h4>
                        </div>
                        <i class="la la-tags fs-1 text-success"></i>
                    </div>

                    <div class="p-3 bg-light rounded">
                        <span class="text-muted small d-block">Total Inventory Units</span>
                        <h4 class="fw-bold mb-0 text-primary">{{ number_format($stats['total_stock_quantity']) }} Units</h4>
                        <small class="text-muted">Across {{ $stats['total_products'] }} product line items</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin.widgets.quick_restock_modal')

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    var palette = [
        '#206bc4', '#2fb344', '#f59f00', '#d63939', '#4299e1', 
        '#7952b3', '#0ca678', '#f76707', '#ae3ec9', '#17a2b8'
    ];

    // Chart 1: Category Asset Breakdown Donut
    var catLabels = @json($chartCategoryLabels);
    var catValues = @json($chartCategoryValues);
    var ctxDonut = document.getElementById('categoryDonutChart').getContext('2d');
    new Chart(ctxDonut, {
        type: 'doughnut',
        data: {
            labels: catLabels,
            datasets: [{
                data: catValues,
                backgroundColor: palette.slice(0, catLabels.length),
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { boxWidth: 12, font: { size: 11 } }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            var val = context.parsed || 0;
                            return ' ₹' + val.toLocaleString('en-IN');
                        }
                    }
                }
            },
            cutout: '65%'
        }
    });

    // Chart 2: Top 5 High-Value Products Horizontal Bar
    var prodLabels = @json($chartTopProductLabels);
    var prodValues = @json($chartTopProductValues);
    var ctxBar = document.getElementById('topProductsBarChart').getContext('2d');
    new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: prodLabels,
            datasets: [{
                label: 'Valuation (₹)',
                data: prodValues,
                backgroundColor: '#206bc4',
                borderRadius: 4
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return ' ₹' + (context.parsed.x || 0).toLocaleString('en-IN');
                        }
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(val) {
                            return '₹' + (val >= 1000 ? (val / 1000) + 'k' : val);
                        }
                    }
                }
            }
        }
    });

    // Chart 3: Stock Health Gauge
    var healthLabels = @json($chartStockHealthLabels);
    var healthValues = @json($chartStockHealthValues);
    var ctxHealth = document.getElementById('stockHealthChart').getContext('2d');
    new Chart(ctxHealth, {
        type: 'doughnut',
        data: {
            labels: healthLabels,
            datasets: [{
                data: healthValues,
                backgroundColor: ['#2fb344', '#f59f00', '#d63939'],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return ' ' + context.label + ': ' + context.parsed + ' items';
                        }
                    }
                }
            },
            cutout: '70%'
        }
    });
});
</script>
@endsection
