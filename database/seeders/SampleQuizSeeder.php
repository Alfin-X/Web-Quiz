<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Category;
use App\Models\User;

class SampleQuizSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        $mathCategory = Category::where('name', 'Matematika')->first();

        if (!$admin || !$mathCategory) {
            $this->command->error('Please run AdminUserSeeder first');
            return;
        }

        // Create sample quiz
        $quiz = Quiz::create([
            'title' => 'Matematika Dasar',
            'description' => 'Kuis tentang operasi matematika dasar seperti penjumlahan, pengurangan, perkalian, dan pembagian.',
            'category_id' => $mathCategory->id,
            'time_limit' => 10, // 10 minutes
            'created_by' => $admin->id,
            'is_active' => true,
        ]);

        // Question 1
        $question1 = Question::create([
            'quiz_id' => $quiz->id,
            'question_text' => 'Berapakah hasil dari 15 + 27?',
            'order' => 1,
        ]);

        QuestionOption::create([
            'question_id' => $question1->id,
            'option_text' => '40',
            'is_correct' => false,
        ]);

        QuestionOption::create([
            'question_id' => $question1->id,
            'option_text' => '42',
            'is_correct' => true,
        ]);

        QuestionOption::create([
            'question_id' => $question1->id,
            'option_text' => '44',
            'is_correct' => false,
        ]);

        QuestionOption::create([
            'question_id' => $question1->id,
            'option_text' => '45',
            'is_correct' => false,
        ]);

        // Question 2
        $question2 = Question::create([
            'quiz_id' => $quiz->id,
            'question_text' => 'Berapakah hasil dari 8 × 7?',
            'order' => 2,
        ]);

        QuestionOption::create([
            'question_id' => $question2->id,
            'option_text' => '54',
            'is_correct' => false,
        ]);

        QuestionOption::create([
            'question_id' => $question2->id,
            'option_text' => '56',
            'is_correct' => true,
        ]);

        QuestionOption::create([
            'question_id' => $question2->id,
            'option_text' => '58',
            'is_correct' => false,
        ]);

        QuestionOption::create([
            'question_id' => $question2->id,
            'option_text' => '60',
            'is_correct' => false,
        ]);

        // Question 3
        $question3 = Question::create([
            'quiz_id' => $quiz->id,
            'question_text' => 'Berapakah hasil dari 144 ÷ 12?',
            'order' => 3,
        ]);

        QuestionOption::create([
            'question_id' => $question3->id,
            'option_text' => '10',
            'is_correct' => false,
        ]);

        QuestionOption::create([
            'question_id' => $question3->id,
            'option_text' => '11',
            'is_correct' => false,
        ]);

        QuestionOption::create([
            'question_id' => $question3->id,
            'option_text' => '12',
            'is_correct' => true,
        ]);

        QuestionOption::create([
            'question_id' => $question3->id,
            'option_text' => '13',
            'is_correct' => false,
        ]);

        // Question 4
        $question4 = Question::create([
            'quiz_id' => $quiz->id,
            'question_text' => 'Berapakah hasil dari 50 - 23?',
            'order' => 4,
        ]);

        QuestionOption::create([
            'question_id' => $question4->id,
            'option_text' => '25',
            'is_correct' => false,
        ]);

        QuestionOption::create([
            'question_id' => $question4->id,
            'option_text' => '26',
            'is_correct' => false,
        ]);

        QuestionOption::create([
            'question_id' => $question4->id,
            'option_text' => '27',
            'is_correct' => true,
        ]);

        QuestionOption::create([
            'question_id' => $question4->id,
            'option_text' => '28',
            'is_correct' => false,
        ]);

        // Question 5
        $question5 = Question::create([
            'quiz_id' => $quiz->id,
            'question_text' => 'Manakah yang merupakan bilangan prima?',
            'order' => 5,
        ]);

        QuestionOption::create([
            'question_id' => $question5->id,
            'option_text' => '15',
            'is_correct' => false,
        ]);

        QuestionOption::create([
            'question_id' => $question5->id,
            'option_text' => '17',
            'is_correct' => true,
        ]);

        QuestionOption::create([
            'question_id' => $question5->id,
            'option_text' => '21',
            'is_correct' => false,
        ]);

        QuestionOption::create([
            'question_id' => $question5->id,
            'option_text' => '25',
            'is_correct' => false,
        ]);

        $this->command->info('Sample quiz created successfully!');
    }
}
