<?php

class StaticPermissions
{
    private static $modelPermissions = [
       App\Models\User::class => [
           'global-user'   => 'Full permissions on users',
           'create-user'   => 'Create a new user.',
           'update-user'   => 'Update a forreign user.',
           'delete-user'   => 'Delete a forreign user.',
       ],
       App\Models\Role::class => [
           'global-role'   => 'Full permissions on roles',
       ],
       App\Models\Auth\DeviceLogin::class => [
            // no permissions specified
       ],
       App\Models\Files\UserFile::class => [
           'global-userfile'   => 'Full permissions on roles',
       ],
    ];

    public static function getPermissionsOnModel($model)
    {
        if(array_has(StaticPermissions::$modelPermissions, $model)){
          return StaticPermissions::$modelPermissions[$model];
        } else {
          throw new Exception("Model not specified or unknown.");
        }
    }

    //  possible Outputs
    //
    //  StaticPermissions::getPermissionsOnModel();
    //  e.g.:
    //  StaticPermissions::getPermissionsOnModel(App\Models\User::class);
}
