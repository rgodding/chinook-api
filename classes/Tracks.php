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
            error_log("Error searching tracks: " . $e->getMessage());
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
            error_log("Error searching tracks by composer: " . $e->getMessage());
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

    
}
