<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;
    protected $table = "books";

    protected $fillable = [
        'id',
        'isbn',
        'title',
        'description',
        'published_date',
        'category_id',
        'editorial_id'
    ];

    public $timestamps = false;

    public function category(){
        return $this->belongsTo(Category::class,'category_id','id');
    }
    public function editorial(){
        return $this->belongsTo(Editorial::class,'editorial_id','id');
     }
    public function authors(){
        return $this->belongsToMany(
           Author::class,// table relationship
           'authors_books',//table pivot o insertion
           'books_id',//from
            'authors_id'//to
        );
      }
    public function bookDownload(){
        return $this->hasOne(BookDownload::class);
    }
        
}
