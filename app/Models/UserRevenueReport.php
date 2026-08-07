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
class UserRevenueReport extends Model
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
        'total_referal_fee',
        'total_gateway_fee',
        'total_profit',
        'total_transaction_amount',
        'total_transaction_fee',
        'type_id',
        'report_yyyymmdd',
        'report_at',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['deleted_at', 'updated_at', 'created_at'];

    public function getFillable()
    {
        return array_merge($this->fillable, array_map(function ($str) {
            return $this->getTable() . ".$str";
        }, $this->fillable));
    }
}
