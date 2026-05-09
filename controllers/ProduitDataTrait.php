<?php
declare(strict_types=1);

require_once ROOT_PATH . '/models/ProductCategory.php';
require_once ROOT_PATH . '/models/Product.php';
require_once ROOT_PATH . '/models/Order.php';

trait ProduitDataTrait
{
    private const CATEGORY_STATUSES = ['ACTIVE', 'INACTIVE'];
    private const PRODUCT_STATUSES = ['ACTIVE', 'DRAFT', 'OUT_OF_STOCK'];
    private const ORDER_STATUSES = ['PENDING', 'PAID', 'SHIPPED', 'CANCELLED'];

    /**
     * @return ProductCategory[]
     */
    protected function fetchAllCategories(): array
    {
        $statement = $this->connection()->query('SELECT * FROM product_categories ORDER BY id DESC');

        return array_map([$this, 'mapCategoryRow'], $statement->fetchAll());
    }

    protected function findCategoryById(int $id): ?ProductCategory
    {
        $statement = $this->connection()->prepare('SELECT * FROM product_categories WHERE id = :id');
        $statement->execute(['id' => $id]);
        $row = $statement->fetch();

        return $row === false ? null : $this->mapCategoryRow($row);
    }

    protected function prepareCategoryFormData(ProductCategory|array|null $source): array
    {
        if ($source instanceof ProductCategory) {
            return [
                'name' => $source->getName(),
                'description' => (string) ($source->getDescription() ?? ''),
                'status' => $source->getStatus(),
            ];
        }

        if ($source === null) {
            return [
                'name' => '',
                'description' => '',
                'status' => 'ACTIVE',
            ];
        }

        return [
            'name' => $this->cleanValue($source['name'] ?? ''),
            'description' => $this->cleanValue($source['description'] ?? ''),
            'status' => $this->cleanValue($source['status'] ?? 'ACTIVE'),
        ];
    }

    protected function validateCategory(array $data): array
    {
        $values = $this->prepareCategoryFormData($data);
        $errors = [];

        if ($values['name'] === '') {
            $errors['name'] = 'Name is required.';
        } elseif (mb_strlen($values['name']) < 2 || mb_strlen($values['name']) > 150) {
            $errors['name'] = 'Name must be between 2 and 150 characters.';
        }

        if (!in_array($values['status'], self::CATEGORY_STATUSES, true)) {
            $errors['status'] = 'Invalid status.';
        }

        if ($values['description'] !== '' && mb_strlen($values['description']) > 2000) {
            $errors['description'] = 'Description must be 2000 characters or fewer.';
        }

        return [
            'values' => $values,
            'errors' => $errors,
        ];
    }

    protected function categoryFromValues(array $values, ?int $id = null): ProductCategory
    {
        return new ProductCategory(
            $id,
            $values['name'],
            $this->nullableString($values['description']),
            $values['status']
        );
    }

    protected function createCategory(ProductCategory $category): int
    {
        $statement = $this->connection()->prepare(
            'INSERT INTO product_categories (name, description, status) VALUES (:name, :description, :status)'
        );
        $statement->execute($this->categoryPayload($category));

        $id = (int) $this->connection()->lastInsertId();
        $category->setId($id);

        return $id;
    }

    protected function updateCategory(ProductCategory $category): void
    {
        $payload = $this->categoryPayload($category);
        $payload['id'] = $category->getId();

        $statement = $this->connection()->prepare(
            'UPDATE product_categories SET name = :name, description = :description, status = :status WHERE id = :id'
        );
        $statement->execute($payload);
    }

    protected function deleteCategory(int $id): void
    {
        $statement = $this->connection()->prepare('DELETE FROM product_categories WHERE id = :id');
        $statement->execute(['id' => $id]);
    }

    /**
     * @return Product[]
     */
    protected function fetchAllProducts(): array
    {
        $statement = $this->connection()->query(
            'SELECT p.*, c.name AS category_name FROM products p
            INNER JOIN product_categories c ON c.id = p.product_category_id
            ORDER BY p.id DESC'
        );

        return array_map([$this, 'mapProductRow'], $statement->fetchAll());
    }

    /**
     * @return Product[]
     */
    protected function fetchProductsByCategory(int $categoryId): array
    {
        $statement = $this->connection()->prepare(
            'SELECT p.*, c.name AS category_name FROM products p
            INNER JOIN product_categories c ON c.id = p.product_category_id
            WHERE p.product_category_id = :product_category_id
            ORDER BY p.id DESC'
        );
        $statement->execute(['product_category_id' => $categoryId]);

        return array_map([$this, 'mapProductRow'], $statement->fetchAll());
    }

