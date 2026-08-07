<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserFee extends Model
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
        'id',
        'user_id',
        'name',
        'status_id',
        'type_id',
        'fee',
        'fee_estimate',
        'individual_fee',
        'min_fee',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */

    protected $hidden = ["deleted_at"];
    public function getFillable()
    {
        return array_merge($this->fillable, array_map(function ($str) {
            return $this->getTable() . ".$str";
        }, $this->fillable));
    }
}