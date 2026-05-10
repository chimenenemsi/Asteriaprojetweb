<?php
declare(strict_types=1);

require_once ROOT_PATH . '/models/BaseModel.php';
require_once ROOT_PATH . '/models/Program.php';

final class ProgramModel extends BaseModel
{
    /**
     * @return Program[]
     */
    public function findAll(): array
    {
        $sql = 'SELECT p.id, p.title, p.goal_type, p.duration_weeks, p.description, COUNT(e.id) AS exercise_count
            FROM programs p
            LEFT JOIN exercises e ON e.program_id = p.id
            GROUP BY p.id, p.title, p.goal_type, p.duration_weeks, p.description
            ORDER BY p.id DESC';
            
        try {
            $statement = $this->db->query($sql);
        } catch (PDOException) {
             // Fallback if exercise_count fails or created_at issues
             $statement = $this->db->query('SELECT * FROM programs ORDER BY id DESC');
        }

        return array_map([$this, 'mapProgramRow'], $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    public function findById(int $id): ?Program
    {
        $statement = $this->db->prepare(
            'SELECT p.id, p.title, p.goal_type, p.duration_weeks, p.description, COUNT(e.id) AS exercise_count
            FROM programs p
            LEFT JOIN exercises e ON e.program_id = p.id
            WHERE p.id = :id
            GROUP BY p.id, p.title, p.goal_type, p.duration_weeks, p.description
            LIMIT 1'
        );
        $statement->execute(['id' => $id]);
        $row = $statement->fetch(PDO::FETCH_ASSOC);

        return $row === false ? null : $this->mapProgramRow($row);
    }

    public function create(array $data): int
    {
        $statement = $this->db->prepare(
            'INSERT INTO programs (title, goal_type, duration_weeks, description)
            VALUES (:title, :goal_type, :duration_weeks, :description)'
        );
        $statement->execute([
            'title' => $data['title'],
            'goal_type' => $data['goal_type'] ?? null,
            'duration_weeks' => (int)($data['duration_weeks'] ?? 0),
            'description' => $data['description'] ?? null,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function delete(int $id): bool
    {
        $statement = $this->db->prepare('DELETE FROM programs WHERE id = :id');
        $statement->execute(['id' => $id]);
        return $statement->rowCount() > 0;
    }

    private function mapProgramRow(array $row): Program
    {
        return new Program(
            (int) $row['id'],
            (string) $row['title'],
            $row['goal_type'] !== null ? (string) $row['goal_type'] : null,
            (int) $row['duration_weeks'],
            $row['description'] !== null ? (string) $row['description'] : null,
            (int) ($row['exercise_count'] ?? 0)
        );
    }
}
