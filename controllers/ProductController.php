<?php
declare(strict_types=1);

require_once ROOT_PATH . '/controllers/BaseController.php';

class ProductController extends BaseController
{
    public function index(string $area): void
    {
        $filters = $this->productFilters($_GET);
        $this->render('products/index', [
            'pageTitle' => 'Products',
            'area' => $area,
            'currentSection' => 'products',
            'products' => $this->allProducts($filters),
            'productStats' => $this->productStatistics($filters),
            'topProducts' => $this->topSellingProducts($filters),
            'productFilters' => $filters,
            'categories' => $this->allCategories(),
        ], $area);
    }

    public function exportPdf(string $area): void
    {
        $filters = $this->productFilters($_GET);
        $products = $this->allProducts($filters);
        $lines = ['Products Export', ''];
        $lines[] = 'Area: ' . $area;
        $lines[] = 'Records: ' . count($products);
        $lines[] = 'Filters: search=' . ($filters['search'] !== '' ? $filters['search'] : 'all')
            . ', category=' . ($filters['category_id'] !== '' ? $filters['category_id'] : 'all')
            . ', status=' . ($filters['status'] !== '' ? $filters['status'] : 'all')
            . ', stock=' . ($filters['stock'] !== '' ? $filters['stock'] : 'all')
            . ', sort=' . $filters['sort'];
        $lines[] = '';

        foreach ($products as $product) {
            $lines[] = sprintf(
                '%s | %s | $%0.2f | stock %d | %s',
                (string) $product['name'],
                (string) $product['sku'],
                (float) $product['price'],
                (int) $product['stock_quantity'],
                (string) $product['status']
            );
        }

        $this->downloadSimplePdf('products.pdf', $lines);
    }

    public function form(string $area, string $mode): void
    {
        $product = $mode === 'edit' ? $this->findProduct((int) ($_GET['id'] ?? 0)) : null;
        if ($mode === 'edit' && $product === null) {
            $this->renderNotFound();
            return;
        }

        $categoryId = (int) ($_GET['category_id'] ?? ($product['product_category_id'] ?? 0));

        $this->render('products/form', [
            'pageTitle' => $mode === 'edit' ? 'Edit Product' : 'New Product',
            'area' => $area,
            'currentSection' => 'products',
            'mode' => $mode,
            'product' => $product,
            'categories' => $this->allCategories(),
            'errors' => [],
            'values' => $product ?? [
                'product_category_id' => $categoryId > 0 ? $categoryId : '',
                'name' => '',
                'sku' => '',
                'price' => '',
                'stock_quantity' => 0,
                'status' => 'ACTIVE',
                'description' => '',
            ],
        ], $area);
    }

    public function create(string $area): void
    {
        $data = $this->normalizeProduct($_POST);
        $errors = $this->validateProduct($data);
        if ($errors !== []) {
            $this->render('products/form', [
                'pageTitle' => 'New Product',
                'area' => $area,
                'currentSection' => 'products',
                'mode' => 'create',
                'categories' => $this->allCategories(),
                'errors' => $errors,
                'values' => array_merge($_POST, $data),
            ], $area);
            return;
        }

        $this->createProduct($data);
        $this->redirect($area . '/categories/show', ['id' => (int) $data['product_category_id']]);
    }

    public function update(string $area): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $product = $this->findProduct($id);
        if ($product === null) {
            $this->renderNotFound();
            return;
        }

        $data = $this->normalizeProduct($_POST);
        $errors = $this->validateProduct($data);
        if ($errors !== []) {
            $this->render('products/form', [
                'pageTitle' => 'Edit Product',
                'area' => $area,
                'currentSection' => 'products',
                'mode' => 'edit',
                'product' => $product,
                'categories' => $this->allCategories(),
                'errors' => $errors,
                'values' => array_merge($product, $_POST, $data),
            ], $area);
            return;
        }

