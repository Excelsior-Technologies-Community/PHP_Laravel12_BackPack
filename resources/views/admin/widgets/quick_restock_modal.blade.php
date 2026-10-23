<div class="modal fade" id="quickRestockModal" tabindex="-1" aria-labelledby="quickRestockModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0">
            <form id="quickRestockForm" method="POST" action="">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="quickRestockModalLabel">
                        <i class="la la-cubes me-1"></i> Quick Stock Adjustment
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-light border d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <span class="text-muted d-block small">Product Name</span>
                            <strong id="modalProductName" class="fs-6 text-dark">-</strong>
                        </div>
                        <div class="text-end">
                            <span class="text-muted d-block small">Current Stock</span>
                            <span id="modalCurrentQtyBadge" class="badge bg-secondary fs-6">0</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Adjustment Type <span class="text-danger">*</span></label>
                        <select name="adjustment_type" id="modalAdjustmentType" class="form-select" required>
                            <option value="stock_in">➕ Stock In / Receipt (+ Units)</option>
                            <option value="stock_out">➖ Stock Out / Dispatch (- Units)</option>
                            <option value="damaged">⚠️ Damaged / Defective / Shrinkage (- Units)</option>
                            <option value="manual_override">✍️ Manual Audit Override (Set Exact Count)</option>
                        </select>
                        <div class="form-text small" id="typeHelpText">Add newly received units to current inventory.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold" id="qtyInputLabel">Units to Adjust <span class="text-danger">*</span></label>
                        <input type="number" name="quantity" id="modalQuantityInput" class="form-control form-control-lg" min="1" required placeholder="e.g. 25">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Reason / Reference Note</label>
                        <input type="text" name="reason" id="modalReasonInput" class="form-control" placeholder="e.g. Batch PO-2026-004 received, damaged transit box...">
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="la la-check-circle me-1"></i> Apply Adjustment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openQuickRestockModal(id, name, currentQty, restockLevel) {
    var form = document.getElementById('quickRestockForm');
    var actionUrl = '{{ url(config("backpack.base.route_prefix", "admin") . "/product") }}/' + id + '/quick-restock';
    form.action = actionUrl;

    document.getElementById('modalProductName').textContent = name;
    var badge = document.getElementById('modalCurrentQtyBadge');
    badge.textContent = currentQty + ' units';
    if (currentQty <= 0) {
        badge.className = 'badge bg-danger fs-6';
    } else if (currentQty <= restockLevel) {
        badge.className = 'badge bg-warning text-dark fs-6';
    } else {
        badge.className = 'badge bg-success fs-6';
    }

    var qtyInput = document.getElementById('modalQuantityInput');
    qtyInput.value = '';
    qtyInput.min = '1';
    document.getElementById('modalReasonInput').value = '';

    var typeSelect = document.getElementById('modalAdjustmentType');
    typeSelect.value = 'stock_in';
    updateTypeHelper();

    var modalElement = document.getElementById('quickRestockModal');
    var modal = new bootstrap.Modal(modalElement);
    modal.show();
}

document.addEventListener('DOMContentLoaded', function() {
    var typeSelect = document.getElementById('modalAdjustmentType');
    if (typeSelect) {
        typeSelect.addEventListener('change', updateTypeHelper);
    }
});

function updateTypeHelper() {
    var typeSelect = document.getElementById('modalAdjustmentType');
    var helpText = document.getElementById('typeHelpText');
    var qtyLabel = document.getElementById('qtyInputLabel');
    var qtyInput = document.getElementById('modalQuantityInput');

    if (typeSelect.value === 'stock_in') {
        helpText.textContent = 'Add newly received units to current inventory.';
        qtyLabel.innerHTML = 'Units to Add <span class="text-danger">*</span>';
        qtyInput.min = '1';
    } else if (typeSelect.value === 'stock_out') {
        helpText.textContent = 'Subtract fulfilled or dispatched units from inventory.';
        qtyLabel.innerHTML = 'Units to Remove <span class="text-danger">*</span>';
        qtyInput.min = '1';
    } else if (typeSelect.value === 'damaged') {
        helpText.textContent = 'Write-off broken, spoiled or damaged stock.';
        qtyLabel.innerHTML = 'Units Damaged/Lost <span class="text-danger">*</span>';
        qtyInput.min = '1';
    } else if (typeSelect.value === 'manual_override') {
        helpText.textContent = 'Directly overwrite total inventory count to this exact value.';
        qtyLabel.innerHTML = 'New Exact Stock Count <span class="text-danger">*</span>';
        qtyInput.min = '0';
    }
}
</script>
