<?php
declare(strict_types=1);

final class Order
{
    public function __construct(
        private ?int $id = null,
        private ?int $productId = null,
        private string $productName = '',
        private string $customerName = '',
        private string $customerEmail = '',
        private int $quantity = 0,
        private float $unitPrice = 0.0,
        private float $totalAmount = 0.0,
        private string $status = 'PENDING',
        private string $orderDate = '',
        private ?string $shippingAddress = null,
        private ?string $notes = null,
        private ?string $createdAt = null,
        private ?string $updatedAt = null,
        private ?string $currentProductName = null,
        private ?string $currentProductSku = null,
        private ?int $currentProductStock = null,
        private ?string $currentProductStatus = null,
        private ?string $currentCategoryName = null
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getProductId(): ?int
    {
        return $this->productId;
    }

    public function setProductId(?int $productId): void
    {
        $this->productId = $productId;
    }

    public function getProductName(): string
    {
        return $this->productName;
    }

    public function setProductName(string $productName): void
    {
        $this->productName = $productName;
    }

    public function getCustomerName(): string
    {
        return $this->customerName;
    }

    public function setCustomerName(string $customerName): void
    {
        $this->customerName = $customerName;
    }

    public function getCustomerEmail(): string
    {
        return $this->customerEmail;
    }

    public function setCustomerEmail(string $customerEmail): void
    {
        $this->customerEmail = $customerEmail;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function getUnitPrice(): float
    {
        return $this->unitPrice;
    }

    public function setUnitPrice(float $unitPrice): void
    {
        $this->unitPrice = $unitPrice;
    }

    public function getTotalAmount(): float
    {
        return $this->totalAmount;
    }

    public function setTotalAmount(float $totalAmount): void
    {
        $this->totalAmount = $totalAmount;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getOrderDate(): string
    {
        return $this->orderDate;
    }

    public function setOrderDate(string $orderDate): void
    {
        $this->orderDate = $orderDate;
    }

    public function getShippingAddress(): ?string
    {
        return $this->shippingAddress;
    }

    public function setShippingAddress(?string $shippingAddress): void
    {
        $this->shippingAddress = $shippingAddress;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): void
    {
        $this->notes = $notes;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?string $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?string $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getCurrentProductName(): ?string
    {
        return $this->currentProductName;
    }

    public function setCurrentProductName(?string $currentProductName): void
    {
        $this->currentProductName = $currentProductName;
    }

    public function getCurrentProductSku(): ?string
    {
        return $this->currentProductSku;
    }

    public function setCurrentProductSku(?string $currentProductSku): void
    {
        $this->currentProductSku = $currentProductSku;
    }

    public function getCurrentProductStock(): ?int
    {
        return $this->currentProductStock;
    }

    public function setCurrentProductStock(?int $currentProductStock): void
    {
        $this->currentProductStock = $currentProductStock;
    }

    public function getCurrentProductStatus(): ?string
    {
        return $this->currentProductStatus;
    }

    public function setCurrentProductStatus(?string $currentProductStatus): void
    {
        $this->currentProductStatus = $currentProductStatus;
    }

    public function getCurrentCategoryName(): ?string
    {
        return $this->currentCategoryName;
    }

    public function setCurrentCategoryName(?string $currentCategoryName): void
    {
        $this->currentCategoryName = $currentCategoryName;
    }
}