    protected function findProductById(int $id): ?Product
    {
        $statement = $this->connection()->prepare(
            'SELECT p.*, c.name AS category_name FROM products p
            INNER JOIN product_categories c ON c.id = p.product_category_id
            WHERE p.id = :id'
        );
        $statement->execute(['id' => $id]);
        $row = $statement->fetch();

        return $row === false ? null : $this->mapProductRow($row);
    }

    protected function prepareProductFormData(Product|array|null $source, int $selectedCategoryId = 0): array
    {
        if ($source instanceof Product) {
            return [
                'product_category_id' => (string) ($source->getProductCategoryId() ?? ($selectedCategoryId > 0 ? $selectedCategoryId : '')),
                'name' => $source->getName(),
                'sku' => $source->getSku(),
                'price' => $source->getPrice() > 0 ? (string) $source->getPrice() : '',
                'stock_quantity' => (string) $source->getStockQuantity(),
                'status' => $source->getStatus(),
                'description' => (string) ($source->getDescription() ?? ''),
            ];
        }

        if ($source === null) {
            return [
                'product_category_id' => $selectedCategoryId > 0 ? (string) $selectedCategoryId : '',
                'name' => '',
                'sku' => '',
                'price' => '',
                'stock_quantity' => '0',
                'status' => 'ACTIVE',
                'description' => '',
            ];
        }

        return [
            'product_category_id' => $this->cleanValue($source['product_category_id'] ?? ($selectedCategoryId > 0 ? (string) $selectedCategoryId : '')),
            'name' => $this->cleanValue($source['name'] ?? ''),
            'sku' => strtoupper($this->cleanValue($source['sku'] ?? '')),
            'price' => $this->cleanValue($source['price'] ?? ''),
            'stock_quantity' => $this->cleanValue($source['stock_quantity'] ?? '0'),
            'status' => $this->cleanValue($source['status'] ?? 'ACTIVE'),
            'description' => $this->cleanValue($source['description'] ?? ''),
        ];
    }

    protected function validateProduct(array $data): array
    {
        $values = $this->prepareProductFormData($data, (int) ($data['product_category_id'] ?? 0));
        $errors = [];

        $categoryId = $values['product_category_id'];
        if (!ctype_digit($categoryId) || (int) $categoryId <= 0 || $this->findCategoryById((int) $categoryId) === null) {
            $errors['product_category_id'] = 'Category is required.';
        }

        if ($values['name'] === '') {
            $errors['name'] = 'Name is required.';
        } elseif (mb_strlen($values['name']) < 2 || mb_strlen($values['name']) > 150) {
            $errors['name'] = 'Name must be between 2 and 150 characters.';
        }

        if ($values['sku'] === '') {
            $errors['sku'] = 'SKU is required.';
        } elseif (!preg_match('/^[A-Z0-9][A-Z0-9_-]{2,79}$/', $values['sku'])) {
            $errors['sku'] = 'SKU must be 3 to 80 characters using letters, numbers, dashes, or underscores.';
        }

        if (!is_numeric($values['price']) || (float) $values['price'] <= 0) {
            $errors['price'] = 'Price must be greater than 0.';
        }

        if (
            filter_var($values['stock_quantity'], FILTER_VALIDATE_INT) === false
            || (int) $values['stock_quantity'] < 0
        ) {
            $errors['stock_quantity'] = 'Stock quantity must be a whole number of 0 or more.';
        }

        if (!in_array($values['status'], self::PRODUCT_STATUSES, true)) {
            $errors['status'] = 'Invalid status.';
        }

        if ($values['description'] !== '' && mb_strlen($values['description']) > 4000) {
            $errors['description'] = 'Description must be 4000 characters or fewer.';
        }

        return [
            'values' => $values,
            'errors' => $errors,
        ];
    }

    protected function productFromValues(array $values, ?int $id = null): Product
    {
        return new Product(
            $id,
            (int) $values['product_category_id'],
            $values['name'],
            $values['sku'],
            (float) $values['price'],
            (int) $values['stock_quantity'],
            $values['status'],
            $this->nullableString($values['description'])
        );
    }

