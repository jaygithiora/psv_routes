<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MyRoute extends Model
{
    protected $fillable = ["name", "description"];

    public function stages() {
        return $this->hasMany(RouteStage::class);
    }
}
