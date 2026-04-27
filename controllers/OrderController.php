<?php
declare(strict_types=1);

require_once ROOT_PATH . '/controllers/BaseController.php';

class OrderController extends BaseController
{
    private static bool $orderLocationSchemaReady = false;

    public function index(string $area): void
    {
        $this->ensureOrderLocationSchema();
        $filters = $this->orderFilters($_GET);

        $this->render('orders/index', [
            'pageTitle' => 'Orders',
            'area' => $area,
            'currentSection' => 'orders',
            'orders' => $this->allOrders($filters),
            'orderFilters' => $filters,
            'products' => $this->allProducts(),
            'orderStats' => $this->orderStatistics($filters),
        ], $area);
    }

    public function exportPdf(string $area): void
    {
        $this->ensureOrderLocationSchema();
        $filters = $this->orderFilters($_GET);
        $orders = $this->allOrders($filters);
        $lines = ['Orders Export', ''];
        $lines[] = 'Area: ' . $area;
        $lines[] = 'Records: ' . count($orders);
        $lines[] = 'Filters: search=' . ($filters['search'] !== '' ? $filters['search'] : 'all')
            . ', status=' . ($filters['status'] !== '' ? $filters['status'] : 'all')
            . ', product=' . ($filters['product_id'] !== '' ? $filters['product_id'] : 'all')
            . ', location=' . ($filters['location'] !== '' ? $filters['location'] : 'all')
            . ', sort=' . $filters['sort'];
        $lines[] = '';

        foreach ($orders as $order) {
            $lines[] = sprintf(
                '%s | %s | qty %d | $%0.2f | %s | %s',
                (string) $order['customer_name'],
                (string) $order['product_name'],
                (int) $order['quantity'],
                (float) $order['total_amount'],
                (string) (($order['delivery_location_name'] ?? '') ?: 'Not pinned'),
                (string) $order['status']
            );
        }

        $this->downloadSimplePdf('orders.pdf', $lines);
    }

    public function show(string $area): void
    {
        $this->ensureOrderLocationSchema();
        $order = $this->findOrder((int) ($_GET['id'] ?? 0));
        if ($order === null) {
            $this->renderNotFound();
            return;
        }

        $this->render('orders/show', [
            'pageTitle' => 'Order',
            'area' => $area,
            'currentSection' => 'orders',
            'order' => $order,
        ], $area);
    }

    public function form(string $area, string $mode): void
    {
        $this->ensureOrderLocationSchema();
        $order = $mode === 'edit' ? $this->findOrder((int) ($_GET['id'] ?? 0)) : null;
        if ($mode === 'edit' && $order === null) {
            $this->renderNotFound();
            return;
        }

        $productId = (int) ($_GET['product_id'] ?? ($order['product_id'] ?? 0));
        $products = $this->allProducts();
        $selectedProduct = null;
        foreach ($products as $product) {
            if ((int) $product['id'] === $productId) {
                $selectedProduct = $product;
                break;
            }
        }

        $this->render('orders/form', [
            'pageTitle' => $mode === 'edit' ? 'Edit Order' : ($area === 'frontoffice' ? 'Place Order' : 'New Order'),
            'area' => $area,
            'currentSection' => 'orders',
            'mode' => $mode,
            'order' => $order,
            'products' => $products,
            'selectedProduct' => $selectedProduct,
            'errors' => [],
            'statusOptions' => order_backoffice_status_options(),
            'values' => $order ?? [
                'product_id' => $productId > 0 ? $productId : '',
                'customer_name' => '',
                'customer_email' => '',
                'quantity' => 1,
                'status' => 'PENDING',
                'order_date' => date('Y-m-d'),
                'shipping_address' => '',
                'delivery_location_name' => '',
                'delivery_latitude' => '',
                'delivery_longitude' => '',
                'notes' => '',
                'unit_price' => $selectedProduct !== null ? (float) ($selectedProduct['price'] ?? 0) : '',
                'total_amount' => $selectedProduct !== null ? (float) ($selectedProduct['price'] ?? 0) : '',
            ],
        ], $area);
    }

