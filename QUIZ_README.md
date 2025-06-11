# Quiz App - Sistem Kuis Interaktif

Aplikasi web kuis interaktif yang mirip dengan Quizizz, dibangun menggunakan Laravel 10 dengan fitur lengkap untuk admin dan user.

## 🚀 Fitur Utama

### Sistem Autentikasi
- ✅ Registrasi dan login untuk Admin dan User
- ✅ Role-based authentication
- ✅ Password reset functionality

### Dashboard Admin
- ✅ Manajemen kuis (CRUD)
- ✅ Manajemen kategori kuis
- ✅ Manajemen pertanyaan dan opsi jawaban
- ✅ Statistik dan analytics
- ✅ Export hasil kuis ke CSV
- ✅ Dashboard dengan overview lengkap

### Dashboard User
- ✅ Daftar kuis tersedia dengan pencarian dan filter
- ✅ Riwayat kuis yang pernah dikerjakan
- ✅ Statistik performa pribadi
- ✅ Leaderboard untuk setiap kuis

### Sistem Kuis
- ✅ Timer untuk setiap kuis
- ✅ Tampilan pertanyaan satu per satu
- ✅ Navigasi antar pertanyaan
- ✅ Auto-save jawaban
- ✅ Skor real-time
- ✅ Hasil akhir dengan review jawaban
- ✅ Leaderboard

## 🛠️ Teknologi yang Digunakan

- **Backend**: Laravel 10
- **Database**: MySQL
- **Frontend**: Bootstrap 5
- **JavaScript**: jQuery untuk AJAX
- **Icons**: Bootstrap Icons

## 📋 Persyaratan Sistem

- PHP >= 8.1
- Composer
- MySQL >= 5.7
- Node.js & NPM (untuk asset compilation)

## 🔧 Instalasi dan Setup

### 1. Clone Repository
```bash
git clone <repository-url>
cd quiz-app
```

### 2. Install Dependencies
```bash
composer install
npm install
```

### 3. Environment Setup
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Database Configuration
Edit file `.env` dan sesuaikan konfigurasi database:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=quiz_app
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Create Database
Buat database MySQL dengan nama `quiz_app`

### 6. Run Migrations
```bash
php artisan migrate
```

### 7. Seed Database
```bash
php artisan db:seed --class=AdminUserSeeder
php artisan db:seed --class=SampleQuizSeeder
```

### 8. Compile Assets (Optional)
```bash
npm run dev
```

### 9. Start Server
```bash
php artisan serve
```

Aplikasi akan berjalan di `http://127.0.0.1:8000`

## 👥 Default Users

Setelah menjalankan seeder, Anda dapat login dengan:

### Admin
- **Email**: admin@quiz.com
- **Password**: password

### User
- **Email**: user@quiz.com
- **Password**: password

## 📖 Cara Penggunaan

### Untuk Admin

1. **Login** sebagai admin
2. **Dashboard Admin** - Lihat statistik keseluruhan
3. **Manage Categories** - Buat dan kelola kategori kuis
4. **Manage Quizzes** - Buat kuis baru atau edit yang sudah ada
5. **Add Questions** - Tambahkan pertanyaan dengan multiple choice
6. **View Statistics** - Lihat analytics dan performa user
7. **Export Results** - Download hasil kuis dalam format CSV

### Untuk User

1. **Register/Login** sebagai user
2. **Dashboard** - Lihat kuis yang tersedia
3. **Search & Filter** - Cari kuis berdasarkan judul atau kategori
4. **Take Quiz** - Klik "Take Quiz" untuk memulai
5. **Quiz Interface** - Jawab pertanyaan dengan timer berjalan
6. **View Results** - Lihat hasil dan review jawaban
7. **My Results** - Lihat riwayat semua kuis yang pernah dikerjakan

## 🎯 Fitur Detail

### Timer System
- Timer countdown real-time
- Auto-submit ketika waktu habis
- Visual progress bar

### Question Navigation
- Navigasi antar pertanyaan
- Status indicator (answered/unanswered)
- Previous/Next buttons

### Real-time Features
- Auto-save jawaban menggunakan AJAX
- Real-time timer update
- Instant feedback

### Responsive Design
- Mobile-friendly interface
- Bootstrap 5 responsive grid
- Touch-friendly buttons

### Security Features
- CSRF protection
- Role-based access control
- Input validation
- SQL injection prevention

## 📊 Database Schema

### Tables
- `users` - Data pengguna dengan role
- `categories` - Kategori kuis
- `quizzes` - Data kuis
- `questions` - Pertanyaan kuis
- `question_options` - Opsi jawaban
- `quiz_attempts` - Percobaan mengerjakan kuis
- `user_answers` - Jawaban user

### Relationships
- User hasMany QuizAttempts
- Quiz belongsTo Category
- Quiz hasMany Questions
- Question hasMany QuestionOptions
- QuizAttempt hasMany UserAnswers

## 🔍 API Endpoints

### AJAX Endpoints
- `POST /api/quiz/{quiz}/answer` - Save user answer
- `GET /api/quiz/{quiz}/question/{question}` - Get question data
- `GET /api/quiz/{quiz}/time-remaining/{attempt}` - Get remaining time

## 🎨 Customization

### Styling
- Edit `resources/views/layouts/app.blade.php` untuk layout utama
- Customize CSS di bagian `<style>` dalam layout
- Gunakan Bootstrap classes untuk styling

### Timer Settings
- Edit `time_limit` di tabel `quizzes` (dalam menit)
- Modify timer logic di `quiz/take.blade.php`

### Scoring System
- Default: Percentage based (correct/total * 100)
- Modify di `QuizController::completeQuiz()`

## 🐛 Troubleshooting

### Common Issues

1. **Database Connection Error**
   - Pastikan MySQL service berjalan
   - Check konfigurasi `.env`
   - Pastikan database sudah dibuat

2. **Permission Errors**
   - Set permission untuk storage dan bootstrap/cache
   ```bash
   chmod -R 775 storage bootstrap/cache
   ```

3. **Asset Not Loading**
   - Run `npm run dev` untuk compile assets
   - Check file permissions

4. **Timer Not Working**
   - Pastikan JavaScript enabled
   - Check browser console untuk errors

## 📝 Development Notes

### Code Structure
- Controllers di `app/Http/Controllers/`
- Models di `app/Models/`
- Views di `resources/views/`
- Routes di `routes/web.php`

### Key Files
- `AdminController.php` - Admin functionality
- `QuizController.php` - Quiz taking logic
- `UserDashboardController.php` - User dashboard
- `AdminMiddleware.php` - Role-based access

## 🤝 Contributing

1. Fork repository
2. Create feature branch
3. Commit changes
4. Push to branch
5. Create Pull Request

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 📞 Support

Untuk pertanyaan atau bantuan, silakan buat issue di repository ini.

---

**Happy Quizzing! 🎉**
