<?php
namespace App\Models;

class Recipe
{
    private $id;
    private $diet_plan_id;
    private $name;
    private $meal_type;
    private $day_number;
    private $calories;
    private $proteins;
    private $carbs;
    private $fats;
    private $prep_time_minutes;
    private $instructions;
    private $image;

    public function __construct($data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->diet_plan_id = intval($data['diet_plan_id'] ?? 0);
        $this->name = htmlspecialchars(trim($data['name'] ?? ''));
        $this->meal_type = $data['meal_type'] ?? 'BREAKFAST';
        $this->day_number = intval($data['day_number'] ?? 1);
        $this->calories = floatval($data['calories'] ?? 0);
        $this->proteins = floatval($data['proteins'] ?? 0);
        $this->carbs = floatval($data['carbs'] ?? 0);
        $this->fats = floatval($data['fats'] ?? 0);
        $this->prep_time_minutes = intval($data['prep_time_minutes'] ?? 0);
        $this->instructions = htmlspecialchars(trim($data['instructions'] ?? ''));
        $this->image = $data['image'] ?? null;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getDietPlanId() { return $this->diet_plan_id; }
    public function getName() { return $this->name; }
    public function getMealType() { return $this->meal_type; }
    public function getDayNumber() { return $this->day_number; }
    public function getCalories() { return $this->calories; }
    public function getProteins() { return $this->proteins; }
    public function getCarbs() { return $this->carbs; }
    public function getFats() { return $this->fats; }
    public function getPrepTime() { return $this->prep_time_minutes; }
    public function getInstructions() { return $this->instructions; }
}
