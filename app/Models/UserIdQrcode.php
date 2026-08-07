<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class UserIdQrcode extends Model
{
    use SoftDeletes;
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
        'name',
        'user_id',
        'bank_id',
        'bank_account_name',
        'bank_account_number',
        'path_qrcode',
        'code',
        'note',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */

    protected $hidden = [];
    public function getFillable()
    {
        return array_merge($this->fillable, array_map(function ($str) {
            return $this->getTable() . ".$str";
        }, $this->fillable));
    }
}
