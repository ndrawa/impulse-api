<?php

namespace App\Models;

class Student extends BaseModel implements IUser
{
    protected $table = "students";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'nim',
        'gender',
        'religion'
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function($student) {
            // create user
            $user = User::create([
                'username' => $student->nim,
                'password' => $student->nim
            ]);
            $user->save();
            $user->assignRole(Role::ROLE_STUDENT);
            $student->user()->associate($user);
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

    public function assignAsprak()
    {
        $this->assignRole(Role::ROLE_ASPRAK);
    }

    public function removeAsprak()
    {
        $this->removeRole(Role::ROLE_ASPRAK);
    }

    public function assignAslab()
    {
        $this->assignRole(Role::ROLE_ASLAB);
    }

    public function removeAslab()
    {
        $this->removeRole(Role::ROLE_ASLAB);
    }
}
