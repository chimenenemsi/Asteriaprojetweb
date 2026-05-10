<?php
declare(strict_types=1);

require_once ROOT_PATH . '/models/BaseModel.php';
require_once ROOT_PATH . '/models/Exercise.php';

final class ExerciseModel extends BaseModel
{
    public function getAllWithProgram(): array
    {
        $sql = "SELECT e.*, p.title as program_title 
                FROM exercises e 
                JOIN programs p ON e.program_id = p.id 
                ORDER BY p.title, e.name";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findAllByProgram(int $programId): array
    {
        $statement = $this->db->prepare(
            'SELECT id, program_id, name, description, muscle_group, sets, reps, rest_seconds
            FROM exercises
            WHERE program_id = :program_id
            ORDER BY id ASC'
        );
        $statement->execute(['program_id' => $programId]);

        return array_map([$this, 'mapExerciseRow'], $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    public function create(array $data): bool
    {
        $sql = "INSERT INTO exercises (program_id, name, description, muscle_group, sets, reps, rest_seconds) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['program_id'],
            $data['name'],
            $data['description'],
            $data['muscle_group'],
            $data['sets'],
            $data['reps'],
            $data['rest_seconds']
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM exercises WHERE id = ?');
        return $stmt->execute([$id]);
    }

    private function mapExerciseRow(array $row): Exercise
    {
        return new Exercise(
            (int) $row['id'],
            (int) $row['program_id'],
            (string) $row['name'],
            $row['description'] !== null ? (string) $row['description'] : null,
            $row['muscle_group'] !== null ? (string) $row['muscle_group'] : null,
            (int) $row['sets'],
            (int) $row['reps'],
            (int) $row['rest_seconds']
        );
    }
}
