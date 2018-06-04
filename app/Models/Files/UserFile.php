<?php

namespace App\Models\Files;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

use App\Models\User;


class UserFile extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'type', 'description', 'name', 'extension'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        // 'tags' => 'array',
    ];

    /**
     * Mime types for userfiles
     *
     * @var array
     */
    private $mimes = [
        'image'     => ['jpg', 'jpeg', 'png', 'gif'],
        'audio'     => ['mp3', 'ogg', 'mpga'],
        'video'     => ['mp4', 'mpeg'],
        'document'  => ['doc', 'docx', 'pdf', 'odt', 'md', 'rtf', 'txt'],
        'archive'   => ['zip', 'rar']
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
        return $this->belongsTo(User::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Custom functions
    |--------------------------------------------------------------------------
    |
    | Here is where you can implement custom functions for this model.
    |
    */

    public function getMimesAsString() {
        $mimesString = '';
        foreach ($this->mimes as $type => $associatedMimes) {
          foreach ($associatedMimes as $mime) {
            $mimesString = $mimesString . $mime . ',';
          }
        }
        return $mimesString;
    }

    public function getTypeByExtension(string $ext){
        foreach ($this->mimes as $type => $associatedMimes) {
          if (in_array($ext, $associatedMimes)) {
              return $type;
          }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Validation Rules
    |--------------------------------------------------------------------------
    |
    | Here is where you can register validation rules for this model.
    |
    */

    /**
     * Returns models validation rules
     *
     * @param usage - determins the request type
     */
    public function modelRules($usage)
    {
        $rules = [
            'store' => [
                'file'        => 'required|file|mimes:' . $this->getMimesAsString(),
                'name'        => 'required|string|unique:user_files,name',
                'description' => 'sometimes|string|min:3',
            ],
            'update' => [
                'file'        => 'sometimes|file|',
                'name'        => 'sometimes|string|unique:user_files,name,'.$this->id.',id',
                'description' => 'sometimes|string|min:3',
            ]
        ];
        return $rules[$usage];
    }
}
