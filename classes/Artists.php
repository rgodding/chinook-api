<?php

require_once BASE_PATH . '/database/DB.php';

class Artists extends DB
{
    public function list(): array
    {
        $sql = <<<SQL
            SELECT *
            FROM Artist
        SQL;
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return [
                ApiResponse::POS_STATUS => ApiResponse::STATUS_SUCCESS,
                ApiResponse::POS_DATA => $stmt->fetchAll(PDO::FETCH_ASSOC),
            ];
        } catch (PDOException $e) {
            logError('Failed to fetch artists: ' . $e->getMessage());
            return [
                ApiResponse::POS_STATUS => ApiResponse::STATUS_ERROR,
                ApiResponse::POS_MESSAGE => 'Failed to fetch artists',
            ];
        }
    }

    function search(string $query){
        $sql = <<<SQL
            SELECT *
            FROM Artist
            WHERE Name LIKE :query
        SQL;
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':query' => '%' . $query . '%'
            ]);
            return [
                ApiResponse::POS_STATUS => ApiResponse::STATUS_SUCCESS,
                ApiResponse::POS_DATA => $stmt->fetchAll(PDO::FETCH_ASSOC),
            ];
        } catch (PDOException $e) {
            logError('Failed to search artists: ' . $e->getMessage());
            return [
                ApiResponse::POS_STATUS => ApiResponse::STATUS_ERROR,
                ApiResponse::POS_MESSAGE => 'Failed to search artists',
            ];
        }
    }

    public function get(int $id): array
    {
        $sql = <<<SQL
            SELECT *
            FROM Artist
            WHERE ArtistId = :id
        SQL;
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':id' => $id
            ]);
            $artist = $stmt->fetch();
            if(!$artist) {
                return [
                    ApiResponse::POS_STATUS => ApiResponse::STATUS_SUCCESS_NOT_FOUND,
                    ApiResponse::POS_MESSAGE => 'No artist found with this ID (' . $id . ')',
                ];
            }
            return [
                ApiResponse::POS_STATUS => ApiResponse::STATUS_SUCCESS,
                ApiResponse::POS_DATA => $artist,
            ];
        } catch (PDOException $e) {
            logError('Failed to fetch artist by ID: ' . $e->getMessage());
            return [
                ApiResponse::POS_STATUS => ApiResponse::STATUS_ERROR,
                ApiResponse::POS_MESSAGE => 'Failed to fetch artist by ID',
            ];
        }
    }

    public function getAlbums(int $id): array
    {
        $sql = <<<SQL
            SELECT *
            FROM Album
            WHERE ArtistId = :id
        SQL;
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':id' => $id
            ]);
            $albums = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return [
                ApiResponse::POS_STATUS => ApiResponse::STATUS_SUCCESS,
                ApiResponse::POS_DATA => $albums,
            ];

        } catch (PDOException $e) {
            logError('Failed to fetch albums for artist ID ' . $id . ': ' . $e->getMessage());
            return [
                ApiResponse::POS_STATUS => ApiResponse::STATUS_ERROR,
                ApiResponse::POS_MESSAGE => 'Failed to fetch albums for artist',
            ];
        }
    }

    public function create(string $name): array
    {
        $sql = <<<SQL
            INSERT INTO Artist (Name)
            VALUES (:name)
        SQL;
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':name' =>  $name
            ]);
            $artistId = $this->pdo->lastInsertId();
            return [
                ApiResponse::POS_STATUS => ApiResponse::STATUS_SUCCESS_CREATED,
                ApiResponse::POS_MESSAGE => 'Artist ID:(' . $artistId . ') created successfully',
                ApiResponse::POS_DATA => [
                    'ArtistId' => $artistId,
                    'Name' => $name
                ]
            ];
        } catch (PDOException $e) {
            return [
                ApiResponse::POS_STATUS => ApiResponse::STATUS_ERROR,
                ApiResponse::POS_MESSAGE => 'Error creating artist'
            ];
        }
    }

    function delete(int $id): array
    {
        $sql = <<<SQL
            DELETE FROM Artist
            WHERE ArtistId = :id
        SQL;
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':id' => $id
            ]);
            if ($stmt->rowCount() === 0) {
                return [
                    ApiResponse::POS_STATUS => ApiResponse::STATUS_SUCCESS_NOT_FOUND,
                    ApiResponse::POS_MESSAGE => 'No artist found with this ID (' . $id . ')',
                ];
            }
            return [
                ApiResponse::POS_STATUS => ApiResponse::STATUS_SUCCESS_NO_CONTENT
            ];
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                // Foreign key constraint violation
                return [
                    ApiResponse::POS_STATUS => ApiResponse::STATUS_ERROR_CONFLICT,
                    ApiResponse::POS_MESSAGE => 'Artist ID:(' . $id . ') cannot be deleted due to existing albums',
                ];
            }
            logError('Failed to delete artist: ' . $e->getMessage());
            return [
                ApiResponse::POS_STATUS => ApiResponse::STATUS_ERROR,
                ApiResponse::POS_MESSAGE => 'Failed to delete artist',
            ];
        }
    }
}

