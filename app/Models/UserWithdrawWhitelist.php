<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserWithdrawWhitelist extends Model
{
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format("Y-m-d H:i:s");
    }

    protected $fillable = [
        'id',
        'user_id',
        'bank_id',
        'bank_account_number',
        'bank_account_name',
        'note',
        'created_at',
        'updated_at',
    ];

    public function getFillable()
    {
        return array_merge($this->fillable, array_map(function ($str) {
            return $this->getTable() . ".$str";
        }, $this->fillable));
    }

    public static function isWhitelisted($intUserId, $intBankId, $strBankAccountNumber): bool
    {
        return self::where([
            'user_id' => $intUserId,
            'bank_id' => $intBankId,
            'bank_account_number' => $strBankAccountNumber,
        ])->exists();
    }
}
