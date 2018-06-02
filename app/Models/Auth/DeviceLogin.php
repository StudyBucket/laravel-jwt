<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Encryptable;

class DeviceLogin extends Model
{
    use Encryptable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'token', 'agent'
    ];

    /**
     * The attributes that will be encrypted.
     *
     * @var array
     */

    protected $encryptable = [
        'token', 'agent'
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    |
    | Here is where you can specify relationships for this model.
    |
    */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
