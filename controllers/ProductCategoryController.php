<?php
declare(strict_types=1);

require_once ROOT_PATH . '/controllers/BaseController.php';

class ProductCategoryController extends BaseController
{
    public function index(string $area): void
    {
        $filters = $this->categoryFilters($_GET);
        $this->render('categories/index', [
            'pageTitle' => 'Product Categories',
            'area' => $area,
            'currentSection' => 'categories',
            'categories' => $this->allCategories($filters),
            'categoryFilters' => $filters,
            'categoryStats' => $this->categoryStatistics($filters),
        ], $area);
    }

    public function exportPdf(string $area): void
    {
        $filters = $this->categoryFilters($_GET);
        $categories = $this->allCategories($filters);
        $lines = ['Product Categories Export', ''];
        $lines[] = 'Area: ' . $area;
        $lines[] = 'Records: ' . count($categories);
        $lines[] = 'Filters: search=' . ($filters['search'] !== '' ? $filters['search'] : 'all')
            . ', status=' . ($filters['status'] !== '' ? $filters['status'] : 'all')
            . ', sort=' . $filters['sort'];
        $lines[] = '';

        foreach ($categories as $category) {
            $lines[] = sprintf(
                '%s | %s | %s',
                (string) $category['name'],
                (string) $category['status'],
                (string) ($category['description'] ?? '')
            );
        }

        $this->downloadSimplePdf('product-categories.pdf', $lines);
    }

    public function show(string $area): void
    {
        $category = $this->findCategory((int) ($_GET['id'] ?? 0));
        if ($category === null) {
            $this->renderNotFound();
            return;
        }

        $productFilters = $this->categoryProductFilters($_GET);
        $this->render('categories/show', [
            'pageTitle' => 'Category',
            'area' => $area,
            'currentSection' => 'categories',
            'category' => $category,
            'products' => $this->productsForCategory((int) $category['id'], $productFilters),
            'productFilters' => $productFilters,
        ], $area);
    }

    public function exportCategoryProductsPdf(string $area): void
    {
        $category = $this->findCategory((int) ($_GET['id'] ?? 0));
        if ($category === null) {
            $this->renderNotFound();
            return;
        }

        $filters = $this->categoryProductFilters($_GET);
        $products = $this->productsForCategory((int) $category['id'], $filters);
        $lines = ['Category Products Export', ''];
        $lines[] = 'Area: ' . $area;
        $lines[] = 'Category: ' . (string) $category['name'];
        $lines[] = 'Records: ' . count($products);
        $lines[] = 'Filters: search=' . ($filters['search'] !== '' ? $filters['search'] : 'all')
            . ', status=' . ($filters['status'] !== '' ? $filters['status'] : 'all')
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

        $this->downloadSimplePdf('category-products.pdf', $lines);
    }

    public function form(string $area, string $mode): void
    {
        $category = $mode === 'edit' ? $this->findCategory((int) ($_GET['id'] ?? 0)) : null;
        if ($mode === 'edit' && $category === null) {
            $this->renderNotFound();
            return;
        }

        $this->render('categories/form', [
            'pageTitle' => $mode === 'edit' ? 'Edit Category' : 'New Category',
            'area' => $area,
            'currentSection' => 'categories',
            'mode' => $mode,
            'category' => $category,
            'errors' => [],
            'values' => $category ?? [
                'name' => '',
                'description' => '',
                'status' => 'ACTIVE',
            ],
        ], $area);
    }

    public function create(string $area): void
    {
        $data = $this->normalizeCategory($_POST);
        $errors = $this->validateCategory($data);
        if ($errors !== []) {
            $this->render('categories/form', [
                'pageTitle' => 'New Category',
                'area' => $area,
                'currentSection' => 'categories',
                'mode' => 'create',
                'errors' => $errors,
                'values' => array_merge($_POST, $data),
            ], $area);
            return;
        }

        $id = $this->createCategory($data);
        $this->redirect($area . '/categories/show', ['id' => $id]);
    }

    public function update(string $area): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $category = $this->findCategory($id);
        if ($category === null) {
            $this->renderNotFound();
            return;
        }

        $data = $this->normalizeCategory($_POST);
        $errors = $this->validateCategory($data);
        if ($errors !== []) {
            $this->render('categories/form', [
                'pageTitle' => 'Edit Category',
                'area' => $area,
                'currentSection' => 'categories',
                'mode' => 'edit',
                'category' => $category,
                'errors' => $errors,
                'values' => array_merge($category, $_POST, $data),
            ], $area);
            return;
        }