    public function create(string $area): void
    {
        $this->ensureOrderLocationSchema();
        $data = $this->prepare($_POST, $area, 'create');
        $errors = $this->validateOrder($data);
        if ($errors !== []) {
            if ($area === 'frontoffice' && (string) ($_POST['inline_order'] ?? '') === '1') {
                $this->render('products/index', [
                    'pageTitle' => 'Products',
                    'area' => $area,
                    'currentSection' => 'products',
                    'products' => $this->allProducts(),
                    'orderErrors' => $errors,
                    'orderValues' => array_merge($_POST, $data),
                    'orderProductId' => (int) ($data['product_id'] ?? 0),
                ], $area);
                return;
            }

            $this->render('orders/form', [
                'pageTitle' => $area === 'frontoffice' ? 'Place Order' : 'New Order',
                'area' => $area,
                'currentSection' => 'orders',
                'mode' => 'create',
                'products' => $this->allProducts(),
                'selectedProduct' => $this->findProduct((int) ($data['product_id'] ?? 0)),
                'errors' => $errors,
                'statusOptions' => order_backoffice_status_options(),
                'values' => array_merge($_POST, $data),
            ], $area);
            return;
        }

        $id = $this->createOrder($data);
        $this->redirect($area . '/orders/show', ['id' => $id]);
    }

    public function update(string $area): void
    {
        $this->ensureOrderLocationSchema();
        $id = (int) ($_GET['id'] ?? 0);
        $order = $this->findOrder($id);
        if ($order === null) {
            $this->renderNotFound();
            return;
        }

        $data = $this->prepare($_POST, $area, 'edit', $order);
        $errors = $this->validateOrder($data);
        if ($errors !== []) {
            $this->render('orders/form', [
                'pageTitle' => 'Edit Order',
                'area' => $area,
                'currentSection' => 'orders',
                'mode' => 'edit',
                'order' => $order,
                'products' => $this->allProducts(),
                'selectedProduct' => $this->findProduct((int) ($data['product_id'] ?? 0)),
                'errors' => $errors,
                'statusOptions' => order_backoffice_status_options(),
                'values' => array_merge($order, $_POST, $data),
            ], $area);
            return;
        }

        $this->updateOrder($id, $data);
        $this->redirect($area . '/orders/show', ['id' => $id]);
    }

    public function delete(string $area): void
    {
        $this->ensureOrderLocationSchema();
        $this->deleteOrder((int) ($_GET['id'] ?? 0));
        $this->redirect($area . '/orders');
    }

    private function prepare(array $source, string $area, string $mode, ?array $existingOrder = null): array
    {
        $data = $this->normalizeOrder($source);
        $product = $this->findProduct((int) ($data['product_id'] ?? 0));
        if ($product !== null) {
            $data['product_name'] = (string) ($product['name'] ?? '');
            $data['unit_price'] = (float) ($product['price'] ?? 0);
            $data['total_amount'] = $data['unit_price'] * (int) ($data['quantity'] ?? 0);
        }

        if ($mode === 'create') {
            $data['order_date'] = date('Y-m-d');
            $data['status'] = $area === 'frontoffice'
                ? 'PENDING'
                : ((string) ($data['status'] ?? '') !== '' ? (string) $data['status'] : 'PENDING');
        } else {
            $data['order_date'] = (string) ($existingOrder['order_date'] ?? date('Y-m-d'));
            $data['status'] = $area === 'frontoffice'
                ? (string) ($existingOrder['status'] ?? 'PENDING')
                : (string) ($data['status'] ?? ($existingOrder['status'] ?? 'PENDING'));
        }

        return $data;
    }

