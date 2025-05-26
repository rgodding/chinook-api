<?php

require_once BASE_PATH . '/database/DB.php';

class Tracks extends DB
{
    function search(string $search): array
    {
        $sql = <<<SQL
            SELECT 
            track.TrackId,
            track.Name,
            track.Composer,
            track.Milliseconds,
            track.Bytes,
            track.UnitPrice,
            genre.GenreId,
            genre.Name AS GenreName,
            mediatype.MediaTypeId,
            mediatype.Name AS MediaTypeName
            FROM track
            JOIN genre ON track.GenreId = genre.GenreId
            JOIN mediatype ON track.MediaTypeId = mediatype.MediaTypeId
            WHERE track.Name LIKE :search
        SQL;
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':search', "%$search%");
            $stmt->execute();
            return [
                ApiResponse::POS_STATUS => ApiResponse::STATUS_SUCCESS,
                ApiResponse::POS_DATA => $stmt->fetchAll()
            ];
        } catch (PDOException $e) {
            logError("Error searching tracks: " . $e->getMessage());
            return [
                ApiResponse::POS_STATUS => ApiResponse::STATUS_ERROR,
                ApiResponse::POS_MESSAGE => 'Error searching tracks'
            ];
        }
    }

    function searchByComposer(string $composer): array
    {
        $sql = <<<SQL
            SELECT 
            track.TrackId,
            track.Name,
            track.Composer,
            track.Milliseconds,
            track.Bytes,
            track.UnitPrice,
            genre.GenreId,
            genre.Name AS GenreName,
            mediatype.MediaTypeId,
            mediatype.Name AS MediaTypeName
            FROM track
            JOIN genre ON track.GenreId = genre.GenreId
            JOIN mediatype ON track.MediaTypeId = mediatype.MediaTypeId
            WHERE track.Composer LIKE :composer
        SQL;
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':composer', "%$composer%");
            $stmt->execute();
            return [
                ApiResponse::POS_STATUS => ApiResponse::STATUS_SUCCESS,
                ApiResponse::POS_DATA => $stmt->fetchAll()
            ];
        } catch (PDOException $e) {
            logError("Error searching tracks by composer: " . $e->getMessage());
            return [
                ApiResponse::POS_STATUS => ApiResponse::STATUS_ERROR,
                ApiResponse::POS_MESSAGE => 'Error searching tracks by composer'
            ];
        }
    }

    function get(int $id): array
    {
        $sql = <<<SQL
            SELECT 
            track.TrackId,
            track.Name,
            track.Composer,
            track.Milliseconds,
            track.Bytes,
            track.UnitPrice,
            genre.GenreId,
            genre.Name AS GenreName,
            mediatype.MediaTypeId,
            mediatype.Name AS MediaTypeName
            FROM track
            JOIN genre ON track.GenreId = genre.GenreId
            JOIN mediatype ON track.MediaTypeId = mediatype.MediaTypeId
            WHERE track.TrackId = :id
        SQL;
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch();
            if (!$result) {
                return [
                    ApiResponse::POS_STATUS =>  ApiResponse::STATUS_SUCCESS_NOT_FOUND,
                    ApiResponse::POS_MESSAGE => 'No track found with this ID (' . $id . ')'
                ];
            }
            return [
                ApiResponse::POS_STATUS => ApiResponse::STATUS_SUCCESS,
                ApiResponse::POS_DATA => $result
            ];
        } catch (PDOException $e) {
            return [
                ApiResponse::POS_STATUS => ApiResponse::STATUS_ERROR,
                ApiResponse::POS_MESSAGE => 'Error getting track'
            ];
        }
    }

    function create(
        string $name,
        int $albumId,
        int $mediaTypeId,
        int $genreId,
        string $composer,
        int $milliseconds,
        int $bytes,
        float $unitPrice
    ): array {
        $sql = <<<SQL
            INSERT INTO TRACK (
                Name, 
                AlbumId, 
                MediaTypeId, 
                GenreId, 
                Composer, 
                Milliseconds, 
                Bytes, 
                UnitPrice
            ) VALUES (
                :name, 
                :albumId, 
                :mediaTypeId, 
                :genreId, 
                :composer, 
                :milliseconds, 
                :bytes, 
                :unitPrice
            )
        SQL;
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':name' => $name,
                ':albumId' => $albumId,
                ':mediaTypeId' => $mediaTypeId,
                ':genreId' => $genreId,
                ':composer' => $composer,
                ':milliseconds' => $milliseconds,
                ':bytes' => $bytes,
                ':unitPrice' => $unitPrice
            ]);
            $trackId = $this->pdo->lastInsertId();
            return [
                ApiResponse::POS_STATUS => ApiResponse::STATUS_SUCCESS_CREATED,
                ApiResponse::POS_MESSAGE => 'Track created successfully',
                ApiResponse::POS_DATA => [
                    'TrackId' => $trackId,
                    'Name' => $name,
                    'AlbumId' => $albumId,
                    'MediaTypeId' => $mediaTypeId,
                    'GenreId' => $genreId,
                    'Composer' => $composer,
                    'Milliseconds' => $milliseconds,
                    'Bytes' => $bytes,
                    'UnitPrice' => $unitPrice
                ]
            ];

        } catch (PDOException $e) {
            logError("Error creating track: " . $e->getMessage());
            return [
                ApiResponse::POS_STATUS => ApiResponse::STATUS_ERROR,
                ApiResponse::POS_MESSAGE => 'Error creating track'
            ];
        }
    }

    function update(
        int $id,
        string $name,
        int $albumId,
        int $mediaTypeId,
        int $genreId,
        string $composer,
        int $milliseconds,
        int $bytes,
        float $unitPrice
    ): array {
        $sql = <<<SQL
            UPDATE TRACK
            SET 
                Name = :name, 
                AlbumId = :albumId, 
                MediaTypeId = :mediaTypeId, 
                GenreId = :genreId, 
                Composer = :composer, 
                Milliseconds = :milliseconds, 
                Bytes = :bytes, 
                UnitPrice = :unitPrice
            WHERE TrackId = :id
        SQL;
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':id' => $id,
                ':name' => $name,
                ':albumId' => $albumId,
                ':mediaTypeId' => $mediaTypeId,
                ':genreId' => $genreId,
                ':composer' => $composer,
                ':milliseconds' => $milliseconds,
                ':bytes' => $bytes,
                ':unitPrice' => $unitPrice
            ]);
            return [
                ApiResponse::POS_STATUS => ApiResponse::STATUS_SUCCESS,
                ApiResponse::POS_MESSAGE => 'Track updated successfully',
                ApiResponse::POS_DATA => [
                    'TrackId' => $id,
                    'Name' => $name,
                    'AlbumId' => $albumId,
                    'MediaTypeId' => $mediaTypeId,
                    'GenreId' => $genreId,
                    'Composer' => $composer,
                    'Milliseconds' => $milliseconds,
                    'Bytes' => $bytes,
                    'UnitPrice' => $unitPrice
                ]
            ];
        } catch (PDOException $e) {
            logError("Error updating track: " . $e->getMessage());
            return [
                ApiResponse::POS_STATUS => ApiResponse::STATUS_ERROR,
                ApiResponse::POS_MESSAGE => 'Error updating track'
            ];
        }
    }
    
}
