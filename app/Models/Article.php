<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    public $table = 'articles';

    public $fillable = ['book_number','article_content','article_title'];
    public function book()
    {
        return $this->belongsTo(Book::class,'book_number', 'book_number');
    }

}