        $this->updateProduct($id, $data);
        $this->redirect($area . '/categories/show', ['id' => (int) $data['product_category_id']]);
    }

    public function delete(string $area): void
    {
        $product = $this->findProduct((int) ($_GET['id'] ?? 0));
        if ($product !== null) {
            $this->deleteProduct((int) $product['id']);
            $this->redirect($area . '/categories/show', ['id' => (int) $product['product_category_id']]);
        }

        $this->redirect($area . '/products');
    }

    private function allProducts(array $filters = []): array
    {
        $query = 'SELECT p.*, c.name AS category_name FROM products p '
            . 'INNER JOIN product_categories c ON c.id = p.product_category_id '
            . 'WHERE 1=1';
        $params = [];

        if (($filters['search'] ?? '') !== '') {
            $searchTerm = '%' . $filters['search'] . '%';
            $query .= ' AND (p.name LIKE :search_name OR p.sku LIKE :search_sku OR COALESCE(p.description, "") LIKE :search_description)';
            $params['search_name'] = $searchTerm;
            $params['search_sku'] = $searchTerm;
            $params['search_description'] = $searchTerm;
        }

        if (($filters['category_id'] ?? '') !== '') {
            $query .= ' AND p.product_category_id = :category_id';
            $params['category_id'] = (int) $filters['category_id'];
        }

        if (($filters['status'] ?? '') !== '') {
            $query .= ' AND p.status = :status';
            $params['status'] = $filters['status'];
        }

        if (($filters['stock'] ?? '') === 'in_stock') {
            $query .= ' AND p.stock_quantity > 0';
        } elseif (($filters['stock'] ?? '') === 'low_stock') {
            $query .= ' AND p.stock_quantity BETWEEN 1 AND 5';
        } elseif (($filters['stock'] ?? '') === 'out_of_stock') {
            $query .= ' AND p.stock_quantity <= 0';
        }

        $sort = $filters['sort'] ?? 'created_desc';
        $query .= ' ORDER BY ' . match ($sort) {
            'name_asc' => 'p.name ASC, p.id DESC',
            'name_desc' => 'p.name DESC, p.id DESC',
            'price_asc' => 'p.price ASC, p.id DESC',
            'price_desc' => 'p.price DESC, p.id DESC',
            'stock_asc' => 'p.stock_quantity ASC, p.id DESC',
            'stock_desc' => 'p.stock_quantity DESC, p.id DESC',
            default => 'p.id DESC',
        };
        $stmt = $this->connection()->prepare($query);
        $this->executeNamed($stmt, $params);

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

    private function createProduct(array $data): int
    {
        $stmt = $this->connection()->prepare(
            'INSERT INTO products (product_category_id, name, sku, price, stock_quantity, status, description) '
            . 'VALUES (:product_category_id, :name, :sku, :price, :stock_quantity, :status, :description)'
        );
        $this->executeNamed($stmt, $data);

        return (int) $this->connection()->lastInsertId();
    }

    private function updateProduct(int $id, array $data): bool
    {
        $stmt = $this->connection()->prepare(
            'UPDATE products SET product_category_id = :product_category_id, name = :name, sku = :sku, '
            . 'price = :price, stock_quantity = :stock_quantity, status = :status, description = :description '
            . 'WHERE id = :id'
        );

        return $this->executeNamed($stmt, ['id' => $id] + $data);
    }

    private function deleteProduct(int $id): bool
    {
        $stmt = $this->connection()->prepare('DELETE FROM products WHERE id = :id');

        return $this->executeNamed($stmt, ['id' => $id]);
    }

    private function allCategories(): array
    {
        $stmt = $this->connection()->query('SELECT * FROM product_categories ORDER BY id DESC');

        return $stmt->fetchAll();
    }

    private function findCategory(int $id): ?array
    {
        $stmt = $this->connection()->prepare('SELECT * FROM product_categories WHERE id = :id');
        $this->executeNamed($stmt, ['id' => $id]);
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    private function validateProduct(array $data): array
    {
        $errors = [];
        $name = trim((string) ($data['name'] ?? ''));
        $sku = trim((string) ($data['sku'] ?? ''));
        $description = trim((string) ($data['description'] ?? ''));

        $categoryId = $data['product_category_id'] ?? null;
        if (!is_numeric($categoryId) || (int) $categoryId <= 0 || $this->findCategory((int) $categoryId) === null) {
            $errors['product_category_id'] = 'Category is required.';
        }

        if ($name === '') {
            $errors['name'] = 'Name is required.';
        } elseif (strlen($name) < 2 || strlen($name) > 150) {
            $errors['name'] = 'Name must be between 2 and 150 characters.';
        }

        if ($sku === '') {
            $errors['sku'] = 'SKU is required.';
        } elseif (!preg_match('/^[A-Z0-9][A-Z0-9_-]{2,79}$/', $sku)) {
            $errors['sku'] = 'SKU must be 3 to 80 characters using letters, numbers, dashes, or underscores.';
        }

        if (!is_numeric($data['price'] ?? null) || (float) ($data['price'] ?? 0) <= 0) {
            $errors['price'] = 'Price must be greater than 0.';
        }

        if (
            filter_var($data['stock_quantity'] ?? null, FILTER_VALIDATE_INT) === false
            || (int) ($data['stock_quantity'] ?? 0) < 0
        ) {
            $errors['stock_quantity'] = 'Stock quantity must be a whole number of 0 or more.';
        }

        if (!in_array((string) ($data['status'] ?? 'ACTIVE'), ['ACTIVE', 'DRAFT', 'OUT_OF_STOCK'], true)) {
            $errors['status'] = 'Invalid status.';
        }

        if ($description !== '' && strlen($description) > 4000) {
            $errors['description'] = 'Description must be 4000 characters or fewer.';
        }

        return $errors;
    }

    private function normalizeProduct(array $data): array
    {
        $description = trim((string) ($data['description'] ?? ''));

        return [
            'product_category_id' => (int) ($data['product_category_id'] ?? 0),
            'name' => trim((string) ($data['name'] ?? '')),
            'sku' => strtoupper(trim((string) ($data['sku'] ?? ''))),
            'price' => is_numeric($data['price'] ?? null) ? (float) $data['price'] : 0.0,
            'stock_quantity' => is_numeric($data['stock_quantity'] ?? null) ? (int) $data['stock_quantity'] : 0,
            'status' => (string) ($data['status'] ?? 'ACTIVE'),
            'description' => $description === '' ? null : $description,
        ];
    }

    private function productStatistics(array $filters = []): array
    {
        [$whereSql, $params] = $this->productFilterSql($filters);
        $statement = $this->connection()->prepare(
            'SELECT COUNT(*) AS total_products,
                    SUM(CASE WHEN status = "ACTIVE" THEN 1 ELSE 0 END) AS active_products,
                    SUM(CASE WHEN stock_quantity <= 5 THEN 1 ELSE 0 END) AS low_stock_products,
                    SUM(CASE WHEN stock_quantity <= 0 THEN 1 ELSE 0 END) AS out_of_stock_products,
                    COALESCE(SUM(price * stock_quantity), 0) AS inventory_value,
                    COALESCE(AVG(price), 0) AS average_price
             FROM products
             WHERE 1=1' . $whereSql
        );
        $this->executeNamed($statement, $params);
        $stats = $statement->fetch() ?: [];

        [$salesWhereSql, $salesParams] = $this->productFilterSql($filters, 'p');
        $salesStatement = $this->connection()->prepare(
            'SELECT COUNT(DISTINCT o.id) AS total_orders,
                    COALESCE(SUM(o.quantity), 0) AS total_units_sold,
                    COALESCE(SUM(o.total_amount), 0) AS total_revenue,
                    COALESCE(AVG(o.total_amount), 0) AS average_order_value
             FROM products p
             LEFT JOIN orders o ON o.product_id = p.id AND o.status <> "CANCELLED"
             WHERE 1=1' . $salesWhereSql
        );
        $this->executeNamed($salesStatement, $salesParams);
        $salesStats = $salesStatement->fetch() ?: [];

        return [
            'total_products' => (int) ($stats['total_products'] ?? 0),
            'active_products' => (int) ($stats['active_products'] ?? 0),
            'low_stock_products' => (int) ($stats['low_stock_products'] ?? 0),
            'out_of_stock_products' => (int) ($stats['out_of_stock_products'] ?? 0),
            'inventory_value' => (float) ($stats['inventory_value'] ?? 0),
            'average_price' => (float) ($stats['average_price'] ?? 0),
            'total_orders' => (int) ($salesStats['total_orders'] ?? 0),
            'total_units_sold' => (int) ($salesStats['total_units_sold'] ?? 0),
            'total_revenue' => (float) ($salesStats['total_revenue'] ?? 0),
            'average_order_value' => (float) ($salesStats['average_order_value'] ?? 0),
        ];
    }

    private function topSellingProducts(array $filters = []): array
    {
        [$whereSql, $params] = $this->productFilterSql($filters, 'p');
        $statement = $this->connection()->prepare(
            'SELECT p.id, p.name, p.sku, p.status, p.stock_quantity,
                    COALESCE(SUM(o.quantity), 0) AS total_units_sold,
                    COALESCE(SUM(o.total_amount), 0) AS total_revenue
             FROM products p
             LEFT JOIN orders o ON o.product_id = p.id AND o.status <> "CANCELLED"
             WHERE 1=1' . $whereSql . '
             GROUP BY p.id, p.name, p.sku, p.status, p.stock_quantity
             ORDER BY total_units_sold DESC, total_revenue DESC, p.id DESC
             LIMIT 5'
        );
        $this->executeNamed($statement, $params);

        return $statement->fetchAll();
    }

    private function productFilters(array $source): array
    {
        return [
            'search' => trim((string) ($source['search'] ?? '')),
            'category_id' => trim((string) ($source['category_id'] ?? '')),
            'status' => trim((string) ($source['status'] ?? '')),
            'stock' => trim((string) ($source['stock'] ?? '')),
            'sort' => trim((string) ($source['sort'] ?? 'created_desc')),
        ];
    }

    private function productFilterSql(array $filters, string $alias = ''): array
    {
        $prefix = $alias !== '' ? $alias . '.' : '';
        $clauses = [];
        $params = [];

        if (($filters['search'] ?? '') !== '') {
            $searchTerm = '%' . $filters['search'] . '%';
            $clauses[] = '(' . $prefix . 'name LIKE :search_name OR ' . $prefix . 'sku LIKE :search_sku OR COALESCE(' . $prefix . 'description, "") LIKE :search_description)';
            $params['search_name'] = $searchTerm;
            $params['search_sku'] = $searchTerm;
            $params['search_description'] = $searchTerm;
        }

        if (($filters['category_id'] ?? '') !== '') {
            $clauses[] = $prefix . 'product_category_id = :category_id';
            $params['category_id'] = (int) $filters['category_id'];
        }

        if (($filters['status'] ?? '') !== '') {
            $clauses[] = $prefix . 'status = :status';
            $params['status'] = $filters['status'];
        }

        if (($filters['stock'] ?? '') === 'in_stock') {
            $clauses[] = $prefix . 'stock_quantity > 0';
        } elseif (($filters['stock'] ?? '') === 'low_stock') {
            $clauses[] = $prefix . 'stock_quantity BETWEEN 1 AND 5';
        } elseif (($filters['stock'] ?? '') === 'out_of_stock') {
            $clauses[] = $prefix . 'stock_quantity <= 0';
        }

        return [
            $clauses === [] ? '' : ' AND ' . implode(' AND ', $clauses),
            $params,
        ];
    }
}
