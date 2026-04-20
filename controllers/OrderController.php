<?php
declare(strict_types=1);

require_once ROOT_PATH . '/controllers/BaseController.php';
require_once ROOT_PATH . '/controllers/ProduitDataTrait.php';

class OrderController extends BaseController
{
    use ProduitDataTrait;

    public function index(string $area): void
    {
        $this->render('orders/index', [
            'pageTitle' => 'Orders',
            'area' => $area,
            'currentSection' => 'orders',
            'orders' => $this->ordersToArrays($this->fetchAllOrders()),
        ], $area);
    }

    public function show(string $area): void
    {
        $order = $this->findOrderById((int) ($_GET['id'] ?? 0));
        if ($order === null) {
            $this->renderNotFound();
            return;
        }

        $this->render('orders/show', [
            'pageTitle' => 'Order',
            'area' => $area,
            'currentSection' => 'orders',
            'order' => $this->orderToArray($order),
        ], $area);
    }

    public function form(string $area, string $mode): void
    {
        $order = $mode === 'edit' ? $this->findOrderById((int) ($_GET['id'] ?? 0)) : null;
        if ($mode === 'edit' && $order === null) {
            $this->renderNotFound();
            return;
        }

        $productId = (int) ($_GET['product_id'] ?? ($order?->getProductId() ?? 0));
        $products = $this->fetchAllProducts();
        $selectedProduct = $productId > 0 ? $this->findProductById($productId) : null;
        $values = $order !== null
            ? $this->prepareOrderFormData($order, $productId)
            : $this->defaultOrderFormData($productId, $selectedProduct);

        $this->render('orders/form', [
            'pageTitle' => $mode === 'edit' ? 'Edit Order' : ($area === 'frontoffice' ? 'Place Order' : 'New Order'),
            'area' => $area,
            'currentSection' => 'orders',
            'mode' => $mode,
            'order' => $this->orderToArray($order),
            'products' => $this->productsToArrays($products),
            'selectedProduct' => $this->productToArray($selectedProduct),
            'errors' => [],
            'statusOptions' => order_backoffice_status_options(),
            'values' => $values,
        ], $area);
    }

    public function create(string $area): void
    {
        $data = $this->prepareOrderInput($_POST, $area, 'create');
        $errors = $this->validateOrder($data);
        if ($errors !== []) {
            if ($area === 'frontoffice' && (string) ($_POST['inline_order'] ?? '') === '1') {
                $this->render('products/index', [
                    'pageTitle' => 'Products',
                    'area' => $area,
                    'currentSection' => 'products',
                    'products' => $this->productsToArrays($this->fetchAllProducts()),
                    'orderErrors' => $errors,
                    'orderValues' => $this->prepareOrderFormData($data, (int) ($data['product_id'] ?? 0)),
                    'orderProductId' => (int) ($data['product_id'] ?? 0),
                ], $area);
                return;
            }

            $this->render('orders/form', [
                'pageTitle' => $area === 'frontoffice' ? 'Place Order' : 'New Order',
                'area' => $area,
                'currentSection' => 'orders',
                'mode' => 'create',
                'products' => $this->productsToArrays($this->fetchAllProducts()),
                'selectedProduct' => $this->productToArray($this->findProductById((int) ($data['product_id'] ?? 0))),
                'errors' => $errors,
                'statusOptions' => order_backoffice_status_options(),
                'values' => $this->prepareOrderFormData($data, (int) ($data['product_id'] ?? 0)),
            ], $area);
            return;
        }

        $order = $this->orderFromValues($data);
        $id = $this->createOrder($order);
        $this->redirect($area . '/orders/show', ['id' => $id]);
    }

    public function update(string $area): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $order = $this->findOrderById($id);
        if ($order === null) {
            $this->renderNotFound();
            return;
        }

        $data = $this->prepareOrderInput($_POST, $area, 'edit', $order);
        $errors = $this->validateOrder($data);
        if ($errors !== []) {
            $this->render('orders/form', [
                'pageTitle' => 'Edit Order',
                'area' => $area,
                'currentSection' => 'orders',
                'mode' => 'edit',
                'order' => $this->orderToArray($order),
                'products' => $this->productsToArrays($this->fetchAllProducts()),
                'selectedProduct' => $this->productToArray($this->findProductById((int) ($data['product_id'] ?? 0))),
                'errors' => $errors,
                'statusOptions' => order_backoffice_status_options(),
                'values' => $this->prepareOrderFormData($data, (int) ($data['product_id'] ?? 0)),
            ], $area);
            return;
        }

        $updatedOrder = $this->orderFromValues($data, $id);
        $this->updateOrder($updatedOrder);
        $this->redirect($area . '/orders/show', ['id' => $id]);
    }

    public function delete(string $area): void
    {
        $this->deleteOrder((int) ($_GET['id'] ?? 0));
        $this->redirect($area . '/orders');
    }
}
