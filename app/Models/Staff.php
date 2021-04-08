<?php

namespace App\Models;

class Staff extends BaseModel implements IUser
{
    protected $table = "staffs";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 
        'nip',
        'code'
    ];

    public static function boot() 
    {
        parent::boot();

        static::creating(function($staff) {
            // create user
            $user = User::create([
                'username' => $staff->nip,
                'password' => $staff->nip
            ]);
            $user->save();
            $user->assignRole(Role::ROLE_STAFF);
            $staff->user()->associate($user);
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function roles()
    {
        return $this->user->getRoleNames();
    }

    private function assignRole($role)
    {
        $this->user->assignRole($role);
    }

    private function removeRole($role)
    {
        $this->user->removeRole($role);
    }

    public function assignAdmin()
    {
        $this->assignRole(Role::ROLE_ADMIN);
    }

    public function removeAdmin()
    {
        $this->removeRole(Role::ROLE_ADMIN);
    }
}

