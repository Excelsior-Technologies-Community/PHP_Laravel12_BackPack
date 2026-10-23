@extends(backpack_view('blank'))

@section('content')
<style>
    @media print {
        .no-print, header, nav, .main-header, .sidebar, .app-header {
            display: none !important;
        }
        body {
            background-color: #ffffff !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        .container-fluid {
            width: 100% !important;
            padding: 0 !important;
        }
        .sticker-card {
            break-inside: avoid;
            page-break-inside: avoid;
        }
    }

    /* Thermal Single Label Mode */
    .thermal-label {
        width: 65mm;
        min-height: 38mm;
        border: 1px dashed #bbb;
        padding: 6px;
        margin: 5px auto;
        background: #fff;
        text-align: center;
        border-radius: 4px;
    }

    /* A4 Multi-Grid Mode */
    .a4-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 8px;
    }
    .a4-label {
        border: 1px dashed #ccc;
        padding: 8px;
        background: #fff;
        border-radius: 4px;
        text-align: center;
    }
</style>

<div class="container-fluid py-3">
    <!-- Toolbar (Hidden on Print) -->
    <div class="card mb-3 no-print shadow-sm">
        <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <a href="{{ backpack_url('product') }}" class="btn btn-outline-secondary me-2">
                    <i class="la la-arrow-left"></i> Products
                </a>
                <span class="fs-5 fw-bold text-dark">🏷️ Barcode (Code-128) & 2D QR Code Printable Studio</span>
            </div>

            <div class="d-flex align-items-center gap-2">
                <!-- Mode Switcher -->
                <div class="btn-group" role="group">
                    <a href="{{ route('product.barcode.sheet', array_filter(['id' => $id])) }}?format=thermal" class="btn btn-sm {{ $format === 'thermal' ? 'btn-primary' : 'btn-outline-primary' }}">
                        <i class="la la-ticket"></i> Thermal (65mm x 38mm)
                    </a>
                    <a href="{{ route('product.barcode.sheet', array_filter(['id' => $id])) }}?format=a4" class="btn btn-sm {{ $format === 'a4' ? 'btn-primary' : 'btn-outline-primary' }}">
                        <i class="la la-th-large"></i> A4 Sticker Sheet Grid
                    </a>
                </div>

                <button onclick="window.print()" class="btn btn-sm btn-success">
                    <i class="la la-print"></i> 1-Click Print Labels
                </button>
            </div>
        </div>
    </div>

    <!-- Sticker Canvas Container -->
    @if($format === 'thermal')
        <div class="d-flex flex-column align-items-center">
            @foreach($products as $product)
                @for($copy = 1; $copy <= ($id ? 3 : 1); $copy++)
                    <div class="thermal-label sticker-card shadow-sm mb-3">
                        <div class="d-flex justify-content-between align-items-center px-1">
                            <span class="fw-bold small text-truncate" style="max-width: 140px;">{{ $product->name }}</span>
                            <span class="fw-bold text-primary small">₹{{ number_format($product->price, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center px-1 my-1">
                            <small class="text-muted font-monospace">{{ $product->sku ?? 'SKU-'.$product->id }}</small>
                            <span class="badge bg-light text-dark border py-0 px-1" style="font-size: 9px;">
                                {{ $product->category->name ?? 'General' }}
                            </span>
                        </div>

                        <!-- Barcode SVG -->
                        <div class="my-1">
                            <svg class="barcode-svg" 
                                 jsbarcode-format="CODE128" 
                                 jsbarcode-value="{{ $product->barcode ?? ('890' . str_pad($product->id, 9, '0', STR_PAD_LEFT)) }}" 
                                 jsbarcode-width="1.3" 
                                 jsbarcode-height="30" 
                                 jsbarcode-fontSize="11" 
                                 jsbarcode-margin="0">
                            </svg>
                        </div>

                        <div class="d-flex justify-content-between align-items-center px-2 mt-1">
                            <div class="qr-box" id="qr-thermal-{{ $product->id }}-{{ $copy }}" data-url="{{ route('product.preview', $product->id) }}"></div>
                            <div class="text-end" style="line-height: 1.1;">
                                <span class="d-block text-muted" style="font-size: 8px;">SCAN FOR INFO</span>
                                <span class="fw-bold text-dark font-monospace" style="font-size: 9px;">{{ $product->barcode }}</span>
                            </div>
                        </div>
                    </div>
                @endfor
            @endforeach
        </div>
    @else
        <div class="card shadow border-0 p-4">
            <div class="a4-grid">
                @foreach($products as $product)
                    @for($copy = 1; $copy <= ($id ? 6 : 2); $copy++)
                        <div class="a4-label sticker-card">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <strong class="small text-truncate" style="max-width: 130px;">{{ $product->name }}</strong>
                                <span class="fw-bold text-primary small">₹{{ number_format($product->price, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="font-monospace small text-muted">{{ $product->sku ?? 'SKU-'.$product->id }}</span>
                                <span class="badge bg-light text-secondary border" style="font-size: 9px;">{{ $product->category->name ?? 'General' }}</span>
                            </div>

                            <svg class="barcode-svg" 
                                 jsbarcode-format="CODE128" 
                                 jsbarcode-value="{{ $product->barcode ?? ('890' . str_pad($product->id, 9, '0', STR_PAD_LEFT)) }}" 
                                 jsbarcode-width="1.2" 
                                 jsbarcode-height="28" 
                                 jsbarcode-fontSize="10" 
                                 jsbarcode-margin="0">
                            </svg>

                            <div class="d-flex justify-content-between align-items-center mt-2 px-1">
                                <div class="qr-box" id="qr-a4-{{ $product->id }}-{{ $copy }}" data-url="{{ route('product.preview', $product->id) }}"></div>
                                <div class="text-end" style="font-size: 9px;">
                                    <span class="text-muted d-block">Enterprise Stock</span>
                                    <span class="font-monospace fw-bold">{{ $product->barcode }}</span>
                                </div>
                            </div>
                        </div>
                    @endfor
                @endforeach
            </div>
        </div>
    @endif
</div>

<!-- JsBarcode & QRCode.js scripts -->
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Render all Code-128 Barcodes
    try {
        JsBarcode(".barcode-svg").init();
    } catch(e) {
        console.error("Barcode rendering error:", e);
    }

    // Render 2D QR Codes
    document.querySelectorAll(".qr-box").forEach(function(el) {
        var url = el.getAttribute("data-url");
        new QRCode(el, {
            text: url,
            width: 38,
            height: 38,
            colorDark: "#111827",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.M
        });
    });
});
</script>
@endsection