    protected function createProduct(Product $product): int
    {
        $statement = $this->connection()->prepare(
            'INSERT INTO products (product_category_id, name, sku, price, stock_quantity, status, description)
            VALUES (:product_category_id, :name, :sku, :price, :stock_quantity, :status, :description)'
        );
        $statement->execute($this->productPayload($product));

        $id = (int) $this->connection()->lastInsertId();
        $product->setId($id);

        return $id;
    }

    protected function updateProduct(Product $product): void
    {
        $payload = $this->productPayload($product);
        $payload['id'] = $product->getId();

        $statement = $this->connection()->prepare(
            'UPDATE products
            SET product_category_id = :product_category_id,
                name = :name,
                sku = :sku,
                price = :price,
                stock_quantity = :stock_quantity,
                status = :status,
                description = :description
            WHERE id = :id'
        );
        $statement->execute($payload);
    }

    protected function deleteProduct(int $id): void
    {
        $statement = $this->connection()->prepare('DELETE FROM products WHERE id = :id');
        $statement->execute(['id' => $id]);
    }

    /**
     * @return Order[]
     */
    protected function fetchAllOrders(): array
    {
        $statement = $this->connection()->query(
            'SELECT o.*, p.name AS current_product_name, p.sku AS current_product_sku,
            p.stock_quantity AS current_product_stock, p.status AS current_product_status,
            c.name AS current_category_name
            FROM orders o
            LEFT JOIN products p ON p.id = o.product_id
            LEFT JOIN product_categories c ON c.id = p.product_category_id
            ORDER BY o.id DESC'
        );

        return array_map([$this, 'mapOrderRow'], $statement->fetchAll());
    }

    protected function findOrderById(int $id): ?Order
    {
        $statement = $this->connection()->prepare(
            'SELECT o.*, p.name AS current_product_name, p.sku AS current_product_sku,
            p.stock_quantity AS current_product_stock, p.status AS current_product_status,
            c.name AS current_category_name
            FROM orders o
            LEFT JOIN products p ON p.id = o.product_id
            LEFT JOIN product_categories c ON c.id = p.product_category_id
            WHERE o.id = :id'
        );
        $statement->execute(['id' => $id]);
        $row = $statement->fetch();

        return $row === false ? null : $this->mapOrderRow($row);
    }

    protected function defaultOrderFormData(int $productId = 0, ?Product $selectedProduct = null): array
    {
        $price = $selectedProduct?->getPrice() ?? 0.0;

        return [
            'product_id' => $productId > 0 ? (string) $productId : '',
            'customer_name' => '',
            'customer_email' => '',
            'quantity' => '1',
            'status' => 'PENDING',
            'order_date' => date('Y-m-d'),
            'shipping_address' => '',
            'notes' => '',
            'unit_price' => $selectedProduct !== null ? (string) $price : '',
            'total_amount' => $selectedProduct !== null ? (string) $price : '',
        ];
    }

    protected function prepareOrderFormData(Order|array|null $source, int $selectedProductId = 0): array
    {
        if ($source instanceof Order) {
            return [
                'product_id' => (string) ($source->getProductId() ?? ($selectedProductId > 0 ? $selectedProductId : '')),
                'customer_name' => $source->getCustomerName(),
                'customer_email' => $source->getCustomerEmail(),
                'quantity' => (string) $source->getQuantity(),
                'status' => $source->getStatus(),
                'order_date' => $source->getOrderDate(),
                'shipping_address' => (string) ($source->getShippingAddress() ?? ''),
                'notes' => (string) ($source->getNotes() ?? ''),
                'unit_price' => (string) $source->getUnitPrice(),
                'total_amount' => (string) $source->getTotalAmount(),
            ];
        }

        if ($source === null) {
            return $this->defaultOrderFormData($selectedProductId);
        }

        return [
            'product_id' => $this->cleanValue($source['product_id'] ?? ($selectedProductId > 0 ? (string) $selectedProductId : '')),
            'customer_name' => $this->cleanValue($source['customer_name'] ?? ''),
            'customer_email' => $this->cleanValue($source['customer_email'] ?? ''),
            'quantity' => $this->cleanValue($source['quantity'] ?? '1'),
            'status' => $this->cleanValue($source['status'] ?? 'PENDING'),
            'order_date' => $this->cleanValue($source['order_date'] ?? date('Y-m-d')),
            'shipping_address' => $this->cleanValue($source['shipping_address'] ?? ''),
            'notes' => $this->cleanValue($source['notes'] ?? ''),
            'unit_price' => $this->cleanValue($source['unit_price'] ?? ''),
            'total_amount' => $this->cleanValue($source['total_amount'] ?? ''),
        ];
    }

