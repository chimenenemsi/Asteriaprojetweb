<?php
namespace App\Models;

class DietPlan
{
    private $id;
    private $title;
    private $goal;
    private $duration_days;
    private $description;
    private $target_calories_per_day;
    private $level;
    private $status;

    public function __construct($data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->title = htmlspecialchars(trim($data['title'] ?? ''));
        $this->goal = htmlspecialchars(trim($data['goal'] ?? ''));
        $this->duration_days = intval($data['duration_days'] ?? 30);
        $this->description = htmlspecialchars(trim($data['description'] ?? ''));
        $this->target_calories_per_day = floatval($data['target_calories_per_day'] ?? 0);
        $this->level = $data['level'] ?? 'BEGINNER';
        $this->status = $data['status'] ?? 'ACTIVE';
    }

    // Getters
    public function getId() { return $this->id; }
    public function getTitle() { return $this->title; }
    public function getGoal() { return $this->goal; }
    public function getDurationDays() { return $this->duration_days; }
    public function getDescription() { return $this->description; }
    public function getTargetCalories() { return $this->target_calories_per_day; }
    public function getLevel() { return $this->level; }
    public function getStatus() { return $this->status; }
}
