<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = array ('name');

    protected $table = 'tag';


    // public function blog() {
    //     return $this->belongsToOne('\Model\Blog', 'blog');
    // }

}