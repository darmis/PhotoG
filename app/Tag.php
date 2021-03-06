<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class tag extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'tag',
    ];

    public function photos()
    {
        return $this->belongsToMany(Photo::class, 'photo_tags');
    }
}
