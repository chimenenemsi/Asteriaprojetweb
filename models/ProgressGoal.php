<?php
declare(strict_types=1);

final class ProgressGoal
{
    public function __construct(
        private ?int $id = null,
        private ?int $userId = null,
        private string $title = '',
        private string $metric = '',
        private float $startValue = 0.0,
        private float $targetValue = 0.0,
        private string $unit = '',
        private string $startDate = '',
        private string $targetDate = '',
        private string $goalType = 'OTHER',
        private string $status = 'ACTIVE',
        private ?string $description = null,
        private ?string $createdAt = null,
        private ?string $updatedAt = null
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

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(?int $userId): void
    {
        $this->userId = $userId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getMetric(): string
    {
        return $this->metric;
    }

    public function setMetric(string $metric): void
    {
        $this->metric = $metric;
    }

    public function getStartValue(): float
    {
        return $this->startValue;
    }

    public function setStartValue(float $startValue): void
    {
        $this->startValue = $startValue;
    }

    public function getTargetValue(): float
    {
        return $this->targetValue;
    }

    public function setTargetValue(float $targetValue): void
    {
        $this->targetValue = $targetValue;
    }

    public function getUnit(): string
    {
        return $this->unit;
    }

    public function setUnit(string $unit): void
    {
        $this->unit = $unit;
    }

    public function getStartDate(): string
    {
        return $this->startDate;
    }

    public function setStartDate(string $startDate): void
    {
        $this->startDate = $startDate;
    }

    public function getTargetDate(): string
    {
        return $this->targetDate;
    }

    public function setTargetDate(string $targetDate): void
    {
        $this->targetDate = $targetDate;
    }

    public function getGoalType(): string
    {
        return $this->goalType;
    }

    public function setGoalType(string $goalType): void
    {
        $this->goalType = $goalType;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
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
}
