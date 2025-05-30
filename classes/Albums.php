<?php

require_once BASE_PATH . '/database/DB.php';

class Albums extends DB
{
    public function list(): array
    {
        // Include 
        $sql = <<<SQL
            SELECT 
            Album.*,
            Artist.Name AS ArtistName
            FROM Album
            JOIN Artist ON Album.ArtistId = Artist.ArtistId
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

    function search(string $query): array
    {
        $query = InputSanitizer::clean($query);
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

    public function get(int $id): array
    {
        $sql = <<<SQL
            SELECT 
            Album.*,
            Artist.Name AS ArtistName
            FROM Album
            JOIN Artist ON Album.ArtistId = Artist.ArtistId
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
            Track.TrackId,
            Track.Name AS TrackName,
            Track.MediaTypeId,
            MediaType.Name AS MediaTypeName,
            Track.GenreId,
            Genre.Name AS GenreName,
            Track.Composer,
            Track.Milliseconds,
            Track.Bytes,
            Track.UnitPrice
            FROM Track
            JOIN MediaType ON Track.MediaTypeId = MediaType.MediaTypeId
            JOIN Genre ON Track.GenreId = Genre.GenreId
            WHERE Track.AlbumId = :id
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
        $title = InputSanitizer::clean($title);
        $sql = <<<SQL
            INSERT INTO Album (Title, ArtistId)
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
                ApiResponse::POS_MESSAGE => 'Album ID:(' . $albumId . ') created successfully',
                ApiResponse::POS_DATA => [
                    'AlbumId' => $albumId,
                    'Title' => $title,
                    'ArtistId' => $artistId
                ]
            ];

        } catch (PDOException $e) {
            logError('Error creating album: ' . $e->getMessage());
            return [
                ApiResponse::POS_STATUS => ApiResponse::STATUS_ERROR,
                ApiResponse::POS_MESSAGE => 'Error creating album'
            ];
        }

    }

    function update(int $id, string $title, int $artistId): array
    {
        $title = InputSanitizer::clean($title);
        $sql = <<<SQL
            UPDATE Album
            SET Title = :title, ArtistId = :artistId
            WHERE AlbumId = :id
        SQL;
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':id' => $id,
                ':title' => $title,
                ':artistId' => $artistId
            ]);
            return [
                ApiResponse::POS_STATUS => ApiResponse::STATUS_SUCCESS,
                ApiResponse::POS_MESSAGE => 'Album ID:(' . $id . ') updated successfully',
                ApiResponse::POS_DATA => [
                    'AlbumId' => $id,
                    'Title' => $title,
                    'ArtistId' => $artistId
                ]
            ];
        } catch (PDOException $e) {
            logError('Error updating album: ' . $e->getMessage());
            return [
                ApiResponse::POS_STATUS => ApiResponse::STATUS_ERROR,
                ApiResponse::POS_MESSAGE => 'Error updating album'
            ];
        }
    }

    function delete(int $id): array
    {
        $sql = <<<SQL
            DELETE FROM Album
            WHERE AlbumId = :id
        SQL;
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':id' => $id
            ]);
            if ($stmt->rowCount() === 0) {
                return [
                    ApiResponse::POS_STATUS => ApiResponse::STATUS_SUCCESS_NOT_FOUND,
                    ApiResponse::POS_MESSAGE => 'No album found with ID: ' . $id
                ];
            }
            return [
                ApiResponse::POS_STATUS => ApiResponse::STATUS_SUCCESS_NO_CONTENT,
                ApiResponse::POS_MESSAGE => 'Album ID:(' . $id . ') deleted successfully'
            ];
        } catch (PDOException $e) {
            if( $e->getCode() === '23000') {
                logError('Error deleting album: ' . $e->getMessage());
                return [
                    ApiResponse::POS_STATUS => ApiResponse::STATUS_ERROR_CONFLICT,
                    ApiResponse::POS_MESSAGE => 'Cannot delete album, it is referenced by other records'
                ];
            }
            logError('Error deleting album: ' . $e->getMessage());
            return [
                ApiResponse::POS_STATUS => ApiResponse::STATUS_ERROR,
                ApiResponse::POS_MESSAGE => 'Error deleting album'
            ];
        }
    }
}

