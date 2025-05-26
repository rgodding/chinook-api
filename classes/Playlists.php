<?php

require_once BASE_PATH . '/database/DB.php';

class Playlists extends DB
{
    function list(): array
    {
        $sql = <<<SQL
            SELECT 
            playlist.PlaylistId,
            playlist.Name
            FROM playlist
        SQL;
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return [
                ApiResponse::POS_STATUS => ApiResponse::STATUS_SUCCESS,
                ApiResponse::POS_DATA => $stmt->fetchAll()
            ];
        } catch (PDOException $e) {
            logError("Error listing playlists: " . $e->getMessage());
            return [
                ApiResponse::POS_STATUS => ApiResponse::STATUS_ERROR,
                ApiResponse::POS_MESSAGE => 'Error listing playlists'
            ];
        }
    }

    function search(string $search): array
    {
        $sql = <<<SQL
            SELECT 
            playlist.PlaylistId,
            playlist.Name
            FROM playlist
            WHERE playlist.Name LIKE :search
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
            logError("Error searching playlists: " . $e->getMessage());
            return [
                ApiResponse::POS_STATUS => ApiResponse::STATUS_ERROR,
                ApiResponse::POS_MESSAGE => 'Error searching playlists'
            ];
        }
    }

    function get(int $id): array
    {
        // Get a specific playlist by ID, including tracks
        $sql = <<<SQL
            SELECT 
            playlist.PlaylistId,
            playlist.Name
            FROM playlist
            WHERE playlist.PlaylistId = :id
        SQL;
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch();
            if(!$result) {
                return [
                    ApiResponse::POS_STATUS => ApiResponse::STATUS_SUCCESS_NOT_FOUND,
                    ApiResponse::POS_MESSAGE => 'No playlist found with ID (' . $id . ')'
                ];
            }
            return [
                ApiResponse::POS_STATUS => ApiResponse::STATUS_SUCCESS,
                ApiResponse::POS_DATA => $result
            ];
        } catch (PDOException $e) {
            logError("Error getting playlist: " . $e->getMessage());
            return [
                ApiResponse::POS_STATUS => ApiResponse::STATUS_ERROR,
                ApiResponse::POS_MESSAGE => 'Error getting playlist'
            ];
        }
    }

    function getTracks(int $id): array
    {
        // Get tracks in a specific playlist by ID
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
            FROM playlisttrack
            JOIN track ON playlisttrack.TrackId = track.TrackId
            JOIN genre ON track.GenreId = genre.GenreId
            JOIN mediatype ON track.MediaTypeId = mediatype.MediaTypeId
            WHERE playlisttrack.PlaylistId = :id
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
            logError("Error getting tracks for playlist: " . $e->getMessage());
            return [
                ApiResponse::POS_STATUS => ApiResponse::STATUS_ERROR,
                ApiResponse::POS_MESSAGE => 'Error getting tracks for playlist'
            ];
        }
    }

    function create(string $name): array
    {
        $sql = <<<SQL
            INSERT INTO playlist (Name)
            VALUES (:name)
        SQL;
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':name', $name, PDO::PARAM_STR);
            $stmt->execute();
            $playlistId = $this->pdo->lastInsertId();
            return [
                ApiResponse::POS_STATUS => ApiResponse::STATUS_SUCCESS_CREATED,
                ApiResponse::POS_MESSAGE => 'Playlist created successfully',
                ApiResponse::POS_DATA => [
                    'PlaylistId' => $playlistId,
                    'Name' => $name
                ]
            ];
        } catch (PDOException $e) {
            logError("Error creating playlist: " . $e->getMessage());
            return [
                ApiResponse::POS_STATUS => ApiResponse::STATUS_ERROR,
                ApiResponse::POS_MESSAGE => 'Error creating playlist'
            ];
        }
    }

    function addTrack($playlistId, $trackId): array
    {
        $sql = <<<SQL
            INSERT INTO playlisttrack (PlaylistId, TrackId)
            VALUES (:playlistId, :trackId)
        SQL;
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':playlistId', $playlistId, PDO::PARAM_INT);
            $stmt->bindValue(':trackId', $trackId, PDO::PARAM_INT);
            $stmt->execute();
            return [
                ApiResponse::POS_STATUS => ApiResponse::STATUS_SUCCESS_CREATED,
                ApiResponse::POS_MESSAGE => 'Track added to playlist successfully',
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
                    ApiResponse::POS_MESSAGE => 'Track already exists in playlist'
                ];
            }
            logError("Error adding track to playlist: " . $e->getMessage());
            return [
                ApiResponse::POS_STATUS => ApiResponse::STATUS_ERROR,
                ApiResponse::POS_MESSAGE => 'Error adding track to playlist'
            ];
        }
    }

    function removeTrack($playlistId, $trackId): array
    {
        $sql = <<<SQL
            DELETE FROM playlisttrack 
            WHERE PlaylistId = :playlistId AND TrackId = :trackId
        SQL;
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':playlistId', $playlistId, PDO::PARAM_INT);
            $stmt->bindValue(':trackId', $trackId, PDO::PARAM_INT);
            $stmt->execute();
            return [
                ApiResponse::POS_STATUS => ApiResponse::STATUS_SUCCESS_NO_CONTENT,
                ApiResponse::POS_MESSAGE => 'Track removed from playlist successfully'
            ];
        } catch (PDOException $e) {
            logError("Error removing track from playlist: " . $e->getMessage());
            return [
                ApiResponse::POS_STATUS => ApiResponse::STATUS_ERROR,
                ApiResponse::POS_MESSAGE => 'Error removing track from playlist'
            ];
        }
    }

    function delete(int $id): array
    {
        // Delete a playlist by ID
        $sql = <<<SQL
            DELETE FROM playlist 
            WHERE PlaylistId = :id
        SQL;
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            return [
                ApiResponse::POS_STATUS => ApiResponse::STATUS_SUCCESS_NO_CONTENT,
                ApiResponse::POS_MESSAGE => 'Playlist deleted successfully'
            ];
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                // Foreign key constraint violation
                return [
                    ApiResponse::POS_STATUS => ApiResponse::STATUS_ERROR_CONFLICT,
                    ApiResponse::POS_MESSAGE => 'Cannot delete playlist with existing tracks'
                ];
            }
            logError("Error deleting playlist: " . $e->getMessage());
            return [
                ApiResponse::POS_STATUS => ApiResponse::STATUS_ERROR,
                ApiResponse::POS_MESSAGE => 'Error deleting playlist'
            ];
        }
    }
}

?>