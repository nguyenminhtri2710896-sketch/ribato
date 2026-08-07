<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserVirtualAccount extends Model
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
        'order_no',
        'gateway_id',
        'gateway_account_id',
        'bank_account_name',
        'bank_account_number',
        'bank_id',
        'bank_short_name',
        'bank_short_code',
        'status_id',
        'ipn_collection',
        'ipn_payout',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */

    protected $hidden = ["ipn_collection", "ipn_payout", "deleted_at"];
    public function getFillable()
    {
        return array_merge($this->fillable, array_map(function ($str) {
            return $this->getTable() . ".$str";
        }, $this->fillable));
    }
}