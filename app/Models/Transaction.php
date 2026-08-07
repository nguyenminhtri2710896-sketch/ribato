<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
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
        'user_id_qrcode_id',
        'user_id_qrcode_name',
        'user_id_qrcode_code',
        'code',
        'code_hashed',
        'ref_code',
        'user_id',
        'user_email',
        'user_token_id',
        'bank_id',
        'bank_total_balance',
        'bank_account_id',
        'status_id',
        'callback_status_id',
        'callback_total_retry',
        'type_id',
        'currency',
        'exchange_rate',
        'amount',
        'fee',
        'amount_after_fee',
        'received_amount',
        'content',
        'referal_fee',
        'gateway_fee',
        'profit',
        'user_fee_json',
        'gateway_fee_json',
        'referal_fee_json',
        'bank_account_name',
        'bank_account_number',
        'bank_short_name',
        'bank_short_code',
        'bank_napas_code',
        'payment_success_url',
        'payment_cancel_url',
        'payment_ipn_url',
        'for_control_yyyymmdd',
        'for_control_yyyymmddhh',
        'for_control_at',
        'for_control_type',
        'created_at',
        'received_at',
        'expired_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */

    protected $hidden = ["referal_fee", "gateway_fee", "gateway_fee_json", "referal_fee_json", "payment_ipn_url", "bank_total_balance", "deleted_at"];
    public function getFillable()
    {
        return array_merge($this->fillable, array_map(function ($str) {
            return $this->getTable() . ".$str";
        }, $this->fillable));
    }
}