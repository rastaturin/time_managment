<?php namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Class User
 * @method static User find
 * @property int id
 * @property int name
 * @property int email
 * @property int hours
 */
class User extends Model implements AuthenticatableContract, CanResetPasswordContract
{

    use Authenticatable, CanResetPassword;

    const USER = 0;
    const MANAGER = 1;
    const ADMIN = 2;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password', 'role', 'hours'];

    /**
     * @var self
     */
    public static $logged;

    /**
     * @var self
     */
    public static $edited;

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token', 'created_at', 'updated_at', 'api_token'];
    protected $appends = ['duration'];

    protected $dates = [];

    public function setPasswordAttribute($pass)
    {
        $this->attributes['password'] = Hash::make($pass);
    }

    public static function getByToken($token)
    {
        return User::firstByAttributes(['token' => $token]);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function timerows()
    {
        return $this->hasMany('App\Timerow');
    }

    public function getDurationAttribute()
    {
        return $this->sumDuration('');
    }

    public function dateOk($date)
    {
        return $this->sumDuration($date) >= $this->hours;
    }

    protected function sumDuration($date)
    {
        return DB::table('timerows')
            ->where('user_id', '=', $this->id)
            ->where('date', '=', $date)
            ->sum('duration');
    }

    public function isManager()
    {
        return $this->role == self::MANAGER || $this->isAdmin();
    }

    public function isAdmin()
    {
        return $this->role == self::ADMIN;
    }

    /**
     * @param $email
     * @return self
     */
    public static function byEmail($email)
    {
        return self::where(['email' => $email])->first();
    }

}