    protected function prepareOrderInput(array $source, string $area, string $mode, ?Order $existingOrder = null): array
    {
        $data = [
            'product_id' => (int) ($source['product_id'] ?? 0),
            'product_name' => $this->cleanValue($source['product_name'] ?? ''),
            'customer_name' => $this->cleanValue($source['customer_name'] ?? ''),
            'customer_email' => $this->cleanValue($source['customer_email'] ?? ''),
            'quantity' => is_numeric($source['quantity'] ?? null) ? (int) $source['quantity'] : 0,
            'unit_price' => is_numeric($source['unit_price'] ?? null) ? (float) $source['unit_price'] : 0.0,
            'total_amount' => is_numeric($source['total_amount'] ?? null) ? (float) $source['total_amount'] : 0.0,
            'status' => $this->cleanValue($source['status'] ?? 'PENDING'),
            'order_date' => $this->cleanValue($source['order_date'] ?? ''),
            'shipping_address' => $this->nullableString($source['shipping_address'] ?? ''),
            'notes' => $this->nullableString($source['notes'] ?? ''),
        ];

        $product = $this->findProductById((int) ($data['product_id'] ?? 0));
        if ($product !== null) {
            $data['product_name'] = $product->getName();
            $data['unit_price'] = $product->getPrice();
            $data['total_amount'] = $data['unit_price'] * (int) ($data['quantity'] ?? 0);
        }

        if ($mode === 'create') {
            $data['order_date'] = date('Y-m-d');
            $data['status'] = $area === 'frontoffice'
                ? 'PENDING'
                : (($data['status'] ?? '') !== '' ? (string) $data['status'] : 'PENDING');
        } else {
            $data['order_date'] = (string) ($existingOrder?->getOrderDate() ?? date('Y-m-d'));
            $data['status'] = $area === 'frontoffice'
                ? (string) ($existingOrder?->getStatus() ?? 'PENDING')
                : (string) ($data['status'] ?? ($existingOrder?->getStatus() ?? 'PENDING'));
        }

        return $data;
    }

    protected function validateOrder(array $data): array
    {
        $errors = [];
        $customerName = trim((string) ($data['customer_name'] ?? ''));
        $email = trim((string) ($data['customer_email'] ?? ''));
        $shippingAddress = trim((string) ($data['shipping_address'] ?? ''));
        $notes = trim((string) ($data['notes'] ?? ''));

        $productId = $data['product_id'] ?? null;
        $product = null;
        if (!is_numeric($productId) || (int) $productId <= 0 || ($product = $this->findProductById((int) $productId)) === null) {
            $errors['product_id'] = 'Product is required.';
        }

        if ($product !== null) {
            if ($product->getStatus() !== 'ACTIVE') {
                $errors['product_id'] = 'Only active products can be ordered.';
            }

            if ($product->getStockQuantity() < 1) {
                $errors['quantity'] = 'This product is currently out of stock.';
            }

            $quantity = $data['quantity'] ?? null;
            if (is_numeric($quantity) && (int) $quantity > $product->getStockQuantity()) {
                $errors['quantity'] = 'Quantity exceeds available stock.';
            }
        }

        if ($customerName === '') {
            $errors['customer_name'] = 'Customer name is required.';
        } elseif (mb_strlen($customerName) < 2 || mb_strlen($customerName) > 150) {
            $errors['customer_name'] = 'Customer name must be between 2 and 150 characters.';
        }

        if ($email === '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $errors['customer_email'] = 'Customer email must be valid.';
        } elseif (mb_strlen($email) > 255) {
            $errors['customer_email'] = 'Customer email must be 255 characters or fewer.';
        }

        if (
            filter_var($data['quantity'] ?? null, FILTER_VALIDATE_INT) === false
            || (int) ($data['quantity'] ?? 0) < 1
        ) {
            $errors['quantity'] = 'Quantity must be a whole number of at least 1.';
        }

        if (!in_array((string) ($data['status'] ?? 'PENDING'), self::ORDER_STATUSES, true)) {
            $errors['status'] = 'Invalid status.';
        }

        $orderDate = (string) ($data['order_date'] ?? '');
        if (!$this->isDateString($orderDate)) {
            $errors['order_date'] = 'Order date must be valid.';
        }

        if ($shippingAddress === '') {
            $errors['shipping_address'] = 'Shipping address is required.';
        } elseif (mb_strlen($shippingAddress) > 2000) {
            $errors['shipping_address'] = 'Shipping address must be 2000 characters or fewer.';
        }

        if ($notes !== '' && mb_strlen($notes) > 2000) {
            $errors['notes'] = 'Notes must be 2000 characters or fewer.';
        }

        return $errors;
    }

