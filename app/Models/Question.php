<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'question_text',
        'question_type',
        'order',
    ];

    /**
     * Relationship with quiz
     */
    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    /**
     * Relationship with question options
     */
    public function options()
    {
        return $this->hasMany(QuestionOption::class);
    }

    /**
     * Get correct option
     */
    public function correctOption()
    {
        return $this->hasOne(QuestionOption::class)->where('is_correct', true);
    }

    /**
     * Relationship with user answers
     */
    public function userAnswers()
    {
        return $this->hasMany(UserAnswer::class);
    }
}
