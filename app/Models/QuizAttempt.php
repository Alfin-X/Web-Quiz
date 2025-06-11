<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'quiz_id',
        'score',
        'total_questions',
        'correct_answers',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Relationship with user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship with quiz
     */
    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    /**
     * Relationship with user answers
     */
    public function userAnswers()
    {
        return $this->hasMany(UserAnswer::class, 'attempt_id');
    }

    /**
     * Check if attempt is completed
     */
    public function isCompleted()
    {
        return !is_null($this->completed_at);
    }

    /**
     * Get percentage score
     */
    public function getPercentageAttribute()
    {
        return $this->total_questions > 0 ? round(($this->correct_answers / $this->total_questions) * 100, 2) : 0;
    }

    /**
     * Get duration in minutes
     */
    public function getDurationAttribute()
    {
        if ($this->completed_at && $this->started_at) {
            return $this->started_at->diffInMinutes($this->completed_at);
        }
        return null;
    }
}
