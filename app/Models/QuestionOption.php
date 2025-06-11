<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'option_text',
        'is_correct',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    /**
     * Relationship with question
     */
    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * Relationship with user answers
     */
    public function userAnswers()
    {
        return $this->hasMany(UserAnswer::class, 'selected_option_id');
    }
}
