<?php
declare(strict_types=1);

require_once ROOT_PATH . '/models/Exercise.php';

final class Program
{
    /**
     * @param Exercise[] $exercises
     */
    public function __construct(
        private ?int $id = null,
        private string $title = '',
        private ?string $goalType = null,
        private int $durationWeeks = 0,
        private ?string $description = null,
        private int $exerciseCount = 0,
        private array $exercises = []
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

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getGoalType(): ?string
    {
        return $this->goalType;
    }

    public function setGoalType(?string $goalType): void
    {
        $this->goalType = $goalType;
    }

    public function getDurationWeeks(): int
    {
        return $this->durationWeeks;
    }

    public function setDurationWeeks(int $durationWeeks): void
    {
        $this->durationWeeks = $durationWeeks;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getExerciseCount(): int
    {
        return $this->exerciseCount;
    }

    public function setExerciseCount(int $exerciseCount): void
    {
        $this->exerciseCount = $exerciseCount;
    }

    /**
     * @return Exercise[]
     */
    public function getExercises(): array
    {
        return $this->exercises;
    }

    /**
     * @param Exercise[] $exercises
     */
    public function setExercises(array $exercises): void
    {
        $this->exercises = $exercises;
        $this->exerciseCount = count($exercises);
    }

    public function addExercise(Exercise $exercise): void
    {
        $this->exercises[] = $exercise;
        $this->exerciseCount = count($this->exercises);
    }
}
