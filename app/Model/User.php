<?php

namespace App\Model;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $username
 * @property string $email
 * @property string $account
 * @property string $ip
 * @property string $corp
 * @property string $code
 * @property string $serno
 * @property string $password
 */
class User extends Model implements AuthenticatableContract, CanResetPasswordContract
{
    const USER_DEFAULT_PASSWORD = '1111';

    use Authenticatable, CanResetPassword, SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    protected $connection = 'mysql';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['username', 'email', 'password', 'ip', 'ext', 'account', 'corp'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * A user can have many articles
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function articles()
    {
        return $this->hasMany('App\Model\Article');
    }

    public function setProfileByErpRow(array $row)
    {
        $this->username = $row['UName'];
        $this->email    = "{$row['Code']}@" . env('DOMAIN');
        $this->account  = $row['Code'];
        $this->ip       = NULL;
        $this->corp     = $row['CName'];
        $this->code     = $row['HCode'];
        $this->serno    = $row['HSerNo'];
        $this->password = bcrypt(self::USER_DEFAULT_PASSWORD);

        return $this;
    }
}
