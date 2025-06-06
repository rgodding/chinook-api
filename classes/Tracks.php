<?php

require_once BASE_PATH . '/database/DB.php';

class Tracks extends DB
{
    function search(string $search): array
    {
        $search = InputSanitizer::clean($search);
        $sql = <<<SQL
            SELECT 
            Track.TrackId,
            Track.Name,
            MediaType.MediaTypeId,
            MediaType.Name AS MediaTypeName,
            Genre.GenreId,
            Genre.Name AS GenreName,
            Track.Composer,
            Track.Milliseconds,
            Track.Bytes,
            Track.UnitPrice
            FROM Track
            JOIN Genre ON Track.GenreId = Genre.GenreId
            JOIN MediaType ON Track.MediaTypeId = MediaType.MediaTypeId
            WHERE Track.Name LIKE :search
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
           Logger::LogError("Error searching tracks: " . $e->getMessage());
            return [
                ApiResponse::POS_STATUS => ApiResponse::STATUS_ERROR,
                ApiResponse::POS_MESSAGE => 'Error searching tracks'
            ];
        }
    }

    function searchByComposer(string $composer): array
    {
        $composer = InputSanitizer::clean($composer);
        $sql = <<<SQL
            SELECT 
            Track.TrackId,
            Track.Name,
            MediaType.MediaTypeId,
            MediaType.Name AS MediaTypeName,
            Genre.GenreId,
            Genre.Name AS GenreName,
            Track.Composer,
            Track.Milliseconds,
            Track.Bytes,
            Track.UnitPrice
            FROM Track
            JOIN Genre ON Track.GenreId = Genre.GenreId
            JOIN MediaType ON Track.MediaTypeId = MediaType.MediaTypeId
            WHERE Track.Composer LIKE :composer
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
           Logger::LogError("Error searching tracks by composer: " . $e->getMessage());
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
            Track.TrackId,
            Track.Name,
            Track.Composer,
            Track.Milliseconds,
            Track.Bytes,
            Track.UnitPrice,
            Genre.GenreId,
            Genre.Name AS GenreName,
            MediaType.MediaTypeId,
            MediaType.Name AS MediaTypeName
            FROM Track
            JOIN Genre ON Track.GenreId = Genre.GenreId
            JOIN MediaType ON Track.MediaTypeId = MediaType.MediaTypeId
            WHERE Track.TrackId = :id
        SQL;
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch();
            if (!$result) {
                return [
                    ApiResponse::POS_STATUS =>  ApiResponse::STATUS_SUCCESS_NOT_FOUND,
                    ApiResponse::POS_MESSAGE => 'No Track found with this ID (' . $id . ')'
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
        $name = InputSanitizer::clean($name);
        $composer = InputSanitizer::clean($composer);
        $sql = <<<SQL
            INSERT INTO Track (
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
                ApiResponse::POS_MESSAGE => 'Track ID:(' . $trackId . ') created successfully',
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
           Logger::LogError("Error creating track: " . $e->getMessage());
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
            UPDATE Track
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
                ApiResponse::POS_MESSAGE => 'Track ID:(' . $id . ') updated successfully',
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
           Logger::LogError("Error updating track: " . $e->getMessage());
            return [
                ApiResponse::POS_STATUS => ApiResponse::STATUS_ERROR,
                ApiResponse::POS_MESSAGE => 'Error updating track'
            ];
        }
    }

    function delete(int $id): array
    {
        $sql = <<<SQL
            DELETE FROM Track
            WHERE TrackId = :id
        SQL;
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            if ($stmt->rowCount() === 0) {
                return [
                    ApiResponse::POS_STATUS => ApiResponse::STATUS_SUCCESS_NOT_FOUND,
                    ApiResponse::POS_MESSAGE => 'No Track found with this ID (' . $id . ')'
                ];
            }
            return [
                ApiResponse::POS_STATUS => ApiResponse::STATUS_SUCCESS_NO_CONTENT,
                ApiResponse::POS_MESSAGE => 'Track ID:(' . $id . ') deleted successfully'
            ];
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                // Foreign key constraint violation
                return [
                    ApiResponse::POS_STATUS => ApiResponse::STATUS_ERROR_CONFLICT,
                    ApiResponse::POS_MESSAGE => 'Cannot delete track, it is referenced by other records'
                ];
            }
           Logger::LogError("Error deleting track: " . $e->getMessage());
            return [
                ApiResponse::POS_STATUS => ApiResponse::STATUS_ERROR,
                ApiResponse::POS_MESSAGE => 'Error deleting track'
            ];
        }
    }
    
}
