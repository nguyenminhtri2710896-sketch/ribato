<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class SubUser extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;
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
        'city_id',
        'district_id',
        'ward_id',
        'first_name',
        'last_name',
        'company_name',
        'fullname',
        'password',
        'birthday',
        'gender',
        'email',
        'phone',
        'address',
        'actived',
        'image_avatar',
        'image_cover',
        'language',
        'full_access',
        'is_admin',
        'authy_2factor',
        'authy_2factor_secret_key',
        'email_verified_at',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        "password_sales",
        "deleted_at",
        "authy_2factor_secret_key",
        "otp_widthdraw_key",
        "withdraw_refer_user_id",
        "payment_code"
    ];
    protected $appends = [];


    public function getFillable()
    {
        return array_merge($this->fillable, array_map(function ($str) {
            return $this->getTable() . ".$str";
        }, $this->fillable));
    }

    public function getFillableParent()
    {
        return array_merge($this->fillable, array_map(function ($str) {
            return "user_parents.$str";
        }, $this->fillable));
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = trim(strtolower($value));
    }

    public function getImageAvatarAttribute($value)
    {
        if (!file_exists(base_path($this->attributes['image_avatar']))) {
            return asset("/images/avatar-default.png");
        }
        return url(!empty($value) ? "/" . $value : "/images/avatar-default.png"); // set image avatar default
    }

    public function getImageCoverAttribute($value)
    {
        if (!file_exists(base_path($this->attributes['image_cover']))) {
            return asset("/images/cover-default.png");
        }

        return url(!empty($value) ? "/" . $value : "/images/cover-default.png"); // set image avatar default
    }

    public function getFullnameAttribute($value)
    {
        return $this->attributes['first_name'] . " " . $this->attributes['last_name']; // có thể hiển thị lại theo từng ngôn ngữ
    }
}