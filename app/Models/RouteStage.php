<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RouteStage extends Model
{
    protected $fillable = ["stage_id", "my_route_id", "status"];

    public function stage() {
        return $this->belongsTo(Stage::class);
    }
    public function my_route() {
        return $this->belongsTo(MyRoute::class);
    }
}
