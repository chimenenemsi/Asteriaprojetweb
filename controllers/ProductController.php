<?php
declare(strict_types=1);

require_once ROOT_PATH . '/controllers/BaseController.php';
require_once ROOT_PATH . '/controllers/ProduitDataTrait.php';

class ProductController extends BaseController
{
    use ProduitDataTrait;

    public function index(string $area): void
    {
        $this->render('products/index', [
            'pageTitle' => 'Products',
            'area' => $area,
            'currentSection' => 'products',
            'products' => $this->productsToArrays($this->fetchAllProducts()),
        ], $area);
    }

    public function form(string $area, string $mode): void
    {
        $product = $mode === 'edit' ? $this->findProductById((int) ($_GET['id'] ?? 0)) : null;
        if ($mode === 'edit' && $product === null) {
            $this->renderNotFound();
            return;
        }

        $categoryId = (int) ($_GET['category_id'] ?? ($product?->getProductCategoryId() ?? 0));

        $this->render('products/form', [
            'pageTitle' => $mode === 'edit' ? 'Edit Product' : 'New Product',
            'area' => $area,
            'currentSection' => 'products',
            'mode' => $mode,
            'product' => $this->productToArray($product),
            'categories' => $this->categoriesToArrays($this->fetchAllCategories()),
            'errors' => [],
            'values' => $this->prepareProductFormData($product, $categoryId),
        ], $area);
    }

    public function create(string $area): void
    {
        $validation = $this->validateProduct($_POST);
        if ($validation['errors'] !== []) {
            $this->render('products/form', [
                'pageTitle' => 'New Product',
                'area' => $area,
                'currentSection' => 'products',
                'mode' => 'create',
                'categories' => $this->categoriesToArrays($this->fetchAllCategories()),
                'errors' => $validation['errors'],
                'values' => $validation['values'],
            ], $area);
            return;
        }

        $product = $this->productFromValues($validation['values']);
        $this->createProduct($product);
        $this->redirect($area . '/categories/show', ['id' => (int) $validation['values']['product_category_id']]);
    }

    public function update(string $area): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $product = $this->findProductById($id);
        if ($product === null) {
            $this->renderNotFound();
            return;
        }

        $validation = $this->validateProduct($_POST);
        if ($validation['errors'] !== []) {
            $this->render('products/form', [
                'pageTitle' => 'Edit Product',
                'area' => $area,
                'currentSection' => 'products',
                'mode' => 'edit',
                'product' => $this->productToArray($product),
                'categories' => $this->categoriesToArrays($this->fetchAllCategories()),
                'errors' => $validation['errors'],
                'values' => $validation['values'],
            ], $area);
            return;
        }

        $updatedProduct = $this->productFromValues($validation['values'], $id);
        $this->updateProduct($updatedProduct);
        $this->redirect($area . '/categories/show', ['id' => (int) $validation['values']['product_category_id']]);
    }

    public function delete(string $area): void
    {
        $product = $this->findProductById((int) ($_GET['id'] ?? 0));
        if ($product !== null) {
            $this->deleteProduct((int) ($product->getId() ?? 0));
            $this->redirect($area . '/categories/show', ['id' => (int) ($product->getProductCategoryId() ?? 0)]);
        }

        $this->redirect($area . '/products');
    }
}
