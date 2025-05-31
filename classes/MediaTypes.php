<?php

require_once BASE_PATH . '/database/DB.php';

class MediaTypes extends DB
{
    public function list(): array
    {
        $sql = <<<SQL
            SELECT *
            FROM MediaType
        SQL;
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return [
                ApiResponse::POS_STATUS => ApiResponse::STATUS_SUCCESS,
                ApiResponse::POS_DATA => $stmt->fetchAll(PDO::FETCH_ASSOC),
            ];
        } catch (PDOException $e) {
           Logger::LogError('Failed to fetch genres: ' . $e->getMessage());
            return [
                ApiResponse::POS_STATUS => ApiResponse::STATUS_ERROR,
                ApiResponse::POS_MESSAGE => 'Failed to fetch genres',
            ];
        }
    }
}