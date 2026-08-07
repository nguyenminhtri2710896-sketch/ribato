<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserTokenRouterAccess extends Model
{
    protected $table = 'user_token_router_access';

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format("Y-m-d H:i:s");
    }

    protected $fillable = [
        'id', 'route_name', 'permission', 'created_at', 'updated_at'
    ];

    public function getFillable()
    {
        return array_merge($this->fillable, array_map(function ($str) {
            return $this->getTable() . ".$str";
        }, $this->fillable));
    }
}
