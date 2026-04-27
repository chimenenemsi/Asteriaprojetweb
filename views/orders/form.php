<?php
$values = $values ?? [];
$errors = $errors ?? [];
$mode = $mode ?? 'create';
$order = $order ?? null;
$selectedProduct = $selectedProduct ?? null;
$backUrl = $area === 'frontoffice'
    ? route_url('frontoffice/products')
    : route_url('backoffice/orders');
$backLabel = $area === 'frontoffice' ? 'Back to Products' : 'Back to Orders';
$statusOptions = $statusOptions ?? order_backoffice_status_options();
$currentStatus = (string) ($values['status'] ?? 'PENDING');
if (!array_key_exists($currentStatus, $statusOptions)) {
    $currentStatus = 'PENDING';
}
$placedAt = !empty($order['created_at'])
    ? date('Y-m-d H:i', strtotime((string) $order['created_at']))
    : 'Saved automatically on submit';
$action = $mode === 'edit'
    ? route_url($area . '/orders/update', ['id' => (int) ($order['id'] ?? 0)])
    : route_url($area . '/orders/create');
?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
<style>
.delivery-map-shell {display:grid;gap:18px}
.delivery-map-toolbar {display:flex;flex-wrap:wrap;gap:12px;align-items:flex-end}
.delivery-map {height:360px;border-radius:22px;border:1px solid #cbd5e1;overflow:hidden;box-shadow:0 20px 50px rgba(15,23,42,.08)}
.delivery-map-hint {padding:14px 16px;border-radius:16px;background:linear-gradient(180deg,#f8fbff 0%,#f8fafc 100%);border:1px solid #d8e5f3;color:#475569}
.delivery-map-search {display:grid;grid-template-columns:minmax(0,1fr) auto auto;gap:12px}
.delivery-map-status {padding:12px 14px;border-radius:14px;background:#fff;border:1px solid #e2e8f0;color:#334155;font-size:13px}
.delivery-map-coordinates {display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}
@media (max-width:760px){.delivery-map-search{grid-template-columns:1fr}.delivery-map-coordinates{grid-template-columns:1fr}}
</style>
<div class="card">
    <div class="header-line">
        <div>
            <h1 style="margin:0"><?= htmlspecialchars($pageTitle ?? 'Order Form', ENT_QUOTES, 'UTF-8') ?></h1>
            <p class="muted"><?= $area === 'frontoffice' ? 'Place your order. The order date is automatic and delivery state is updated from backoffice.' : 'Create or update an order and manage its delivery state.' ?></p>
        </div>
        <a href="<?= htmlspecialchars($backUrl, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($backLabel, ENT_QUOTES, 'UTF-8') ?></a>
    </div>
</div>

<div class="card">
    <form method="post" action="<?= htmlspecialchars($action, ENT_QUOTES, 'UTF-8') ?>" class="form-shell">
        <div class="form-section">
            <h2>Order Setup</h2>
            <p>Select the product and quantity. Price totals are calculated from the catalog automatically.</p>
            <div class="row">
                <div class="col-6">
                    <div class="field">
                        <label for="order-product-select">Product</label>
                        <select name="product_id" id="order-product-select" required>
                            <option value="">Select product</option>
                            <?php foreach (($products ?? []) as $product): ?>
                                <option
                                    value="<?= (int) $product['id'] ?>"
                                    data-name="<?= htmlspecialchars((string) $product['name'], ENT_QUOTES, 'UTF-8') ?>"
                                    data-price="<?= htmlspecialchars((string) $product['price'], ENT_QUOTES, 'UTF-8') ?>"
                                    data-sku="<?= htmlspecialchars((string) $product['sku'], ENT_QUOTES, 'UTF-8') ?>"
                                    data-stock="<?= htmlspecialchars((string) $product['stock_quantity'], ENT_QUOTES, 'UTF-8') ?>"
                                    data-status="<?= htmlspecialchars((string) $product['status'], ENT_QUOTES, 'UTF-8') ?>"
                                    data-category="<?= htmlspecialchars((string) ($product['category_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                    <?= (string) ($values['product_id'] ?? '') === (string) $product['id'] ? 'selected' : '' ?>
                                >
                                    <?= htmlspecialchars((string) $product['name'], ENT_QUOTES, 'UTF-8') ?> - $<?= htmlspecialchars(number_format((float) $product['price'], 2), ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errors['product_id'])): ?><div class="error"><?= htmlspecialchars($errors['product_id'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
                    </div>
                </div>
                <div class="col-3">
                    <div class="field">
                        <label for="order-quantity-input">Quantity</label>
                        <input type="number" id="order-quantity-input" name="quantity" min="1" step="1" required value="<?= htmlspecialchars((string) ($values['quantity'] ?? 1), ENT_QUOTES, 'UTF-8') ?>">
                        <div class="field-help" id="order-stock-hint">Choose a quantity within the available stock.</div>
                        <?php if (isset($errors['quantity'])): ?><div class="error"><?= htmlspecialchars($errors['quantity'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
                    </div>
                </div>
                <?php if ($area === 'backoffice'): ?>
                    <div class="col-3">
                        <div class="field">
                            <label for="order-status-select">Delivery State</label>
                            <select id="order-status-select" name="status" required>
                                <?php foreach ($statusOptions as $status => $label): ?>
                                    <option value="<?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?>" <?= $currentStatus === $status ? 'selected' : '' ?>><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($errors['status'])): ?><div class="error"><?= htmlspecialchars($errors['status'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-section">
            <h2>Customer Details</h2>
            <p>Collect the contact and delivery information for the order.</p>
            <div class="row">
                <div class="col-5">
                    <div class="field">
                        <label for="customer-name">Customer Name</label>
                        <input id="customer-name" type="text" name="customer_name" minlength="2" maxlength="150" required placeholder="Customer full name" value="<?= htmlspecialchars((string) ($values['customer_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                        <?php if (isset($errors['customer_name'])): ?><div class="error"><?= htmlspecialchars($errors['customer_name'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
                    </div>
                </div>
                <div class="col-7">
                    <div class="field">
                        <label for="customer-email">Customer Email</label>
                        <input id="customer-email" type="email" name="customer_email" maxlength="255" required placeholder="name@example.com" value="<?= htmlspecialchars((string) ($values['customer_email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                        <?php if (isset($errors['customer_email'])): ?><div class="error"><?= htmlspecialchars($errors['customer_email'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
                    </div>
                </div>
                <div class="col-12">
                    <div class="field">
                        <label for="shipping-address">Shipping Address</label>
                        <textarea id="shipping-address" name="shipping_address" rows="4" maxlength="2000" required placeholder="Street, area, city, and anything needed for delivery"><?= htmlspecialchars((string) ($values['shipping_address'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
                        <?php if (isset($errors['shipping_address'])): ?><div class="error"><?= htmlspecialchars($errors['shipping_address'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
                    </div>
                </div>
                <div class="col-12">
                    <div class="field">
                        <label for="order-notes">Notes</label>
                        <textarea id="order-notes" name="notes" rows="4" maxlength="2000" placeholder="Special delivery notes or order comments"><?= htmlspecialchars((string) ($values['notes'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
                        <?php if (isset($errors['notes'])): ?><div class="error"><?= htmlspecialchars($errors['notes'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-section">
            <h2>Delivery Location</h2>
            <p>Search a place, use current location, or click the map. The location label will be filled with the actual place name instead of just raw coordinates whenever the map service can resolve it.</p>
            <div class="delivery-map-shell">
                <div class="delivery-map-toolbar">
                    <div class="field" style="flex:1 1 260px">
                        <label for="delivery-location-name">Location Label</label>
                        <input id="delivery-location-name" type="text" name="delivery_location_name" maxlength="255" required placeholder="Pin a spot on the map or type a short label" value="<?= htmlspecialchars((string) ($values['delivery_location_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                    </div>
                </div>
                <div class="delivery-map-search">
                    <div class="field" style="margin:0">
                        <label for="delivery-location-search">Find A Place</label>
                        <input id="delivery-location-search" type="text" placeholder="Search area, street, mall, landmark...">
                    </div>
                    <button class="btn btn-secondary" type="button" id="search-location" style="margin-top:28px">Search Map</button>
                    <button class="btn btn-secondary" type="button" id="use-current-location" style="margin-top:28px">Use Current Location</button>
                </div>
                <?php if (isset($errors['delivery_location_name'])): ?><div class="error"><?= htmlspecialchars($errors['delivery_location_name'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
                <div class="delivery-map-hint">Click anywhere on the map to drop the delivery marker. The system stores the coordinates, tries to reverse-geocode the exact place name, and keeps that readable label with the order.</div>
                <div id="delivery-map-status" class="delivery-map-status">No delivery point pinned yet.</div>
                <div id="delivery-map" class="delivery-map"></div>
                <div class="delivery-map-coordinates">
                    <div class="field">
                        <label for="delivery-latitude">Latitude</label>
                        <input id="delivery-latitude" class="readonly-input" type="text" name="delivery_latitude" readonly value="<?= htmlspecialchars((string) ($values['delivery_latitude'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                    </div>
                    <div class="field">
                        <label for="delivery-longitude">Longitude</label>
                        <input id="delivery-longitude" class="readonly-input" type="text" name="delivery_longitude" readonly value="<?= htmlspecialchars((string) ($values['delivery_longitude'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                    </div>
                </div>
            </div>
        </div>

        <div class="form-section">
            <h2>Product Snapshot</h2>
            <p>These details come from the product record and refresh automatically when you choose a different item.</p>
            <div class="summary-grid">
                <div class="summary-item">
                    <strong>Category</strong>
                    <span id="order-category-display"><?= htmlspecialchars((string) (($selectedProduct['category_name'] ?? '') ?: 'Select a product'), ENT_QUOTES, 'UTF-8') ?></span>
                </div>
                <div class="summary-item">
                    <strong>SKU</strong>
                    <span id="order-sku-display"><?= htmlspecialchars((string) (($selectedProduct['sku'] ?? '') ?: 'Select a product'), ENT_QUOTES, 'UTF-8') ?></span>
                </div>
                <div class="summary-item">
                    <strong>Available Stock</strong>
                    <span id="order-stock-display"><?= htmlspecialchars($selectedProduct !== null ? (string) ($selectedProduct['stock_quantity'] ?? 0) : 'Select a product', ENT_QUOTES, 'UTF-8') ?></span>
                </div>
                <div class="summary-item">
                    <strong>Product Status</strong>
                    <span id="order-product-status-display"><?= htmlspecialchars((string) (($selectedProduct['status'] ?? '') ?: 'Select a product'), ENT_QUOTES, 'UTF-8') ?></span>
                </div>
                <div class="summary-item">
                    <strong>Unit Price</strong>
                    <span id="order-price-display"><?= htmlspecialchars(($values['unit_price'] ?? '') === '' ? 'Calculated from product' : ('$' . number_format((float) $values['unit_price'], 2)), ENT_QUOTES, 'UTF-8') ?></span>
                </div>
                <div class="summary-item">
                    <strong>Total</strong>
                    <span id="order-total-display"><?= htmlspecialchars(($values['total_amount'] ?? '') === '' ? 'Calculated from quantity' : ('$' . number_format((float) $values['total_amount'], 2)), ENT_QUOTES, 'UTF-8') ?></span>
                </div>
                <div class="summary-item">
                    <strong>Placed On</strong>
                    <span><?= htmlspecialchars($placedAt, ENT_QUOTES, 'UTF-8') ?></span>
                </div>
                <div class="summary-item">
                    <strong>Delivery State</strong>
                    <span><?= htmlspecialchars($area === 'frontoffice' && $mode === 'create' ? 'Starts as Not delivered' : order_status_label((string) ($order['status'] ?? $currentStatus)), ENT_QUOTES, 'UTF-8') ?></span>
                </div>
            </div>
            <?php if (isset($errors['order_date'])): ?><div class="error" style="margin-top:12px"><?= htmlspecialchars($errors['order_date'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
        </div>

        <div class="actions">
            <button class="btn btn-primary" type="submit"><?= $mode === 'edit' ? 'Save Order' : ($area === 'frontoffice' ? 'Place Order' : 'Create Order') ?></button>
        </div>
    </form>
</div>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script>
(() => {
    const productSelect = document.getElementById('order-product-select');
    const quantityInput = document.getElementById('order-quantity-input');
    const categoryDisplay = document.getElementById('order-category-display');
    const skuDisplay = document.getElementById('order-sku-display');
    const stockDisplay = document.getElementById('order-stock-display');
    const statusDisplay = document.getElementById('order-product-status-display');
    const priceDisplay = document.getElementById('order-price-display');
    const totalDisplay = document.getElementById('order-total-display');
    const stockHint = document.getElementById('order-stock-hint');
    const locationNameInput = document.getElementById('delivery-location-name');
    const latitudeInput = document.getElementById('delivery-latitude');
    const longitudeInput = document.getElementById('delivery-longitude');
    const useCurrentLocationButton = document.getElementById('use-current-location');
    const searchLocationButton = document.getElementById('search-location');
    const searchLocationInput = document.getElementById('delivery-location-search');
    const mapStatus = document.getElementById('delivery-map-status');
    const initialLatitude = parseFloat(latitudeInput.value || '0');
    const initialLongitude = parseFloat(longitudeInput.value || '0');
    const hasInitialLocation = !Number.isNaN(initialLatitude) && !Number.isNaN(initialLongitude) && (latitudeInput.value !== '' && longitudeInput.value !== '');

    if (!productSelect || !quantityInput) {
        return;
    }

    const formatMoney = (value) => '$' + Number(value || 0).toFixed(2);

    const updateOrderPreview = () => {
        const selectedOption = productSelect.options[productSelect.selectedIndex];
        const quantity = Math.max(0, Number(quantityInput.value || 0));

        if (!selectedOption || !selectedOption.value) {
            categoryDisplay.textContent = 'Select a product';
            skuDisplay.textContent = 'Select a product';
            stockDisplay.textContent = 'Select a product';
            statusDisplay.textContent = 'Select a product';
            priceDisplay.textContent = 'Calculated from product';
            totalDisplay.textContent = 'Calculated from quantity';
            quantityInput.removeAttribute('max');
            stockHint.textContent = 'Choose a quantity within the available stock.';
            stockHint.style.color = '';
            return;
        }

        const price = Number(selectedOption.dataset.price || 0);
        const stock = Number(selectedOption.dataset.stock || 0);
        const status = selectedOption.dataset.status || '';
        const category = selectedOption.dataset.category || '';
        const sku = selectedOption.dataset.sku || '';

        categoryDisplay.textContent = category || 'N/A';
        skuDisplay.textContent = sku || 'N/A';
        stockDisplay.textContent = String(stock);
        statusDisplay.textContent = status || 'N/A';
        priceDisplay.textContent = formatMoney(price);
        totalDisplay.textContent = formatMoney(price * quantity);
        if (stock > 0) {
            quantityInput.max = String(stock);
        } else {
            quantityInput.removeAttribute('max');
        }

        if (quantity > stock) {
            stockHint.textContent = 'Requested quantity is higher than available stock.';
            stockHint.style.color = '#b91c1c';
        } else if (status !== 'ACTIVE') {
            stockHint.textContent = 'Only active products can be ordered.';
            stockHint.style.color = '#b45309';
        } else {
            stockHint.textContent = 'Stock is available for this quantity.';
            stockHint.style.color = '#166534';
        }
    };

    productSelect.addEventListener('change', updateOrderPreview);
    quantityInput.addEventListener('input', updateOrderPreview);
    updateOrderPreview();

    if (window.L) {
        const defaultCenter = hasInitialLocation ? [initialLatitude, initialLongitude] : [6.5244, 3.3792];
        const map = L.map('delivery-map').setView(defaultCenter, hasInitialLocation ? 13 : 6);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        let marker = null;

        const setStatus = (message) => {
            if (mapStatus) {
                mapStatus.textContent = message;
            }
        };

        const fallbackLabel = (lat, lng) => `Pinned near ${Number(lat).toFixed(4)}, ${Number(lng).toFixed(4)}`;

        const reverseGeocode = async (lat, lng) => {
            try {
                const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${encodeURIComponent(String(lat))}&lon=${encodeURIComponent(String(lng))}`);
                const payload = await response.json();
                return payload.display_name || '';
            } catch (error) {
                return '';
            }
        };

        const searchPlace = async (query) => {
            try {
                const response = await fetch(`https://nominatim.openstreetmap.org/search?format=jsonv2&limit=1&q=${encodeURIComponent(query)}`);
                const payload = await response.json();
                return Array.isArray(payload) && payload[0] ? payload[0] : null;
            } catch (error) {
                return null;
            }
        };

        const applyLocation = async (lat, lng, shouldPan = true, forceLabel = false) => {
            latitudeInput.value = Number(lat).toFixed(7);
            longitudeInput.value = Number(lng).toFixed(7);
            setStatus('Resolving the delivery place name...');

            if (!marker) {
                marker = L.marker([lat, lng]).addTo(map);
            } else {
                marker.setLatLng([lat, lng]);
            }

            if (shouldPan) {
                map.setView([lat, lng], Math.max(map.getZoom(), 13));
            }

            const resolvedName = await reverseGeocode(lat, lng);
            if (resolvedName && (!locationNameInput.value.trim() || forceLabel)) {
                locationNameInput.value = resolvedName;
            } else if (!locationNameInput.value.trim()) {
                locationNameInput.value = fallbackLabel(lat, lng);
            }

            setStatus(`Pinned delivery point: ${locationNameInput.value || fallbackLabel(lat, lng)}.`);
        };

        if (hasInitialLocation) {
            applyLocation(initialLatitude, initialLongitude, false, false);
        } else {
            setStatus('Search a place, use your current location, or click the map to pin the delivery point.');
        }

        map.on('click', (event) => {
            applyLocation(event.latlng.lat, event.latlng.lng, true, true);
        });

        if (useCurrentLocationButton) {
            useCurrentLocationButton.addEventListener('click', () => {
                if (!navigator.geolocation) {
                    setStatus('Your browser does not support current-location detection.');
                    return;
                }

                setStatus('Finding your current location...');
                navigator.geolocation.getCurrentPosition((position) => {
                    applyLocation(position.coords.latitude, position.coords.longitude, true, true);
                }, () => {
                    setStatus('Current location could not be retrieved. You can still search or pin manually.');
                });
            });
        }

        if (searchLocationButton && searchLocationInput) {
            const runSearch = async () => {
                const query = searchLocationInput.value.trim();
                if (!query) {
                    setStatus('Type a place name or address to search the map.');
                    return;
                }

                setStatus(`Searching "${query}"...`);
                const result = await searchPlace(query);
                if (!result) {
                    setStatus('No location match was found. Try a broader area or click the map manually.');
                    return;
                }

                await applyLocation(result.lat, result.lon, true, true);
            };

            searchLocationButton.addEventListener('click', runSearch);
            searchLocationInput.addEventListener('keydown', (event) => {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    runSearch();
                }
            });
        }
    }
})();
</script>
