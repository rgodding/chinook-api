<?php

require_once BASE_PATH . '/database/DB.php';

class Albums extends DB
{
    public function list(): array
    {
        $sql = <<<SQL
            SELECT *
            FROM Album
        SQL;
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return [
                ApiResponse::POS_STATUS => ApiResponse::STATUS_SUCCESS,
                ApiResponse::POS_DATA => $stmt->fetchAll(PDO::FETCH_ASSOC),
            ];
        } catch (PDOException $e) {
            logError('Failed to fetch albums: ' . $e->getMessage());
            return [
                ApiResponse::POS_STATUS => ApiResponse::STATUS_ERROR,
                ApiResponse::POS_MESSAGE => 'Failed to fetch albums',
            ];
        }
    }

    function search(string $query){
        $sql = <<<SQL
            SELECT *
            FROM Album
            WHERE Title LIKE :query
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
            logError('Failed to search albums: ' . $e->getMessage());
            return [
                ApiResponse::POS_STATUS => ApiResponse::STATUS_ERROR,
                ApiResponse::POS_MESSAGE => 'Failed to search albums',
            ];
        }
    }

    public function getById(int $id): array
    {
        $sql = <<<SQL
            SELECT *
            FROM album
            WHERE albumId = :id
        SQL;
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':id' => $id
            ]);
            $album = $stmt->fetch();
            if(!$album) {
                return [
                    ApiResponse::POS_STATUS => ApiResponse::STATUS_SUCCESS_NOT_FOUND,
                    ApiResponse::POS_MESSAGE => 'No album found with this ID (' . $id . ')',
                ];
            }
            return [
                ApiResponse::POS_STATUS => ApiResponse::STATUS_SUCCESS,
                ApiResponse::POS_DATA => $album,
            ];
        } catch (PDOException $e) {
            logError('Failed to fetch album by ID: ' . $e->getMessage());
            return [
                ApiResponse::POS_STATUS => ApiResponse::STATUS_ERROR,
                ApiResponse::POS_MESSAGE => 'Failed to fetch album by ID',
            ];
        }
    }

    public function getTracks(int $id): array
    {
        $sql = <<<SQL
            SELECT 
            track.TrackId,
            track.Name AS TrackName,
            track.GenreId,
            genre.Name AS GenreName,
            track.Composer,
            track.Milliseconds,
            track.Bytes,
            track.UnitPrice
            FROM track
            JOIN mediatype ON track.MediaTypeId = mediatype.MediaTypeId
            JOIN genre ON track.GenreId = genre.GenreId
            WHERE track.AlbumId = :id
        SQL;
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':id' => $id
            ]);
            return [
                ApiResponse::POS_STATUS => ApiResponse::STATUS_SUCCESS,
                ApiResponse::POS_DATA => $stmt->fetchAll(PDO::FETCH_ASSOC),
            ];
        } catch (PDOException $e) {
            logError('Failed to fetch tracks for album ID ' . $id . ': ' . $e->getMessage());
            return [
                ApiResponse::POS_STATUS => ApiResponse::STATUS_ERROR,
                ApiResponse::POS_MESSAGE => 'Failed to fetch tracks for album',
            ];
        }
    }

    function create(string $title, int $artistId): array
    {
        $sql = <<<SQL
            INSERT INTO album (Title, ArtistId)
            VALUES (:title, :artistId)
        SQL;
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':title' => $title,
                ':artistId' => $artistId
            ]);
            $albumId = $this->pdo->lastInsertId();
            return [
                ApiResponse::POS_STATUS => ApiResponse::STATUS_SUCCESS_CREATED,
                ApiResponse::POS_MESSAGE => 'Album created successfully',
                ApiResponse::POS_DATA => [
                    'AlbumId' => $albumId,
                    'Title' => $title,
                    'ArtistId' => $artistId
                ]
            ];

        } catch (PDOException $e) {
            return [
                ApiResponse::POS_STATUS => ApiResponse::STATUS_ERROR,
                ApiResponse::POS_MESSAGE => 'Error creating album'
            ];
        }

    }
}

