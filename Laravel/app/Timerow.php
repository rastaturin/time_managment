<?php namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Timerow
 * @method static Timerow find
 * @property int id
 * @property int duration
 * @property string note
 * @property int date
 * @property User user
 */
class Timerow extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'timerows';

    protected $fillable = ['date', 'duration', 'user_id', 'note'];
    protected $hidden = ['created_at', 'updated_at'];

    protected $appends = ['isGreen', 'isRed'];
    protected $curUser;

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function getIsGreenAttribute()
    {
        return $this->user->hours && $this->user->dateOk($this->date);
    }

    public function getIsRedAttribute()
    {
        return $this->user->hours && !$this->user->dateOk($this->date);
    }

    public function scopeDates($query, $from, $till)
    {
        if ($from) {
            $query = $query->where('date', '>=', $from);
        }
        if ($till) {
            $query = $query->where('date', '<=', $till);
        }
        return $query;
    }


}
