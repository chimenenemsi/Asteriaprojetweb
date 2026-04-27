<?php
use Config\Database;

class StaticPageController
{
    // ===== READ ALL with Search & Sort =====
    public static function obtenirTous()
    {
        try {
            $pdo = Database::getConnexion();
            
            $search = $_GET['search'] ?? '';
            $sort = $_GET['sort'] ?? 'id';
            $order = $_GET['order'] ?? 'DESC';

            $allowedSorts = ['id', 'slug', 'title', 'type'];
            if (!in_array($sort, $allowedSorts)) $sort = 'id';
            $order = (strtoupper($order) === 'ASC') ? 'ASC' : 'DESC';

            $sql = "SELECT * FROM static_pages WHERE 1=1";
            $params = [];

            if (!empty($search)) {
                $sql .= " AND (title LIKE :search OR slug LIKE :search OR type LIKE :search)";
                $params[':search'] = "%$search%";
            }

            $sql .= " ORDER BY $sort $order";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $pages = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(['success' => true, 'pages' => $pages]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // ===== CREATE =====
    public static function creer()
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
            $pdo = Database::getConnexion();
            $sql = "INSERT INTO static_pages (slug, title, eyebrow, intro, summary, image, type, content_json) 
                    VALUES (:slug, :title, :eyebrow, :intro, :summary, :image, :type, :content_json)";
            
            $stmt = $pdo->prepare($sql);
            $resultat = $stmt->execute([
                ':slug' => htmlspecialchars(trim($data['slug'] ?? '')),
                ':title' => htmlspecialchars(trim($data['title'] ?? '')),
                ':eyebrow' => htmlspecialchars(trim($data['eyebrow'] ?? '')),
                ':intro' => htmlspecialchars(trim($data['intro'] ?? '')),
                ':summary' => htmlspecialchars(trim($data['summary'] ?? '')),
                ':image' => $data['image'] ?? null,
                ':type' => $data['type'] ?? 'FRONT',
                ':content_json' => $data['content_json'] ?? null
            ]);

            echo json_encode(['success' => $resultat, 'id' => $pdo->lastInsertId()]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // ===== UPDATE =====
    public static function mettre_a_jour()
    {
        try {
            $id = intval($_GET['id'] ?? 0);
            $data = json_decode(file_get_contents('php://input'), true);
            $pdo = Database::getConnexion();
            $sql = "UPDATE static_pages SET slug = :slug, title = :title, eyebrow = :eyebrow, intro = :intro, 
                    summary = :summary, image = :image, type = :type, content_json = :content_json WHERE id = :id";
            
            $stmt = $pdo->prepare($sql);
            $resultat = $stmt->execute([
                ':id' => $id,
                ':slug' => htmlspecialchars(trim($data['slug'] ?? '')),
                ':title' => htmlspecialchars(trim($data['title'] ?? '')),
                ':eyebrow' => htmlspecialchars(trim($data['eyebrow'] ?? '')),
                ':intro' => htmlspecialchars(trim($data['intro'] ?? '')),
                ':summary' => htmlspecialchars(trim($data['summary'] ?? '')),
                ':image' => $data['image'] ?? null,
                ':type' => $data['type'] ?? 'FRONT',
                ':content_json' => $data['content_json'] ?? null
            ]);

            echo json_encode(['success' => $resultat]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // ===== DELETE =====
    public static function supprimer()
    {
        try {
            $id = intval($_GET['id'] ?? 0);
            $pdo = Database::getConnexion();
            $stmt = $pdo->prepare("DELETE FROM static_pages WHERE id = :id");
            $resultat = $stmt->execute([':id' => $id]);
            echo json_encode(['success' => $resultat]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