    private function allOrders(array $filters = []): array
    {
        $query = 'SELECT o.*, p.name AS current_product_name, p.sku AS current_product_sku, '
            . 'p.stock_quantity AS current_product_stock, p.status AS current_product_status, '
            . 'c.name AS current_category_name FROM orders o '
            . 'LEFT JOIN products p ON p.id = o.product_id '
            . 'LEFT JOIN product_categories c ON c.id = p.product_category_id '
            . 'WHERE 1=1';
        $params = [];

        if (($filters['search'] ?? '') !== '') {
            $searchTerm = '%' . $filters['search'] . '%';
            $query .= ' AND (o.customer_name LIKE :search_customer OR o.customer_email LIKE :search_email OR o.product_name LIKE :search_product OR COALESCE(o.delivery_location_name, "") LIKE :search_location)';
            $params['search_customer'] = $searchTerm;
            $params['search_email'] = $searchTerm;
            $params['search_product'] = $searchTerm;
            $params['search_location'] = $searchTerm;
        }

        if (($filters['status'] ?? '') !== '') {
            $query .= ' AND o.status = :status';
            $params['status'] = $filters['status'];
        }

        if (($filters['product_id'] ?? '') !== '') {
            $query .= ' AND o.product_id = :product_id';
            $params['product_id'] = (int) $filters['product_id'];
        }

        if (($filters['location'] ?? '') === 'pinned') {
            $query .= ' AND o.delivery_latitude IS NOT NULL AND o.delivery_longitude IS NOT NULL';
        } elseif (($filters['location'] ?? '') === 'missing') {
            $query .= ' AND (o.delivery_latitude IS NULL OR o.delivery_longitude IS NULL)';
        }

        $sort = $filters['sort'] ?? 'created_desc';
        $query .= ' ORDER BY ' . match ($sort) {
            'customer_asc' => 'o.customer_name ASC, o.id DESC',
            'total_desc' => 'o.total_amount DESC, o.id DESC',
            'total_asc' => 'o.total_amount ASC, o.id DESC',
            'quantity_desc' => 'o.quantity DESC, o.id DESC',
            'date_asc' => 'o.order_date ASC, o.id ASC',
            default => 'o.id DESC',
        };
        $stmt = $this->connection()->prepare($query);
        $this->executeNamed($stmt, $params);

        return $stmt->fetchAll();
    }

