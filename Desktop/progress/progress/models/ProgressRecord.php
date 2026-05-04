<?php
declare(strict_types=1);

final class ProgressRecord
{
    public function __construct(
        private ?int $id = null,
        private ?int $progressGoalId = null,
        private string $recordDate = '',
        private float $recordedValue = 0.0,
        private ?int $adherenceScore = null,
        private string $mood = 'STEADY',
        private string $recordType = 'CHECKPOINT',
        private ?string $notes = null,
        private ?string $createdAt = null,
        private ?string $updatedAt = null,
        private ?string $goalTitle = null,
        private ?string $goalUnit = null
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

    public function getProgressGoalId(): ?int
    {
        return $this->progressGoalId;
    }

    public function setProgressGoalId(?int $progressGoalId): void
    {
        $this->progressGoalId = $progressGoalId;
    }

    public function getRecordDate(): string
    {
        return $this->recordDate;
    }

    public function setRecordDate(string $recordDate): void
    {
        $this->recordDate = $recordDate;
    }

    public function getRecordedValue(): float
    {
        return $this->recordedValue;
    }

    public function setRecordedValue(float $recordedValue): void
    {
        $this->recordedValue = $recordedValue;
    }

    public function getAdherenceScore(): ?int
    {
        return $this->adherenceScore;
    }

    public function setAdherenceScore(?int $adherenceScore): void
    {
        $this->adherenceScore = $adherenceScore;
    }

    public function getMood(): string
    {
        return $this->mood;
    }

    public function setMood(string $mood): void
    {
        $this->mood = $mood;
    }

    public function getRecordType(): string
    {
        return $this->recordType;
    }

    public function setRecordType(string $recordType): void
    {
        $this->recordType = $recordType;
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

    public function getGoalTitle(): ?string
    {
        return $this->goalTitle;
    }

    public function setGoalTitle(?string $goalTitle): void
    {
        $this->goalTitle = $goalTitle;
    }

    public function getGoalUnit(): ?string
    {
        return $this->goalUnit;
    }

    public function setGoalUnit(?string $goalUnit): void
    {
        $this->goalUnit = $goalUnit;
    }
}