    protected function orderFromValues(array $values, ?int $id = null): Order
    {
        return new Order(
            $id,
            (int) $values['product_id'],
            (string) $values['product_name'],
            (string) $values['customer_name'],
            (string) $values['customer_email'],
            (int) $values['quantity'],
            (float) $values['unit_price'],
            (float) $values['total_amount'],
            (string) $values['status'],
            (string) $values['order_date'],
            $this->nullableString($values['shipping_address'] ?? ''),
            $this->nullableString($values['notes'] ?? '')
        );
    }

    protected function createOrder(Order $order): int
    {
        $statement = $this->connection()->prepare(
            'INSERT INTO orders (product_id, product_name, customer_name, customer_email, quantity, unit_price, total_amount, status, order_date, shipping_address, notes)
            VALUES (:product_id, :product_name, :customer_name, :customer_email, :quantity, :unit_price, :total_amount, :status, :order_date, :shipping_address, :notes)'
        );
        $statement->execute($this->orderPayload($order));

        $id = (int) $this->connection()->lastInsertId();
        $order->setId($id);

        return $id;
    }

    protected function updateOrder(Order $order): void
    {
        $payload = $this->orderPayload($order);
        $payload['id'] = $order->getId();

        $statement = $this->connection()->prepare(
            'UPDATE orders
            SET product_id = :product_id,
                product_name = :product_name,
                customer_name = :customer_name,
                customer_email = :customer_email,
                quantity = :quantity,
                unit_price = :unit_price,
                total_amount = :total_amount,
                status = :status,
                order_date = :order_date,
                shipping_address = :shipping_address,
                notes = :notes
            WHERE id = :id'
        );
        $statement->execute($payload);
    }

    protected function deleteOrder(int $id): void
    {
        $statement = $this->connection()->prepare('DELETE FROM orders WHERE id = :id');
        $statement->execute(['id' => $id]);
    }

    protected function cleanValue(mixed $value): string
    {
        return trim((string) $value);
    }

    protected function nullableString(mixed $value): ?string
    {
        $value = $this->cleanValue($value);

        return $value === '' ? null : $value;
    }

    protected function isDateString(string $value): bool
    {
        $date = DateTimeImmutable::createFromFormat('Y-m-d', $value);

        return $date instanceof DateTimeImmutable && $date->format('Y-m-d') === $value;
    }

    /**
     * @param ProductCategory[] $categories
     * @return array<int, array<string, mixed>>
     */
    protected function categoriesToArrays(array $categories): array
    {
        return array_map([$this, 'categoryToArray'], $categories);
    }

    /**
     * @param Product[] $products
     * @return array<int, array<string, mixed>>
     */
    protected function productsToArrays(array $products): array
    {
        return array_map([$this, 'productToArray'], $products);
    }

    /**
     * @param Order[] $orders
     * @return array<int, array<string, mixed>>
     */
    protected function ordersToArrays(array $orders): array
    {
        return array_map([$this, 'orderToArray'], $orders);
    }

