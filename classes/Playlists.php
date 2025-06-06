<?php

require_once BASE_PATH . '/database/DB.php';

class Playlists extends DB
{
    function list(): array
    {
        $sql = <<<SQL
            SELECT 
            Playlist.PlaylistId,
            Playlist.Name
            FROM Playlist
        SQL;
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return [
                ApiResponse::POS_STATUS => ApiResponse::STATUS_SUCCESS,
                ApiResponse::POS_DATA => $stmt->fetchAll()
            ];
        } catch (PDOException $e) {
           Logger::LogError("Error listing Playlists: " . $e->getMessage());
            return [
                ApiResponse::POS_STATUS => ApiResponse::STATUS_ERROR,
                ApiResponse::POS_MESSAGE => 'Error listing Playlists'
            ];
        }
    }

    function search(string $search): array
    {
        $search = InputSanitizer::clean($search);
        $sql = <<<SQL
            SELECT 
            Playlist.PlaylistId,
            Playlist.Name
            FROM Playlist
            WHERE Playlist.Name LIKE :search
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
           Logger::LogError("Error searching Playlists: " . $e->getMessage());
            return [
                ApiResponse::POS_STATUS => ApiResponse::STATUS_ERROR,
                ApiResponse::POS_MESSAGE => 'Error searching Playlists'
            ];
        }
    }

    function get(int $id): array
    {
        // Get a specific Playlist by ID, including tracks
        $sql = <<<SQL
            SELECT 
            Playlist.PlaylistId,
            Playlist.Name
            FROM Playlist
            WHERE Playlist.PlaylistId = :id
        SQL;
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch();
            if(!$result) {
                return [
                    ApiResponse::POS_STATUS => ApiResponse::STATUS_SUCCESS_NOT_FOUND,
                    ApiResponse::POS_MESSAGE => 'No Playlist found with ID (' . $id . ')'
                ];
            }
            return [
                ApiResponse::POS_STATUS => ApiResponse::STATUS_SUCCESS,
                ApiResponse::POS_DATA => $result
            ];
        } catch (PDOException $e) {
           Logger::LogError("Error getting Playlist: " . $e->getMessage());
            return [
                ApiResponse::POS_STATUS => ApiResponse::STATUS_ERROR,
                ApiResponse::POS_MESSAGE => 'Error getting Playlist'
            ];
        }
    }

    function getTracks(int $id): array
    {
        // Get tracks in a specific Playlist by ID
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
            FROM PlaylistTrack
            JOIN Track ON PlaylistTrack.TrackId = Track.TrackId
            JOIN Genre ON Track.GenreId = Genre.GenreId
            JOIN MediaType ON Track.MediaTypeId = MediaType.MediaTypeId
            WHERE PlaylistTrack.PlaylistId = :id
        SQL;
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return [
                ApiResponse::POS_STATUS => ApiResponse::STATUS_SUCCESS,
                ApiResponse::POS_DATA => $stmt->fetchAll()
            ];
        } catch (PDOException $e) {
           Logger::LogError("Error getting tracks for Playlist: " . $e->getMessage());
            return [
                ApiResponse::POS_STATUS => ApiResponse::STATUS_ERROR,
                ApiResponse::POS_MESSAGE => 'Error getting tracks for Playlist'
            ];
        }
    }

    function create(string $name): array
    {
        $name = InputSanitizer::clean($name);
        $sql = <<<SQL
            INSERT INTO Playlist (Name)
            VALUES (:name)
        SQL;
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':name', $name, PDO::PARAM_STR);
            $stmt->execute();
            $playlistId = $this->pdo->lastInsertId();
            return [
                ApiResponse::POS_STATUS => ApiResponse::STATUS_SUCCESS_CREATED,
                ApiResponse::POS_MESSAGE => 'Playlist ID:(' . $playlistId . ') created successfully',
                ApiResponse::POS_DATA => [
                    'PlaylistId' => $playlistId,
                    'Name' => $name
                ]
            ];
        } catch (PDOException $e) {
           Logger::LogError("Error creating Playlist: " . $e->getMessage());
            return [
                ApiResponse::POS_STATUS => ApiResponse::STATUS_ERROR,
                ApiResponse::POS_MESSAGE => 'Error creating Playlist'
            ];
        }
    }

    function addTrack($playlistId, $trackId): array
    {
        $sql = <<<SQL
            INSERT INTO PlaylistTrack (PlaylistId, TrackId)
            VALUES (:playlistId, :trackId)
        SQL;
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':playlistId', $playlistId, PDO::PARAM_INT);
            $stmt->bindValue(':trackId', $trackId, PDO::PARAM_INT);
            $stmt->execute();
            return [
                ApiResponse::POS_STATUS => ApiResponse::STATUS_SUCCESS_CREATED,
                ApiResponse::POS_MESSAGE => 'Track (TrackId: ' . $trackId . ') added to Playlist (PlaylistId: ' . $playlistId . ') successfully',
                ApiResponse::POS_DATA => [
                    'PlaylistId' => $playlistId,
                    'TrackId' => $trackId
                ]
            ];
        } catch (PDOException $e) {
            if($e->getCode() === '23000') {
                // Duplicate entry error
                return [
                    ApiResponse::POS_STATUS => ApiResponse::STATUS_ERROR_CONFLICT,
                    ApiResponse::POS_MESSAGE => 'Track already exists in Playlist'
                ];
            }
           Logger::LogError("Error adding Track to Playlist: " . $e->getMessage());
            return [
                ApiResponse::POS_STATUS => ApiResponse::STATUS_ERROR,
                ApiResponse::POS_MESSAGE => 'Error adding Track to Playlist'
            ];
        }
    }

    function removeTrack($playlistId, $trackId): array
    {
        $sql = <<<SQL
            DELETE FROM PlaylistTrack 
            WHERE PlaylistId = :playlistId AND TrackId = :trackId
        SQL;
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':playlistId', $playlistId, PDO::PARAM_INT);
            $stmt->bindValue(':trackId', $trackId, PDO::PARAM_INT);
            $stmt->execute();
            return [
                ApiResponse::POS_STATUS => ApiResponse::STATUS_SUCCESS_NO_CONTENT,
                ApiResponse::POS_MESSAGE => 'Track (TrackId: ' . $trackId . ') removed from Playlist (PlaylistId: ' . $playlistId . ') successfully'
            ];
        } catch (PDOException $e) {
           Logger::LogError("Error removing Track from Playlist: " . $e->getMessage());
            return [
                ApiResponse::POS_STATUS => ApiResponse::STATUS_ERROR,
                ApiResponse::POS_MESSAGE => 'Error removing Track from Playlist'
            ];
        }
    }

    function delete(int $id): array
    {
        // Delete a Playlist by ID
        $sql = <<<SQL
            DELETE FROM Playlist 
            WHERE PlaylistId = :id
        SQL;
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            return [
                ApiResponse::POS_STATUS => ApiResponse::STATUS_SUCCESS_NO_CONTENT,
                ApiResponse::POS_MESSAGE => 'Playlist ID:(' . $id . ') deleted successfully'
            ];
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                // Foreign key constraint violation
                return [
                    ApiResponse::POS_STATUS => ApiResponse::STATUS_ERROR_CONFLICT,
                    ApiResponse::POS_MESSAGE => 'Cannot delete Playlist with existing tracks'
                ];
            }
           Logger::LogError("Error deleting Playlist: " . $e->getMessage());
            return [
                ApiResponse::POS_STATUS => ApiResponse::STATUS_ERROR,
                ApiResponse::POS_MESSAGE => 'Error deleting Playlist'
            ];
        }
    }
}

?>