    private function findOrder(int $id): ?array
    {
        $stmt = $this->connection()->prepare(
            'SELECT o.*, p.name AS current_product_name, p.sku AS current_product_sku, '
            . 'p.stock_quantity AS current_product_stock, p.status AS current_product_status, '
            . 'c.name AS current_category_name FROM orders o '
            . 'LEFT JOIN products p ON p.id = o.product_id '
            . 'LEFT JOIN product_categories c ON c.id = p.product_category_id '
            . 'WHERE o.id = :id'
        );
        $this->executeNamed($stmt, ['id' => $id]);
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    private function createOrder(array $data): int
    {
        $stmt = $this->connection()->prepare(
            'INSERT INTO orders (product_id, product_name, customer_name, customer_email, quantity, unit_price, total_amount, status, order_date, shipping_address, delivery_location_name, delivery_latitude, delivery_longitude, notes) '
            . 'VALUES (:product_id, :product_name, :customer_name, :customer_email, :quantity, :unit_price, :total_amount, :status, :order_date, :shipping_address, :delivery_location_name, :delivery_latitude, :delivery_longitude, :notes)'
        );
        $this->executeNamed($stmt, $data);

        return (int) $this->connection()->lastInsertId();
    }

    private function updateOrder(int $id, array $data): bool
    {
        $stmt = $this->connection()->prepare(
            'UPDATE orders SET product_id = :product_id, product_name = :product_name, customer_name = :customer_name, '
            . 'customer_email = :customer_email, quantity = :quantity, unit_price = :unit_price, total_amount = :total_amount, '
            . 'status = :status, order_date = :order_date, shipping_address = :shipping_address, '
            . 'delivery_location_name = :delivery_location_name, delivery_latitude = :delivery_latitude, '
            . 'delivery_longitude = :delivery_longitude, notes = :notes WHERE id = :id'
        );

        return $this->executeNamed($stmt, ['id' => $id] + $data);
    }

    private function deleteOrder(int $id): bool
    {
        $stmt = $this->connection()->prepare('DELETE FROM orders WHERE id = :id');

        return $this->executeNamed($stmt, ['id' => $id]);
    }

    private function allProducts(): array
    {
        $stmt = $this->connection()->query(
            'SELECT p.*, c.name AS category_name FROM products p '
            . 'INNER JOIN product_categories c ON c.id = p.product_category_id '
            . 'ORDER BY p.id DESC'
        );

        return $stmt->fetchAll();
    }

    private function findProduct(int $id): ?array
    {
        $stmt = $this->connection()->prepare(
            'SELECT p.*, c.name AS category_name FROM products p '
            . 'INNER JOIN product_categories c ON c.id = p.product_category_id '
            . 'WHERE p.id = :id'
        );
        $this->executeNamed($stmt, ['id' => $id]);
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    private function validateOrder(array $data): array
    {
        $errors = [];
        $customerName = trim((string) ($data['customer_name'] ?? ''));
        $email = trim((string) ($data['customer_email'] ?? ''));
        $shippingAddress = trim((string) ($data['shipping_address'] ?? ''));
        $deliveryLocation = trim((string) ($data['delivery_location_name'] ?? ''));
        $notes = trim((string) ($data['notes'] ?? ''));

        $productId = $data['product_id'] ?? null;
        $product = null;
        if (!is_numeric($productId) || (int) $productId <= 0 || ($product = $this->findProduct((int) $productId)) === null) {
            $errors['product_id'] = 'Product is required.';
        }

        if ($product !== null) {
            if (($product['status'] ?? '') !== 'ACTIVE') {
                $errors['product_id'] = 'Only active products can be ordered.';
            }

            if ((int) ($product['stock_quantity'] ?? 0) < 1) {
                $errors['quantity'] = 'This product is currently out of stock.';
            }

            $quantity = $data['quantity'] ?? null;
            if (is_numeric($quantity) && (int) $quantity > (int) ($product['stock_quantity'] ?? 0)) {
                $errors['quantity'] = 'Quantity exceeds available stock.';
            }
        }

        if ($customerName === '') {
            $errors['customer_name'] = 'Customer name is required.';
        } elseif (strlen($customerName) < 2 || strlen($customerName) > 150) {
            $errors['customer_name'] = 'Customer name must be between 2 and 150 characters.';
        }

        if ($email === '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $errors['customer_email'] = 'Customer email must be valid.';
        } elseif (strlen($email) > 255) {
            $errors['customer_email'] = 'Customer email must be 255 characters or fewer.';
        }

        if (
            filter_var($data['quantity'] ?? null, FILTER_VALIDATE_INT) === false
            || (int) ($data['quantity'] ?? 0) < 1
        ) {
            $errors['quantity'] = 'Quantity must be a whole number of at least 1.';
        }

        if (!in_array((string) ($data['status'] ?? 'PENDING'), ['PENDING', 'PAID', 'SHIPPED', 'CANCELLED'], true)) {
            $errors['status'] = 'Invalid status.';
        }

        $orderDate = (string) ($data['order_date'] ?? '');
        $date = DateTimeImmutable::createFromFormat('Y-m-d', $orderDate);
        if ($date === false || $date->format('Y-m-d') !== $orderDate) {
            $errors['order_date'] = 'Order date must be valid.';
        }

        if ($shippingAddress === '') {
            $errors['shipping_address'] = 'Shipping address is required.';
        } elseif (strlen($shippingAddress) > 2000) {
            $errors['shipping_address'] = 'Shipping address must be 2000 characters or fewer.';
        }

        if ($deliveryLocation === '') {
            $errors['delivery_location_name'] = 'Pick a delivery location on the map.';
        } elseif (strlen($deliveryLocation) > 255) {
            $errors['delivery_location_name'] = 'Delivery location must be 255 characters or fewer.';
        }

        if (!is_numeric($data['delivery_latitude'] ?? null) || (float) ($data['delivery_latitude'] ?? 999) < -90 || (float) ($data['delivery_latitude'] ?? 999) > 90) {
            $errors['delivery_location_name'] = 'Choose a valid delivery point on the map.';
        }

        if (!is_numeric($data['delivery_longitude'] ?? null) || (float) ($data['delivery_longitude'] ?? 999) < -180 || (float) ($data['delivery_longitude'] ?? 999) > 180) {
            $errors['delivery_location_name'] = 'Choose a valid delivery point on the map.';
        }

        if ($notes !== '' && strlen($notes) > 2000) {
            $errors['notes'] = 'Notes must be 2000 characters or fewer.';
        }

        return $errors;
    }

    private function normalizeOrder(array $data): array
    {
        return [
            'product_id' => (int) ($data['product_id'] ?? 0),
            'product_name' => trim((string) ($data['product_name'] ?? '')),
            'customer_name' => trim((string) ($data['customer_name'] ?? '')),
            'customer_email' => trim((string) ($data['customer_email'] ?? '')),
            'quantity' => is_numeric($data['quantity'] ?? null) ? (int) $data['quantity'] : 0,
            'unit_price' => is_numeric($data['unit_price'] ?? null) ? (float) $data['unit_price'] : 0.0,
            'total_amount' => is_numeric($data['total_amount'] ?? null) ? (float) $data['total_amount'] : 0.0,
            'status' => (string) ($data['status'] ?? 'PENDING'),
            'order_date' => trim((string) ($data['order_date'] ?? '')),
            'shipping_address' => trim((string) ($data['shipping_address'] ?? '')) ?: null,
            'delivery_location_name' => trim((string) ($data['delivery_location_name'] ?? '')) ?: null,
            'delivery_latitude' => is_numeric($data['delivery_latitude'] ?? null) ? (float) $data['delivery_latitude'] : null,
            'delivery_longitude' => is_numeric($data['delivery_longitude'] ?? null) ? (float) $data['delivery_longitude'] : null,
            'notes' => trim((string) ($data['notes'] ?? '')) ?: null,
        ];
    }

    private function ensureOrderLocationSchema(): void
    {
        if (self::$orderLocationSchemaReady) {
            return;
        }

        $this->ensureOrderColumn('delivery_location_name', 'ALTER TABLE orders ADD COLUMN delivery_location_name VARCHAR(255) NULL AFTER shipping_address');
        $this->ensureOrderColumn('delivery_latitude', 'ALTER TABLE orders ADD COLUMN delivery_latitude DECIMAL(10,7) NULL AFTER delivery_location_name');
        $this->ensureOrderColumn('delivery_longitude', 'ALTER TABLE orders ADD COLUMN delivery_longitude DECIMAL(10,7) NULL AFTER delivery_latitude');

        self::$orderLocationSchemaReady = true;
    }

    private function ensureOrderColumn(string $column, string $sql): void
    {
        $statement = $this->connection()->prepare(
            'SELECT COUNT(*) FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = :table_name
               AND COLUMN_NAME = :column_name'
        );
        $this->executeNamed($statement, [
            'table_name' => 'orders',
            'column_name' => $column,
        ]);

        if ((int) $statement->fetchColumn() === 0) {
            $this->connection()->exec($sql);
        }
    }

    private function orderFilters(array $source): array
    {
        return [
            'search' => trim((string) ($source['search'] ?? '')),
            'status' => trim((string) ($source['status'] ?? '')),
            'product_id' => trim((string) ($source['product_id'] ?? '')),
            'location' => trim((string) ($source['location'] ?? '')),
            'sort' => trim((string) ($source['sort'] ?? 'created_desc')),
        ];
    }

    private function orderStatistics(array $filters = []): array
    {
        $query = 'SELECT COUNT(*) AS total_orders,
                         COALESCE(SUM(quantity), 0) AS total_units,
                         COALESCE(SUM(total_amount), 0) AS total_revenue,
                         SUM(CASE WHEN status = "PENDING" THEN 1 ELSE 0 END) AS pending_orders,
                         SUM(CASE WHEN status = "SHIPPED" THEN 1 ELSE 0 END) AS shipped_orders,
                         SUM(CASE WHEN status = "CANCELLED" THEN 1 ELSE 0 END) AS cancelled_orders
                  FROM orders
                  WHERE 1=1';
        $params = [];

        if (($filters['search'] ?? '') !== '') {
            $searchTerm = '%' . $filters['search'] . '%';
            $query .= ' AND (customer_name LIKE :search_customer OR customer_email LIKE :search_email OR product_name LIKE :search_product OR COALESCE(delivery_location_name, "") LIKE :search_location)';
            $params['search_customer'] = $searchTerm;
            $params['search_email'] = $searchTerm;
            $params['search_product'] = $searchTerm;
            $params['search_location'] = $searchTerm;
        }

        if (($filters['status'] ?? '') !== '') {
            $query .= ' AND status = :status';
            $params['status'] = $filters['status'];
        }

        if (($filters['product_id'] ?? '') !== '') {
            $query .= ' AND product_id = :product_id';
            $params['product_id'] = (int) $filters['product_id'];
        }

        if (($filters['location'] ?? '') === 'pinned') {
            $query .= ' AND delivery_latitude IS NOT NULL AND delivery_longitude IS NOT NULL';
        } elseif (($filters['location'] ?? '') === 'missing') {
            $query .= ' AND (delivery_latitude IS NULL OR delivery_longitude IS NULL)';
        }

        $stmt = $this->connection()->prepare($query);
        $this->executeNamed($stmt, $params);
        $stats = $stmt->fetch() ?: [];

        return [
            'total_orders' => (int) ($stats['total_orders'] ?? 0),
            'total_units' => (int) ($stats['total_units'] ?? 0),
            'total_revenue' => (float) ($stats['total_revenue'] ?? 0),
            'pending_orders' => (int) ($stats['pending_orders'] ?? 0),
            'shipped_orders' => (int) ($stats['shipped_orders'] ?? 0),
            'cancelled_orders' => (int) ($stats['cancelled_orders'] ?? 0),
        ];
    }
}
