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
class GatewayAccount extends Model
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
        'gateway_id',
        'name',
        'username',
        'password',
        'payout_pin',
        'tenant',
        'status_id',
        'private_key',
        'public_key',
        'gateway_public_key',
        'secret_key',
        'access_token',
        'merchant_id',
        'business_id',
        'balance',
        'pending_balance',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['username', 'password', 'tenant', 'secret_key', 'access_token', 'payout_pin', 'private_key', 'public_key', 'gateway_public_key', 'deleted_at'];

    public function getFillable()
    {
        return array_merge($this->fillable, array_map(function ($str) {
            return $this->getTable() . ".$str";
        }, $this->fillable));
    }
}
