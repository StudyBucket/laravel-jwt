<?php

use App\Models\User;
use App\Models\Role;
use App\Models\Files\UserFile;

return [
    'user'          =>    User::class,
    'role'          =>    Role::class,
    'userfile'      =>    UserFile::class
];
