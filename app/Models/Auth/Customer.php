<?php

namespace App\Models\Auth;


use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Customer extends Authenticatable implements JWTSubject
{
    use Notifiable;
    protected $fillable = ['email', 'password', 'openid', 'unionid', 'nickname', 'mobile',
        'leaderid', 'avatar', 'country', 'province', 'city', 'gender'];

    public function leader()
    {
        return $this->hasOne('App\Models\Common\Leader', 'customerid');
    }

    public function community()
    {
        return $this->belongsTo('App\Models\Common\Community', 'commid');
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
}