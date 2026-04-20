<?php
declare(strict_types=1);

final class Exercise
{
    public function __construct(
        private ?int $id = null,
        private ?int $programId = null,
        private string $name = '',
        private ?string $description = null,
        private ?string $muscleGroup = null,
        private int $sets = 0,
        private int $reps = 0,
        private int $restSeconds = 0
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

    public function getProgramId(): ?int
    {
        return $this->programId;
    }

    public function setProgramId(?int $programId): void
    {
        $this->programId = $programId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getMuscleGroup(): ?string
    {
        return $this->muscleGroup;
    }

    public function setMuscleGroup(?string $muscleGroup): void
    {
        $this->muscleGroup = $muscleGroup;
    }

    public function getSets(): int
    {
        return $this->sets;
    }

    public function setSets(int $sets): void
    {
        $this->sets = $sets;
    }

    public function getReps(): int
    {
        return $this->reps;
    }

    public function setReps(int $reps): void
    {
        $this->reps = $reps;
    }

    public function getRestSeconds(): int
    {
        return $this->restSeconds;
    }

    public function setRestSeconds(int $restSeconds): void
    {
        $this->restSeconds = $restSeconds;
    }
}
