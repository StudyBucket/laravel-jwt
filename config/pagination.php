<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Pagination treshold
    |--------------------------------------------------------------------------
    |
    | This value is the number of total items required to start in pagination mode.
    |
    | NOT IN USE!!!
    |
    | Possible usage:
    |
    | if(User::count() >= Config::get('pagination.paginationThreshold')){
    |     $response = User::paginate(Config::get('pagination.itemsPerPage'))->appends('paged', $request->input('paged'));
    | } else {
    |     $response = User::all();
    | }
    |
    */

    // 'paginationThreshold' => 25,

    /*
    |--------------------------------------------------------------------------
    | Pagination items per page
    |--------------------------------------------------------------------------
    |
    | This value is the number item shown per page in pagination mode.
    |
    */

    'itemsPerPage' => 15,

];