        $this->updateCategory($id, $data);
        $this->redirect($area . '/categories/show', ['id' => $id]);
    }

    public function delete(string $area): void
    {
        $this->deleteCategory((int) ($_GET['id'] ?? 0));
        $this->redirect($area . '/categories');
    }

    private function allCategories(array $filters = []): array
    {
        $query = 'SELECT * FROM product_categories WHERE 1=1';
        $params = [];

        if (($filters['search'] ?? '') !== '') {
            $searchTerm = '%' . $filters['search'] . '%';
            $query .= ' AND (name LIKE :search_name OR COALESCE(description, "") LIKE :search_description)';
            $params['search_name'] = $searchTerm;
            $params['search_description'] = $searchTerm;
        }

        if (($filters['status'] ?? '') !== '') {
            $query .= ' AND status = :status';
            $params['status'] = $filters['status'];
        }

        $sort = $filters['sort'] ?? 'created_desc';
        $query .= ' ORDER BY ' . match ($sort) {
            'name_asc' => 'name ASC, id DESC',
            'name_desc' => 'name DESC, id DESC',
            'status_asc' => 'status ASC, name ASC',
            default => 'id DESC',
        };
        $stmt = $this->connection()->prepare($query);
        $this->executeNamed($stmt, $params);

        return $stmt->fetchAll();
    }

    private function findCategory(int $id): ?array
    {
        $stmt = $this->connection()->prepare('SELECT * FROM product_categories WHERE id = :id');
        $this->executeNamed($stmt, ['id' => $id]);
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    private function createCategory(array $data): int
    {
        $stmt = $this->connection()->prepare(
            'INSERT INTO product_categories (name, description, status) VALUES (:name, :description, :status)'
        );
        $this->executeNamed($stmt, $data);

        return (int) $this->connection()->lastInsertId();
    }

    private function updateCategory(int $id, array $data): bool
    {
        $stmt = $this->connection()->prepare(
            'UPDATE product_categories SET name = :name, description = :description, status = :status WHERE id = :id'
        );

        return $this->executeNamed($stmt, ['id' => $id] + $data);
    }

    private function deleteCategory(int $id): bool
    {
        $stmt = $this->connection()->prepare('DELETE FROM product_categories WHERE id = :id');

        return $this->executeNamed($stmt, ['id' => $id]);
    }

    private function productsForCategory(int $categoryId, array $filters = []): array
    {
        $query = 'SELECT p.*, c.name AS category_name FROM products p '
            . 'INNER JOIN product_categories c ON c.id = p.product_category_id '
            . 'WHERE p.product_category_id = :product_category_id';
        $params = ['product_category_id' => $categoryId];

        if (($filters['search'] ?? '') !== '') {
            $searchTerm = '%' . $filters['search'] . '%';
            $query .= ' AND (p.name LIKE :search_name OR p.sku LIKE :search_sku OR COALESCE(p.description, "") LIKE :search_description)';
            $params['search_name'] = $searchTerm;
            $params['search_sku'] = $searchTerm;
            $params['search_description'] = $searchTerm;
        }

        if (($filters['status'] ?? '') !== '') {
            $query .= ' AND p.status = :status';
            $params['status'] = $filters['status'];
        }

        $sort = $filters['sort'] ?? 'created_desc';
        $query .= ' ORDER BY ' . match ($sort) {
            'name_asc' => 'p.name ASC, p.id DESC',
            'name_desc' => 'p.name DESC, p.id DESC',
            'price_asc' => 'p.price ASC, p.id DESC',
            'price_desc' => 'p.price DESC, p.id DESC',
            'stock_desc' => 'p.stock_quantity DESC, p.id DESC',
            'stock_asc' => 'p.stock_quantity ASC, p.id DESC',
            default => 'p.id DESC',
        };
        $stmt = $this->connection()->prepare($query);
        $this->executeNamed($stmt, $params);

        return $stmt->fetchAll();
    }

    private function categoryFilters(array $source): array
    {
        return [
            'search' => trim((string) ($source['search'] ?? '')),
            'status' => trim((string) ($source['status'] ?? '')),
            'sort' => trim((string) ($source['sort'] ?? 'created_desc')),
        ];
    }

    private function categoryProductFilters(array $source): array
    {
        return [
            'search' => trim((string) ($source['product_search'] ?? '')),
            'status' => trim((string) ($source['product_status'] ?? '')),
            'sort' => trim((string) ($source['product_sort'] ?? 'created_desc')),
        ];
    }

    private function categoryStatistics(array $filters = []): array
    {
        $query = 'SELECT COUNT(*) AS total_categories,
                         SUM(CASE WHEN status = "ACTIVE" THEN 1 ELSE 0 END) AS active_categories,
                         SUM(CASE WHEN status = "INACTIVE" THEN 1 ELSE 0 END) AS inactive_categories
                  FROM product_categories
                  WHERE 1=1';
        $params = [];

        if (($filters['search'] ?? '') !== '') {
            $searchTerm = '%' . $filters['search'] . '%';
            $query .= ' AND (name LIKE :search_name OR COALESCE(description, "") LIKE :search_description)';
            $params['search_name'] = $searchTerm;
            $params['search_description'] = $searchTerm;
        }

        if (($filters['status'] ?? '') !== '') {
            $query .= ' AND status = :status';
            $params['status'] = $filters['status'];
        }

        $stmt = $this->connection()->prepare($query);
        $this->executeNamed($stmt, $params);
        $stats = $stmt->fetch() ?: [];

        return [
            'total_categories' => (int) ($stats['total_categories'] ?? 0),
            'active_categories' => (int) ($stats['active_categories'] ?? 0),
            'inactive_categories' => (int) ($stats['inactive_categories'] ?? 0),
        ];
    }

    private function validateCategory(array $data): array
    {
        $errors = [];
        $name = trim((string) ($data['name'] ?? ''));
        $description = trim((string) ($data['description'] ?? ''));

        if ($name === '') {
            $errors['name'] = 'Name is required.';
        } elseif (strlen($name) < 2 || strlen($name) > 150) {
            $errors['name'] = 'Name must be between 2 and 150 characters.';
        }

        if (!in_array((string) ($data['status'] ?? 'ACTIVE'), ['ACTIVE', 'INACTIVE'], true)) {
            $errors['status'] = 'Invalid status.';
        }

        if ($description !== '' && strlen($description) > 2000) {
            $errors['description'] = 'Description must be 2000 characters or fewer.';
        }

        return $errors;
    }

    private function normalizeCategory(array $data): array
    {
        $description = trim((string) ($data['description'] ?? ''));

        return [
            'name' => trim((string) ($data['name'] ?? '')),
            'description' => $description === '' ? null : $description,
            'status' => (string) ($data['status'] ?? 'ACTIVE'),
        ];
    }
}