    /**
     * @return array<string, mixed>
     */
    protected function categoryToArray(?ProductCategory $category): ?array
    {
        if ($category === null) {
            return null;
        }

        return [
            'id' => $category->getId(),
            'name' => $category->getName(),
            'description' => $category->getDescription(),
            'status' => $category->getStatus(),
            'created_at' => $category->getCreatedAt(),
            'updated_at' => $category->getUpdatedAt(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function productToArray(?Product $product): ?array
    {
        if ($product === null) {
            return null;
        }

        return [
            'id' => $product->getId(),
            'product_category_id' => $product->getProductCategoryId(),
            'name' => $product->getName(),
            'sku' => $product->getSku(),
            'price' => $product->getPrice(),
            'stock_quantity' => $product->getStockQuantity(),
            'status' => $product->getStatus(),
            'description' => $product->getDescription(),
            'created_at' => $product->getCreatedAt(),
            'updated_at' => $product->getUpdatedAt(),
            'category_name' => $product->getCategoryName(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function orderToArray(?Order $order): ?array
    {
        if ($order === null) {
            return null;
        }

        return [
            'id' => $order->getId(),
            'product_id' => $order->getProductId(),
            'product_name' => $order->getProductName(),
            'customer_name' => $order->getCustomerName(),
            'customer_email' => $order->getCustomerEmail(),
            'quantity' => $order->getQuantity(),
            'unit_price' => $order->getUnitPrice(),
            'total_amount' => $order->getTotalAmount(),
            'status' => $order->getStatus(),
            'order_date' => $order->getOrderDate(),
            'shipping_address' => $order->getShippingAddress(),
            'notes' => $order->getNotes(),
            'created_at' => $order->getCreatedAt(),
            'updated_at' => $order->getUpdatedAt(),
            'current_product_name' => $order->getCurrentProductName(),
            'current_product_sku' => $order->getCurrentProductSku(),
            'current_product_stock' => $order->getCurrentProductStock(),
            'current_product_status' => $order->getCurrentProductStatus(),
            'current_category_name' => $order->getCurrentCategoryName(),
        ];
    }

    private function categoryPayload(ProductCategory $category): array
    {
        return [
            'name' => $category->getName(),
            'description' => $category->getDescription(),
            'status' => $category->getStatus(),
        ];
    }

    private function productPayload(Product $product): array
    {
        return [
            'product_category_id' => $product->getProductCategoryId(),
            'name' => $product->getName(),
            'sku' => $product->getSku(),
            'price' => $product->getPrice(),
            'stock_quantity' => $product->getStockQuantity(),
            'status' => $product->getStatus(),
            'description' => $product->getDescription(),
        ];
    }

    private function orderPayload(Order $order): array
    {
        return [
            'product_id' => $order->getProductId(),
            'product_name' => $order->getProductName(),
            'customer_name' => $order->getCustomerName(),
            'customer_email' => $order->getCustomerEmail(),
            'quantity' => $order->getQuantity(),
            'unit_price' => $order->getUnitPrice(),
            'total_amount' => $order->getTotalAmount(),
            'status' => $order->getStatus(),
            'order_date' => $order->getOrderDate(),
            'shipping_address' => $order->getShippingAddress(),
            'notes' => $order->getNotes(),
        ];
    }

    private function mapCategoryRow(array $row): ProductCategory
    {
        return new ProductCategory(
            (int) $row['id'],
            (string) $row['name'],
            $row['description'] !== null ? (string) $row['description'] : null,
            (string) $row['status'],
            $row['created_at'] !== null ? (string) $row['created_at'] : null,
            $row['updated_at'] !== null ? (string) $row['updated_at'] : null
        );
    }

    private function mapProductRow(array $row): Product
    {
        return new Product(
            (int) $row['id'],
            (int) $row['product_category_id'],
            (string) $row['name'],
            (string) $row['sku'],
            (float) $row['price'],
            (int) $row['stock_quantity'],
            (string) $row['status'],
            $row['description'] !== null ? (string) $row['description'] : null,
            $row['created_at'] !== null ? (string) $row['created_at'] : null,
            $row['updated_at'] !== null ? (string) $row['updated_at'] : null,
            $row['category_name'] !== null ? (string) $row['category_name'] : null
        );
    }

    private function mapOrderRow(array $row): Order
    {
        return new Order(
            (int) $row['id'],
            (int) $row['product_id'],
            (string) $row['product_name'],
            (string) $row['customer_name'],
            (string) $row['customer_email'],
            (int) $row['quantity'],
            (float) $row['unit_price'],
            (float) $row['total_amount'],
            (string) $row['status'],
            (string) $row['order_date'],
            $row['shipping_address'] !== null ? (string) $row['shipping_address'] : null,
            $row['notes'] !== null ? (string) $row['notes'] : null,
            $row['created_at'] !== null ? (string) $row['created_at'] : null,
            $row['updated_at'] !== null ? (string) $row['updated_at'] : null,
            $row['current_product_name'] !== null ? (string) $row['current_product_name'] : null,
            $row['current_product_sku'] !== null ? (string) $row['current_product_sku'] : null,
            $row['current_product_stock'] !== null ? (int) $row['current_product_stock'] : null,
            $row['current_product_status'] !== null ? (string) $row['current_product_status'] : null,
            $row['current_category_name'] !== null ? (string) $row['current_category_name'] : null
        );
    }
}
