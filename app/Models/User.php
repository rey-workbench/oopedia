<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role_id'];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function quizAttempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function studentState()
    {
        return $this->hasOne(StudentState::class);
    }

    public function answeredQuestions()
    {
        return $this->hasMany(QuizAttempt::class)->where('score', '>', 0); // Or distinct question_id
    }

    public function hasRole($role)
    {
        return $this->role->role_name === $role;
    }
}