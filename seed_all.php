<?php
declare(strict_types=1);

define('ROOT_PATH', __DIR__);
require_once ROOT_PATH . '/config/Database.php';
class_alias('Config\Database', 'Database');

use Config\Database;

try {
    $pdo = Database::connection();
    echo "--- Database Seeding Report ---\n";

    // 1. CLEAR OLD DATA (Optional, but helps for a clean seed)
    // $pdo->exec("SET FOREIGN_KEY_CHECKS = 0; TRUNCATE users; TRUNCATE products; TRUNCATE programs; TRUNCATE diet_plans; SET FOREIGN_KEY_CHECKS = 1;");

    // 2. SEED USERS
    $users = [
        ['fullname' => 'Admin Asteria', 'email' => 'admin@asteria.com', 'password' => password_hash('admin123', PASSWORD_DEFAULT), 'role' => 'admin', 'secret_code' => password_hash('1234', PASSWORD_DEFAULT)],
        ['fullname' => 'Issra Ben Taher', 'email' => 'issra@asteria.com', 'password' => password_hash('user123', PASSWORD_DEFAULT), 'role' => 'user', 'secret_code' => password_hash('5678', PASSWORD_DEFAULT)],
        ['fullname' => 'Jean Dupont', 'email' => 'jean@asteria.com', 'password' => password_hash('jean123', PASSWORD_DEFAULT), 'role' => 'user', 'secret_code' => password_hash('0000', PASSWORD_DEFAULT)]
    ];
    $stmtUser = $pdo->prepare("INSERT IGNORE INTO users (fullname, email, password, role, secret_code) VALUES (:fullname, :email, :password, :role, :secret_code)");
    foreach ($users as $u) { $stmtUser->execute($u); }
    echo "✅ Users seeded.\n";

    // 3. SEED PRODUCT CATEGORIES
    $categories = [
        ['name' => 'Nutrition & Alimentation', 'description' => 'Produits bio et sains'],
        ['name' => 'Équipement Fitness', 'description' => 'Matériel de sport'],
        ['name' => 'Compléments Alimentaires', 'description' => 'Vitamines et minéraux'],
        ['name' => 'Boissons Bio', 'description' => 'Thés, jus et infusions']
    ];
    $stmtCat = $pdo->prepare("INSERT IGNORE INTO product_categories (name, description) VALUES (:name, :description)");
    foreach ($categories as $c) { $stmtCat->execute($c); }
    echo "✅ Categories seeded.\n";

    // 4. SEED PRODUCTS
    $nutritionCatId = $pdo->query("SELECT id FROM product_categories WHERE name LIKE 'Nutrition%' LIMIT 1")->fetchColumn();
    $drinkCatId = $pdo->query("SELECT id FROM product_categories WHERE name LIKE 'Boissons%' LIMIT 1")->fetchColumn();

    $products = [
        ['product_category_id' => $nutritionCatId, 'name' => 'Beurre d\'Amande Bio', 'sku' => 'NUT-001', 'price' => 12.50, 'stock_quantity' => 50, 'description' => '100% amandes grillées.'],
        ['product_category_id' => $nutritionCatId, 'name' => 'Graines de Chia', 'sku' => 'NUT-002', 'price' => 8.90, 'stock_quantity' => 100, 'description' => 'Fibres et Oméga-3.'],
        ['product_category_id' => $drinkCatId, 'name' => 'Thé Vert Matcha', 'sku' => 'DRK-001', 'price' => 15.00, 'stock_quantity' => 30, 'description' => 'Énergie et antioxydants.'],
        ['product_category_id' => $drinkCatId, 'name' => 'Eau de Coco Bio', 'sku' => 'DRK-002', 'price' => 4.50, 'stock_quantity' => 200, 'description' => 'Hydratation naturelle.']
    ];
    $stmtProd = $pdo->prepare("INSERT IGNORE INTO products (product_category_id, name, sku, price, stock_quantity, description) VALUES (:product_category_id, :name, :sku, :price, :stock_quantity, :description)");
    foreach ($products as $p) { $stmtProd->execute($p); }
    echo "✅ Products seeded.\n";

    // 5. SEED DIET PLANS & RECIPES
    $plans = [
        ['title' => 'Détox Printemps', 'goal' => 'Nettoyer l\'organisme', 'duration_days' => 14, 'description' => 'Focus sur les légumes verts et l\'hydratation.', 'target_calories_per_day' => 1400, 'level' => 'BEGINNER'],
        ['title' => 'Keto Performance', 'goal' => 'Brûler les graisses', 'duration_days' => 30, 'description' => 'Riche en bons lipides, très faible en glucides.', 'target_calories_per_day' => 2200, 'level' => 'ADVANCED']
    ];
    $stmtPlan = $pdo->prepare("INSERT IGNORE INTO diet_plans (title, goal, duration_days, description, target_calories_per_day, level) VALUES (:title, :goal, :duration_days, :description, :target_calories_per_day, :level)");
    $stmtRecipe = $pdo->prepare("INSERT IGNORE INTO recipes (diet_plan_id, day_number, meal_type, name, calories, proteins, carbs, fats, instructions) VALUES (:diet_plan_id, :day_number, :meal_type, :name, :calories, :proteins, :carbs, :fats, :instructions)");

    foreach ($plans as $plan) {
        $stmtPlan->execute($plan);
        $planId = $pdo->lastInsertId();
        if ($planId) {
            $stmtRecipe->execute([
                'diet_plan_id' => $planId, 'day_number' => 1, 'meal_type' => 'BREAKFAST', 'name' => 'Avocat Toast Bio', 
                'calories' => 400, 'proteins' => 10, 'carbs' => 30, 'fats' => 25, 'instructions' => 'Pain complet, avocat, citron.'
            ]);
            $stmtRecipe->execute([
                'diet_plan_id' => $planId, 'day_number' => 1, 'meal_type' => 'DINNER', 'name' => 'Saumon Vapeur', 
                'calories' => 500, 'proteins' => 40, 'carbs' => 5, 'fats' => 30, 'instructions' => 'Saumon, brocolis, huile d\'olive.'
            ]);
        }
    }
    echo "✅ Diet Plans & Recipes seeded.\n";

    echo "\n--- Seeding Success ---\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
