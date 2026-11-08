# Movie Watchlist API - Backend

API backend untuk aplikasi Movie Watchlist yang memungkinkan pengguna mendaftar, login, melihat katalog film, menambahkan film ke watchlist, menandai film sebagai sudah ditonton, dan mendapatkan rekomendasi film menggunakan Google Gemini AI.

## Struktur Repositori

- `filmlog-backend/` - Direktori backend Laravel (API)
- `frontend/` - Aplikasi frontend HTML/CSS/JS standalone (tanpa build tool)
- `data/` - Data film dalam format JSON (20 film starter pack)

## Stack Teknologi

- **Laravel** 12.x (PHP ^8.2)
- **MySQL** 8.0+
- **JWT Authentication** (php-open-source-saver/jwt-auth 2.8)
- **Google Gemini AI** (gemini-3.8-flash)

## Prerequisites

- PHP ^8.2
- Composer
- MySQL 8.0+
- API Key Gemini dari [Google AI Studio](https://aistudio.google.com)

## Setup dari Nol

1. Clone repositori
2. Masuk ke direktori backend:
   ```bash
   cd filmlog-backend
   ```
3. Install dependencies:
   ```bash
   composer install
   ```
4. Copy file environment:
   ```bash
   cp .env.example .env
   ```
   Atau di Windows:
   ```cmd
   copy .env.example .env
   ```
5. Generate application key:
   ```bash
   php artisan key:generate
   ```
6. Generate JWT secret:
   ```bash
   php artisan jwt:secret
   ```
7. Buat database MySQL kosong
8. Konfigurasi file `.env` dengan pengaturan database Anda:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=nama_database
   DB_USERNAME=username
   DB_PASSWORD=password
   
   JWT_SECRET=(otomatis digenerate)
   
   GEMINI_API_KEY=your_gemini_api_key_here
   GEMINI_MODEL=gemini-3.8-flash
   ```
9. Jalankan migrasi dan seeder:
   ```bash
   php artisan migrate --seed
   ```
   *Seeder akan membaca data dari `../data/movies.json`*
10. Jalankan server:
    ```bash
    php artisan serve
    ```

## Environment Variables

| Variable | Keterangan |
|----------|------------|
| `DB_*` | Konfigurasi koneksi database MySQL |
| `JWT_SECRET` | Secret key untuk JWT authentication |
| `GEMINI_API_KEY` | API key untuk Google Gemini AI |
| `GEMINI_MODEL` | Model Gemini yang digunakan (default: gemini-3.8-flash) |

## Cara Menjalankan Bersama Frontend

1. **Backend**: Berjalan di port `8000` secara default (`http://localhost:8000`)
   ```bash
   cd filmlog-backend
   php artisan serve
   ```

2. **Frontend**: Berjalan di port `5500` (atau port lain sesuai preferensi)
   ```bash
   cd frontend
   py -m http.server 5500
   ```
   Alternatif:
   ```bash
   python -m http.server 5500
   ```
   atau
   ```bash
   python3 -m http.server 5500
   ```

3. Buka browser ke `http://localhost:5500`

**Catatan CORS**: Konfigurasi CORS saat ini mengizinkan semua origin (`'allowed_origins' => ['*']` di `config/cors.php`). Frontend di `http://localhost:5500` dapat mengakses API tanpa masalah.

## Endpoint API (10 endpoint)

### Authentication
| Method | Path | Auth | Keterangan |
|--------|------|------|------------|
| POST | `/api/auth/register` | ❌ | Registrasi pengguna baru |
| POST | `/api/auth/login` | ❌ | Login untuk mendapatkan token JWT |

### Movies (Publik)
| Method | Path | Auth | Keterangan |
|--------|------|------|------------|
| GET | `/api/movies` | ❌ | Mendapatkan daftar semua film |
| GET | `/api/movies/{movie}` | ❌ | Mendapatkan detail film spesifik |
| POST | `/api/movies/recommend` | ❌ | Mendapatkan rekomendasi film berdasarkan query |

### Watchlist (Perlu Auth)
| Method | Path | Auth | Keterangan |
|--------|------|------|------------|
| GET | `/api/watchlist` | ✅ | Mendapatkan daftar watchlist user |
| POST | `/api/watchlist` | ✅ | Menambahkan film ke watchlist |
| DELETE | `/api/watchlist/{movieId}` | ✅ | Menghapus film dari watchlist |

### Watched (Perlu Auth)
| Method | Path | Auth | Keterangan |
|--------|------|------|------------|
| GET | `/api/watched` | ✅ | Mendapatkan daftar film yang sudah ditonton |
| POST | `/api/watched` | ✅ | Menandai film sebagai sudah ditonton dengan rating dan review |

### User Info
| Method | Path | Auth | Keterangan |
|--------|------|------|------------|
| GET | `/api/user` | ✅ | Mendapatkan informasi user yang sedang login |

## Contoh Request/Response

### 1. Login
**Request:**
```json
POST /api/auth/login
{
  "email": "user@example.com",
  "password": "password123"
}
```

**Response (200):**
```json
{
  "token": "jwt_token_string",
  "user": {
    "id": 1,
    "username": "user123",
    "email": "user@example.com"
  },
  "token_type": "bearer",
  "expires_in": 3600
}
```

### 2. Menambahkan Film ke Watchlist
**Request:**
```json
POST /api/watchlist
Authorization: Bearer <token>
{
  "movie_id": 12
}
```

**Response (201):**
```json
{
  "message": "Movie added to watchlist.",
  "movie": {
    "id": 12,
    "title": "Alien",
    "year": 1979,
    "director": "Ridley Scott",
    "genres": "Sci-Fi,Horror",
    "rating": 8.5,
    "duration": "117 min",
    "synopsis": "Kru kapal kargo luar angkasa komersial Nostromo...",
    "poster_url": "https://image.tmdb.org/t/p/w500/vfrQk5IPloGg1v9Rzbh2Eg3VGyM.jpg",
    "cast_members": "Sigourney Weaver,Tom Skerritt,John Hurt,Veronica Cartwright"
  }
}
```

### 3. Menandai Film sebagai Sudah Ditonton
**Request:**
```json
POST /api/watched
Authorization: Bearer <token>
{
  "movie_id": 12,
  "rating": 4,
  "review": "Film klasik sci-fi horor yang sangat menegangkan!"
}
```

**Response (201):**
```json
{
  "id": 1,
  "user_id": 1,
  "movie_id": 12,
  "rating": 4,
  "review": "Film klasik sci-fi horor yang sangat menegangkan!",
  "created_at": "2026-11-08T05:52:18.000000Z",
  "updated_at": "2026-11-08T05:52:18.000000Z",
  "movie": {
    "id": 12,
    "title": "Alien",
    "year": 1979,
    "director": "Ridley Scott",
    "genres": "Sci-Fi,Horror",
    "rating": 8.5,
    "duration": "117 min",
    "synopsis": "Kru kapal kargo luar angkasa komersial Nostromo...",
    "poster_url": "https://image.tmdb.org/t/p/w500/vfrQk5IPloGg1v9Rzbh2Eg3VGyM.jpg",
    "cast_members": "Sigourney Weaver,Tom Skerritt,John Hurt,Veronica Cartwright"
  }
}
```

### 4. Mendapatkan Rekomendasi Film
**Request:**
```json
POST /api/movies/recommend
{
  "query": "film sci-fi tentang AI yang sadar diri"
}
```

**Response (200):**
```json
{
  "movie_id": 8,
  "reasoning": "User meminta film sci-fi tentang AI yang sadar diri. Dari katalog, film 'The Matrix' membahas realitas simulasi yang dikendalikan oleh mesin cerdas, mencakup tema kesadaran AI meskipun tidak secara eksplisit tentang AI tunggal.",
  "movie": {
    "id": 8,
    "title": "The Matrix",
    "year": 1999,
    "director": "Lana Wachowski, Lilly Wachowski",
    "genres": "Sci-Fi,Action",
    "rating": 8.7,
    "duration": "136 min",
    "synopsis": "Seorang peretas komputer bernama Neo menemukan bahwa realitas yang selama ini ia jalani hanyalah simulasi buatan mesin cerdas yang memperbudak manusia, dan ia bergabung dalam pemberontakan.",
    "poster_url": "https://image.tmdb.org/t/p/w500/f89U3ADr1oiB1s9GkdPOEpXUk5H.jpg",
    "cast_members": "Keanu Reeves,Laurence Fishburne,Carrie-Anne Moss,Hugo Weaving"
  },
  "exact_match": false
}
```

## Kode Error HTTP

| Kode | Keterangan |
|------|------------|
| 200 | Success |
| 201 | Created (resource berhasil dibuat) |
| 400 | Bad Request (query kosong di endpoint recommend) |
| 401 | Unauthenticated (token tidak valid/tidak ada) |
| 404 | Not Found (resource tidak ditemukan) |
| 409 | Conflict (duplicate entry, misal film sudah ada di watchlist) |
| 422 | Unprocessable Entity (validasi input gagal) |
| 502 | Bad Gateway (error koneksi ke Gemini AI) |

Semua error API dikembalikan dalam format JSON.

## Desain Sistem

### Database Schema

**Tabel `users`:**
- `id` (PK), `username` (unique), `email` (unique), `password`, `created_at`, `updated_at`

**Tabel `movies`:**
- `id` (PK), `title`, `year`, `director`, `genres`, `rating` (decimal 3,1), `duration`, `synopsis` (text), `poster_url` (text), `cast_members` (text), `created_at`, `updated_at`

**Tabel `watchlists`:**
- `id` (PK), `user_id` (FK ke users), `movie_id` (FK ke movies), `added_at` (timestamp), `UNIQUE(user_id, movie_id)`

**Tabel `watched`:**
- `id` (PK), `user_id` (FK ke users), `movie_id` (FK ke movies), `rating` (tinyint, nullable), `review` (text, nullable), `created_at`, `updated_at`, `UNIQUE(user_id, movie_id)`

**Relasi:**
- `users` 1:n `watchlists`
- `users` 1:n `watched`
- `movies` 1:n `watchlists`
- `movies` 1:n `watched`

### Keamanan

1. **Password Hashing**: Password disimpan dengan bcrypt (di `AuthController::register` menggunakan `Hash::make()`)
2. **Authentication**: Menggunakan JWT token dengan middleware `auth:api`
3. **Input Validation**: Semua endpoint memiliki validasi input menggunakan Laravel validation
4. **Error Handling**: Semua error dikembalikan dalam format JSON

### Google Gemini AI Grounding

Sistem rekomendasi menggunakan Google Gemini dengan pendekatan grounding:

1. **Katalog dari Database**: Semua film dari tabel `movies` di-format menjadi string teks yang berisi ID, judul, tahun, sutradara, genre, dan pemeran.
2. **System Instruction**: Model diinstruksikan untuk:
   - Memilih SATU film dari katalog yang paling sesuai permintaan user
   - Hanya menggunakan data di katalog, tidak menggunakan pengetahuan luar
   - Mengklasifikasikan `match_quality` sebagai:
     - `"exact"` jika benar-benar sesuai
     - `"closest"` jika tidak ada yang persis tapi ada yang mirip
     - `"none"` jika permintaan tidak berkaitan dengan film (dengan `movie_id: 0`)
   - Hanya menggunakan ID yang ada di katalog
3. **Response Format**: Model harus merespons dalam format JSON (`responseMimeType: "application/json"`) dengan kunci: `match_quality`, `reasoning` (bahasa Indonesia), `movie_id`
4. **Timeout**: Request ke Gemini memiliki timeout 60 detik
5. **Validasi Backend**: `movie_id` yang dikembalikan divalidasi di `RecommendationController` menggunakan `Movie::find()`
6. **Error Handling**: `GeminiRecommender` melempar exception saat Gemini gagal merespons atau output tidak valid. `RecommendationController` menangkap exception tersebut dan membalas dengan status 502

## Catatan Penting

1. **Review Opsional**: Field `review` pada endpoint `/api/watched` bersifat opsional (nullable)
2. **Rating**: Field `rating` pada endpoint `/api/watched` bersifat optional (`sometimes`, `nullable`) namun jika diberikan harus antara 1-5 (divalidasi di `WatchedController`)
3. **Duplicate Prevention**: Sistem mencegah duplikasi film di watchlist dengan constraint `UNIQUE(user_id, movie_id)` dan pengecekan di controller
4. **Data Seeder**: Seeder membaca data dari `../data/movies.json` di root repo (20 film starter pack), sehingga setup harus dijalankan dari dalam repo lengkap
5. **CORS**: API mengizinkan semua origin untuk development (`config/cors.php`)