<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Api10000 extends Model
{
    protected $table = 'api10000s';

    protected $fillable = ['_index', '_type', '_id', '_score', '_source', 'sort'];
}
