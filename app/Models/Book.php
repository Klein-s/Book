<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    public $fillable = ['book_number','book_name','book_author','book_img'];

    public function articles()
    {
        return $this->hasMany(Article::class, 'book_number', 'book_number');
    }
}
