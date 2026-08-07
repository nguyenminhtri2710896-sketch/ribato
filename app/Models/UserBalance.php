<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property string $updated_at
 * @property string $created_at
 * @property string $deleted_at
 * @property boolean $is_actived
 */
class UserBalance extends Model
{
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format("Y-m-d H:i:s");
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'user_id', 'type_id', 'balance', 'updated_at', 'created_at', 'deleted_at'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['deleted_at', 'updated_at', 'created_at', 'id'];
    protected $appends = [];

    public function getFillable()
    {
        return  array_merge($this->fillable, array_map(function ($str) {
            return $this->getTable() . ".$str";
        }, $this->fillable));
    }
}
