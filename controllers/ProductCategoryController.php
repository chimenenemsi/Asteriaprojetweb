<?php
declare(strict_types=1);

require_once ROOT_PATH . '/controllers/BaseController.php';
require_once ROOT_PATH . '/controllers/ProduitDataTrait.php';

class ProductCategoryController extends BaseController
{
    use ProduitDataTrait;

    public function index(string $area): void
    {
        $this->render('categories/index', [
            'pageTitle' => 'Product Categories',
            'area' => $area,
            'currentSection' => 'categories',
            'categories' => $this->categoriesToArrays($this->fetchAllCategories()),
        ], $area);
    }

    public function show(string $area): void
    {
        $category = $this->findCategoryById((int) ($_GET['id'] ?? 0));
        if ($category === null) {
            $this->renderNotFound();
            return;
        }

        $this->render('categories/show', [
            'pageTitle' => 'Category',
            'area' => $area,
            'currentSection' => 'categories',
            'category' => $this->categoryToArray($category),
            'products' => $this->productsToArrays($this->fetchProductsByCategory((int) ($category->getId() ?? 0))),
        ], $area);
    }

    public function form(string $area, string $mode): void
    {
        $category = $mode === 'edit' ? $this->findCategoryById((int) ($_GET['id'] ?? 0)) : null;
        if ($mode === 'edit' && $category === null) {
            $this->renderNotFound();
            return;
        }

        $this->render('categories/form', [
            'pageTitle' => $mode === 'edit' ? 'Edit Category' : 'New Category',
            'area' => $area,
            'currentSection' => 'categories',
            'mode' => $mode,
            'category' => $this->categoryToArray($category),
            'errors' => [],
            'values' => $this->prepareCategoryFormData($category),
        ], $area);
    }

    public function create(string $area): void
    {
        $validation = $this->validateCategory($_POST);
        if ($validation['errors'] !== []) {
            $this->render('categories/form', [
                'pageTitle' => 'New Category',
                'area' => $area,
                'currentSection' => 'categories',
                'mode' => 'create',
                'errors' => $validation['errors'],
                'values' => $validation['values'],
            ], $area);
            return;
        }

        $category = $this->categoryFromValues($validation['values']);
        $id = $this->createCategory($category);
        $this->redirect($area . '/categories/show', ['id' => $id]);
    }

    public function update(string $area): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $category = $this->findCategoryById($id);
        if ($category === null) {
            $this->renderNotFound();
            return;
        }

        $validation = $this->validateCategory($_POST);
        if ($validation['errors'] !== []) {
            $this->render('categories/form', [
                'pageTitle' => 'Edit Category',
                'area' => $area,
                'currentSection' => 'categories',
                'mode' => 'edit',
                'category' => $this->categoryToArray($category),
                'errors' => $validation['errors'],
                'values' => $validation['values'],
            ], $area);
            return;
        }

        $updatedCategory = $this->categoryFromValues($validation['values'], $id);
        $this->updateCategory($updatedCategory);
        $this->redirect($area . '/categories/show', ['id' => $id]);
    }

    public function delete(string $area): void
    {
        $this->deleteCategory((int) ($_GET['id'] ?? 0));
        $this->redirect($area . '/categories');
    }
}
