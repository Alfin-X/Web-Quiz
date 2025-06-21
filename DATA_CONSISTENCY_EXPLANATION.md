# 📊 Data Consistency: Admin vs Guru

## 🎯 **Konsep Utama**

**Data yang sama, akses yang berbeda!**

Admin dan Guru mengakses **database yang sama** dengan **tabel yang sama**, tetapi dengan **filtering yang berbeda** berdasarkan role dan ownership.

---

## 🗄️ **Database Tables (Sama untuk Semua)**

```sql
users           - Semua user (admin, guru, murid)
categories      - Semua kategori quiz
quizzes         - Semua quiz (dengan created_by untuk ownership)
questions       - Semua pertanyaan
question_options - Semua opsi jawaban
quiz_attempts   - Semua attempt quiz
user_answers    - Semua jawaban user
```

---

## 🔐 **Access Control Logic**

### **Admin (Full System Access):**
```php
// Admin melihat SEMUA data
$allQuizzes = Quiz::all();
$allAttempts = QuizAttempt::all();
$allUsers = User::all();
```

### **Guru (Filtered Access):**
```php
// Guru hanya melihat quiz yang mereka buat
$myQuizzes = Quiz::where('created_by', auth()->id());
$myQuizAttempts = QuizAttempt::whereHas('quiz', function($q) {
    $q->where('created_by', auth()->id());
});
```

### **Murid (Read-Only Access):**
```php
// Murid hanya bisa mengakses quiz aktif dan melihat leaderboard
$activeQuizzes = Quiz::where('is_active', true);
$publicLeaderboard = QuizAttempt::whereNotNull('completed_at');
```

---

## 📈 **Data Consistency Examples**

### **1. Quiz Statistics**

#### **Admin Dashboard:**
```php
'total_quizzes' => Quiz::count(),                    // SEMUA quiz
'total_attempts' => QuizAttempt::count(),            // SEMUA attempts
'total_users' => User::where('role', 'user')->count() // SEMUA murid
```

#### **Guru Dashboard:**
```php
'total_quizzes' => Quiz::where('created_by', auth()->id())->count(),  // Quiz GURU ini
'total_attempts' => QuizAttempt::whereHas('quiz', function($q) {      // Attempts quiz GURU ini
    $q->where('created_by', auth()->id());
})->count(),
'total_students' => User::where('role', 'user')->count()             // SEMUA murid (sama)
```

### **2. Leaderboard Data**

#### **Admin Leaderboard:**
```php
// Melihat ranking dari SEMUA quiz
$leaderboard = QuizAttempt::with(['user', 'quiz'])
    ->whereNotNull('completed_at')
    ->orderBy('score', 'desc');
```

#### **Guru Leaderboard:**
```php
// Melihat ranking hanya dari quiz GURU ini
$leaderboard = QuizAttempt::with(['user', 'quiz'])
    ->whereNotNull('completed_at')
    ->whereHas('quiz', function($q) {
        $q->where('created_by', auth()->id());
    })
    ->orderBy('score', 'desc');
```

#### **Murid Leaderboard:**
```php
// Melihat ranking dari SEMUA quiz (public)
$leaderboard = QuizAttempt::with(['user', 'quiz'])
    ->whereNotNull('completed_at')
    ->orderBy('score', 'desc');
```

---

## 🎯 **Key Points**

### **✅ Yang SAMA:**
1. **Database Structure** - Tabel dan kolom yang sama
2. **Data Format** - Format data yang konsisten
3. **Calculation Logic** - Cara hitung skor, ranking, dll
4. **UI Components** - Tampilan dan styling yang sama
5. **Business Rules** - Aturan quiz, scoring, dll

### **🔄 Yang BERBEDA:**
1. **Data Scope** - Admin (all), Guru (own), Murid (public)
2. **Access Control** - Permission berdasarkan role
3. **Filtering** - WHERE clause yang berbeda
4. **Actions Available** - CRUD permissions yang berbeda

---

## 📊 **Contoh Konkret: Quiz "Matematika Dasar"**

### **Scenario:**
- **Guru A** membuat quiz "Matematika Dasar"
- **Murid 1, 2, 3** mengerjakan quiz tersebut
- **Admin** ingin melihat overview system

### **Data di Database:**
```sql
quizzes:
id=1, title="Matematika Dasar", created_by=2 (Guru A)

quiz_attempts:
id=1, quiz_id=1, user_id=3 (Murid 1), score=85
id=2, quiz_id=1, user_id=4 (Murid 2), score=92  
id=3, quiz_id=1, user_id=5 (Murid 3), score=78
```

### **Yang Dilihat Admin:**
- **Dashboard**: Total 1 quiz, 3 attempts
- **Leaderboard**: Murid 2 (92%), Murid 1 (85%), Murid 3 (78%)
- **Analytics**: Quiz "Matematika Dasar" popular

### **Yang Dilihat Guru A:**
- **Dashboard**: Total 1 quiz (milik saya), 3 attempts
- **Leaderboard**: Murid 2 (92%), Murid 1 (85%), Murid 3 (78%)
- **Analytics**: Quiz "Matematika Dasar" saya popular

### **Yang Dilihat Guru B:**
- **Dashboard**: Total 0 quiz (tidak ada milik saya), 0 attempts
- **Leaderboard**: Kosong (tidak ada quiz saya)
- **Analytics**: Tidak ada data

### **Yang Dilihat Murid:**
- **Dashboard**: Quiz "Matematika Dasar" tersedia
- **Leaderboard**: Murid 2 (92%), Murid 1 (85%), Murid 3 (78%)
- **My Results**: Hasil quiz saya saja

---

## 🔧 **Implementation Pattern**

### **Controller Pattern:**
```php
class AdminController {
    public function leaderboard() {
        // No filtering - see all data
        $data = QuizAttempt::with(['user', 'quiz'])->get();
    }
}

class GuruController {
    public function leaderboard() {
        // Filter by created_by
        $data = QuizAttempt::with(['user', 'quiz'])
            ->whereHas('quiz', function($q) {
                $q->where('created_by', auth()->id());
            })->get();
    }
}

class UserDashboardController {
    public function leaderboard() {
        // Public data only
        $data = QuizAttempt::with(['user', 'quiz'])
            ->whereHas('quiz', function($q) {
                $q->where('is_active', true);
            })->get();
    }
}
```

---

## ✅ **Verification Checklist**

### **Data Consistency:**
- [x] Same database tables
- [x] Same data structure
- [x] Same calculation logic
- [x] Same UI components

### **Access Control:**
- [x] Admin sees all data
- [x] Guru sees only their quiz data
- [x] Murid sees public data
- [x] Proper filtering implemented

### **Business Logic:**
- [x] Scoring system consistent
- [x] Ranking algorithm same
- [x] Performance calculation same
- [x] Time tracking consistent

---

## 🎯 **Summary**

**Admin dan Guru menggunakan data yang SAMA dari database yang SAMA, tetapi dengan SCOPE AKSES yang berbeda.**

- **Admin**: Full system view
- **Guru**: Own quiz view  
- **Murid**: Public view

**Tidak ada duplikasi data, hanya filtering yang berbeda!** 🎉
