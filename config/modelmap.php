<?php

use App\Models\User;
use App\Models\Role;
use App\Models\Files\UserFile;
use App\Models\Auth\DeviceLogin;

return [
    'user'          =>    User::class,
    'role'          =>    Role::class,
    'devicelogin'   =>    DeviceLogin::class,
    'userfile'      =>    UserFile::class
];
