# chinook-api

## API

### Albums
- `GET /albums` — Get a list of all albums  
- `GET /albums?s={searchTerm}` — Search albums by title (query param: `s`)  
- `GET /albums/{id}` — Get a single album by ID  

- `GET /albums/{id}/tracks` — Get tracks for a specific album  

- `POST /albums` — Create a new album  
  - **Body:** `{ "artist_id": int, "title": string }`

- `PUT /albums/{id}` — Update an existing album  
  - **Body:** `{ "artist_id": int, "title": string }`

- `DELETE /albums/{id}` — Delete an album by ID  
---
### Artists
- `GET /artists` — Get a list of all artists  
- `GET /artists?s={searchTerm}` — Search artists by name (query param: `s`)  
- `GET /artists/{id}` — Get a single artist by ID  

- `GET /artists/{id}/albums` — Get albums for a specific artist  

- `POST /artists` — Create a new artist  
  - **Body:** `{ "name": string }`

- `DELETE /artists/{id}` — Delete an artist by ID  
---
### Tracks
- `GET /tracks?s={searchTerm}` — Search tracks by name (query param: `s`)  
- `GET /tracks?composer={composerName}` — Search tracks by composer (query param: `composer`)  
- `GET /tracks/{id}` — Get a single track by ID  

- `POST /tracks` — Create a new track  
  - **Body:**  
    ```json
    {
      "name": "string",
      "album_id": number,
      "media_type_id": number,
      "genre_id": number,
      "composer": "string",
      "milliseconds": number,
      "bytes": number,
      "unit_price": number
    }
    ```

- `PUT /tracks/{id}` — Update a track (same body as POST)  

- `DELETE /tracks/{id}` — Delete a track by ID  
---
### MediaTypes
- `GET /media_types` — Get a list of all media types  
---
### Genres
- `GET /genres` — Get a list of all genres  
---
### Playlists
- `GET /playlists` — Get a list of all playlists  
- `GET /playlists?s={searchTerm}` — Search playlists by name (query param: `s`)  
- `GET /playlists/{id}` — Get a single playlist by ID  

- `POST /playlists` — Create a new playlist  
  - **Body:** `{ "name": string }`

- `POST /playlists/{playlist_id}/tracks` — Add a track to a playlist  
  - **Body:** `{ "track_id": int }`

- `DELETE /playlists/{playlist_id}/tracks/{track_id}` — Remove a track from a playlist  

- `DELETE /playlists/{id}` — Delete a playlist by ID  
