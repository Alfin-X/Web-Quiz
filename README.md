# 🎯 Quizz - Interactive Quiz Application

<p align="center">
    <img src="https://img.shields.io/badge/Laravel-10.x-red.svg" alt="Laravel Version">
    <img src="https://img.shields.io/badge/PHP-8.1+-blue.svg" alt="PHP Version">
    <img src="https://img.shields.io/badge/License-MIT-green.svg" alt="License">
</p>

## 📖 About Quizz

Quizz is a comprehensive, interactive quiz application built with Laravel framework. It provides a complete solution for creating, managing, and taking quizzes with an intuitive admin panel and user-friendly interface.

### ✨ Key Features

- **🔐 User Authentication** - Secure login and registration system
- **👨‍💼 Admin Panel** - Complete quiz and user management
- **📝 Quiz Management** - Create, edit, and organize quizzes by categories
- **⏱️ Timed Quizzes** - Configurable time limits for each quiz
- **📊 Real-time Results** - Instant scoring and detailed analytics
- **🏆 Leaderboards** - Track top performers
- **📱 Responsive Design** - Works perfectly on all devices
- **🎨 Modern UI** - Clean, intuitive Bootstrap-based interface

## 🚀 Quick Start

### Prerequisites

- PHP 8.1 or higher
- Composer
- MySQL/MariaDB
- Node.js & NPM (for asset compilation)

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/Alfin-X/Web-Quiz.git
   cd Web-Quiz
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure database**
   Edit `.env` file with your database credentials:
   ```env
   DB_DATABASE=quizz
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

5. **Run migrations and seeders**
   ```bash
   php artisan migrate --seed
   ```

6. **Start the application**
   ```bash
   php artisan serve
   ```

Visit `http://localhost:8000` to access the application.

## 👥 Default Users

After running the seeders, you can login with:

**Admin Account:**
- Email: `admin@quiz.com`
- Password: `password`

**Test User:**
- Email: `user@quiz.com`
- Password: `password`

## 🎮 How to Use

### For Administrators
1. Login with admin credentials
2. Access the admin dashboard
3. Create categories for organizing quizzes
4. Create quizzes and add questions
5. Monitor user performance and statistics

### For Users
1. Register or login to your account
2. Browse available quizzes
3. Take quizzes within the time limit
4. View your results and compare with others
5. Track your progress over time

## 🛠️ Technology Stack

- **Backend:** Laravel 10.x
- **Frontend:** Bootstrap 5, jQuery
- **Database:** MySQL/MariaDB
- **Authentication:** Laravel Breeze
- **Styling:** Bootstrap Icons, Custom CSS

## 📁 Project Structure

```
quizz/
├── app/
│   ├── Http/Controllers/
│   │   ├── AdminController.php
│   │   ├── QuizController.php
│   │   └── UserDashboardController.php
│   └── Models/
│       ├── Quiz.php
│       ├── Question.php
│       ├── QuizAttempt.php
│       └── UserAnswer.php
├── resources/views/
│   ├── admin/
│   ├── quiz/
│   └── user/
└── database/
    ├── migrations/
    └── seeders/
```