<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Lumen\Auth\Authorizable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Hash;

class User extends BaseModel implements AuthenticatableContract, AuthorizableContract, JWTSubject
{
    use Authenticatable, Authorizable, HasFactory, HasRoles;

    public static function boot()
    {
        parent::boot();

        static::creating(function($user) {
            $user->password = Hash::make($user->password);
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'password',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

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

    public function staff()
    {
        return $this->hasOne(Staff::class);
    }

    public function student()
    {
        return $this->hasOne(Student::class);
    }

    public function isLaboran()
    {
        return $this->hasRole(Role::ROLE_LABORAN);
    }

    public function isStaff()
    {
        return $this->hasRole(Role::ROLE_STAFF);
    }

    public function isStudent()
    {
        return $this->hasRole(Role::ROLE_STUDENT);
    }

    public function isAsprak()
    {
        return $this->hasRole(Role::ROLE_ASPRAK);
    }

    public function isAslab()
    {
        return $this->hasRole(Role::ROLE_ASLAB);
    }

    public function isDosen()
    {
        return $this->hasRole(Role::ROLE_DOSEN);
    }

    public function user()
    {
        if($this->isStaff()) {
            return $this->staff;
        } else {
            return $this->student;
        }
    }
}
