<?php
declare(strict_types=1);

define('ROOT_PATH', __DIR__);
require_once ROOT_PATH . '/config/Database.php';
class_alias('Config\Database', 'Database');

use Config\Database;

try {
    $pdo = Database::connection();
    echo "Connected to database.\n";

    // 1. Seed Diet Plans
    $plans = [
        [
            'title' => 'Perte de Poids Express',
            'goal' => 'Perdre 5kg en un mois',
            'duration_days' => 30,
            'description' => 'Un programme hypocalorique riche en fibres et protéines.',
            'target_calories_per_day' => 1500,
            'level' => 'BEGINNER',
            'status' => 'ACTIVE'
        ],
        [
            'title' => 'Prise de Masse Musculaire',
            'goal' => 'Gagner du muscle sec',
            'duration_days' => 60,
            'description' => 'Programme riche en protéines et glucides complexes.',
            'target_calories_per_day' => 2800,
            'level' => 'INTERMEDIATE',
            'status' => 'ACTIVE'
        ],
        [
            'title' => 'Équilibre Végétarien',
            'goal' => 'Stabiliser son poids sans viande',
            'duration_days' => 45,
            'description' => 'Focus sur les protéines végétales et les micronutriments.',
            'target_calories_per_day' => 2000,
            'level' => 'BEGINNER',
            'status' => 'ACTIVE'
        ]
    ];

    $stmtPlan = $pdo->prepare("INSERT INTO diet_plans (title, goal, duration_days, description, target_calories_per_day, level, status) 
                               VALUES (:title, :goal, :duration_days, :description, :target_calories_per_day, :level, :status)");

    foreach ($plans as $plan) {
        $stmtPlan->execute($plan);
        $planId = $pdo->lastInsertId();
        echo "Inserted Diet Plan: {$plan['title']} (ID: $planId)\n";

        // 2. Seed some recipes for each plan
        $recipes = [
            [
                'diet_plan_id' => $planId,
                'day_number' => 1,
                'meal_type' => 'BREAKFAST',
                'name' => 'Smoothie Protéiné aux Baies',
                'calories' => 350,
                'proteins' => 25,
                'carbs' => 40,
                'fats' => 8,
                'prep_time_minutes' => 10,
                'instructions' => 'Mixer tous les ingrédients jusqu\'à obtention d\'une texture lisse.'
            ],
            [
                'diet_plan_id' => $planId,
                'day_number' => 1,
                'meal_type' => 'LUNCH',
                'name' => 'Salade de Quinoa et Poulet',
                'calories' => 550,
                'proteins' => 35,
                'carbs' => 50,
                'fats' => 15,
                'prep_time_minutes' => 20,
                'instructions' => 'Mélanger le quinoa cuit, le poulet grillé et les légumes frais.'
            ]
        ];

        $stmtRecipe = $pdo->prepare("INSERT INTO recipes (diet_plan_id, day_number, meal_type, name, calories, proteins, carbs, fats, prep_time_minutes, instructions) 
                                     VALUES (:diet_plan_id, :day_number, :meal_type, :name, :calories, :proteins, :carbs, :fats, :prep_time_minutes, :instructions)");
        
        foreach ($recipes as $recipe) {
            $stmtRecipe->execute($recipe);
        }
        echo "  Added 2 recipes for plan ID: $planId\n";
    }

    echo "\nSeeding completed successfully!\n";

} catch (Exception $e) {
    echo "Error during seeding: " . $e->getMessage() . "\n";
}
