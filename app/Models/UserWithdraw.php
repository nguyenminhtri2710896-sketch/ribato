<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserWithdraw extends Model
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
        'gateway_id',
        'gateway_account_id',
        'user_email',
        'bank_id',
        'user_transaction_id',
        'ref_code',
        'trans_code',
        'bank_short_name',
        'bank_account_number',
        'bank_account_name',
        'remark',
        'amount',
        'note',
        'fee',
        'fee_estimate',
        'amount_after_fee',
        'amount_estimate_after_fee',
        'referal_fee',
        'gateway_fee',
        'amount_gateway_after_fee',
        'profit',
        'profit_estimate',
        'user_fee_json',
        'user_fee_estimate_json',
        'gateway_fee_json',
        'referal_fee_json',
        'status_id',
        'type_id',
        'is_tranfer_local',
        'sorting',
        'callback_status_id',
        'callback_total_retry',
        'approved_id',
        'is_show',
        'platform',
        'approved_at',
        'partner_process_id',
        'partner_hash_code',
        'partner_auth_code',
        'partner_transaction_id',
        'partner_transaction_amount',
        'partner_transaction_image',
        'partner_transaction_status_id',
        'partner_transaction_cancel_reason',
        'gateway_account_transaction_id',
        'gateway_account_transaction_amount',
        'created_at',
        'updated_at',
        'deleted_at'
    ];


    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */

    protected $hidden = ["partner_transaction_id", "partner_auth_code", "partner_hash_code", "referal_fee", "gateway_fee", "gateway_fee_json", "referal_fee_json", "deleted_at"];

    public function getFillable()
    {
        return array_merge($this->fillable, array_map(function ($str) {
            return $this->getTable() . ".$str";
        }, $this->fillable));
    }

}