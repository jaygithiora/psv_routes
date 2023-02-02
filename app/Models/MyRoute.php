<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MyRoute extends Model
{
    protected $fillable = ["name", "description", "distance"];

    public function route_stages() {
        return $this->hasMany(RouteStage::class);
    }
}
