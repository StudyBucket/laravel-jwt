<?php

namespace App\Http\Controllers\Api\Files;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Config;

use App\Http\Requests\Files\StoreUserFileRequest;
use App\Http\Requests\Files\UpdateUserFileRequest;

use App\Models\Files\UserFile;
use App\Models\User;
// use App\Jobs\User\StoreUser;
// use App\Jobs\User\UpdateUser;
// use App\Jobs\User\DestroyUser;

// use App\Http\Resources\User\UserResource;

class UserFileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        if (Auth::user()->can('index', UserFile::class)) {
          $response = UserFile::paginate(Config::get('pagination.itemsPerPage'))
          //$response = UserResource::collection($response)
                        ->appends('paged', $request->input('paged'));
          return response($response)
                    ->setStatusCode(200);
        } else {
          return response(null)
                    ->setStatusCode(403);
        }

        return UserFile::all();
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function indexUser(User $user, Request $request)
    {
        if (Auth::user()->can('index', UserFile::class)) {
          $response = UserFile::where('user_id', $user->id)->paginate(Config::get('pagination.itemsPerPage'))
          //$response = UserResource::collection($response)
                        ->appends('paged', $request->input('paged'));
          return response($response)
                    ->setStatusCode(200);
        } else {
          return response(null)
                    ->setStatusCode(403);
        }

        return UserFile::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserFileRequest $request)
    {
        if (Auth::user()->can('create', UserFile::class)) {
          // dispatch(new StoreUser($request->all()));
          //$response = $request->hasFile('file') ? 'true' : 'false';

          $model = new UserFile();
          $file = $request->file('file');
          $ext = $file->getClientOriginalExtension();
          $type = $model->getTypeByExtension($ext);

          $serverPathName = '/public/' . $this->getUserDir() . '/' . $type . '/';
          $serverFileName = $request['name'] . '.' . $ext;

          if (Storage::putFileAs($serverPathName, $file, $serverFileName)) {
              $response = $model::create([
                  'user_id' => Auth::id(),
                  'type' => $type,
                  'description' => $request['description'] ? $request['description'] : '',
                  'extension' => $ext,
                  'name' => $request['name'],
              ]);
              return response($response)
                        ->setStatusCode(202);
          }
          return response($request)
                    ->setStatusCode(500);
        } else {
          return response($request)
                    ->setStatusCode(403);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Files\UserFile  $userfile
     * @return \Illuminate\Http\Response
     */
    public function show(UserFile $userfile)
    {
        if (Auth::user()->can('view', $userfile)) {
          $response = $userfile;  //new UserResource($user);
          return response($response)
                    ->setStatusCode(200);
        } else {
          return response(null)
                    ->setStatusCode(403);
        }
        return $userfile;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Files\UserFile  $userfile
     * @return \Illuminate\Http\Response
     */
    public function delivery(UserFile $userfile)
    {
        if (Auth::user()->can('view', $userfile)) {
          //$response = new UserResource($user);
          $file = UserFile::findOrFail($userfile->id);
          if (Storage::disk('local')->exists('/public/' . $this->getUserDir() . '/' . $file->type . '/' . $file->name . '.' . $file->extension)) {
              return Storage::disk('local')->download('/public/' . $this->getUserDir() . '/' . $file->type . '/' . $file->name . '.' . $file->extension);
          }
          return response($response)
                    ->setStatusCode(500);
        } else {
          return response(null)
                    ->setStatusCode(403);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserFileRequest $request, UserFile $userfile)
    {
        if (Auth::user()->can('update', $userfile)){
          //dispatch(new UpdateUser($user, $request->all()));
          $userfile->update($request->all());
          return response(UserFile::find($userfile->id))
                    ->setStatusCode(202);
        } else {
          return response(null)
                    ->setStatusCode(403);
        }

        return [
          'request' => $request,
          'userFile' => $userFile
        ];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserFile $userfile)
    {
        if (Auth::user()->can('delete', $userfile)){
          //dd($userfile);
          $file = UserFile::findOrFail($userfile->id);
          if (Storage::disk('local')->exists('/public/' . $this->getUserDir() . '/' . $file->type . '/' . $file->name . '.' . $file->extension)) {
              if (Storage::disk('local')->delete('/public/' . $this->getUserDir() . '/' . $file->type . '/' . $file->name . '.' . $file->extension)) {
                  return response(json_encode($file->delete()))
                            ->setStatusCode(200);
              }
          }
          return response(null)
                    ->setStatusCode(500);
        } else {
          return response(null)
                    ->setStatusCode(403);
        }
    }

    /**
     * Get directory for the specific user
     * @return string Specific user directory
     */
    private function getUserDir()
    {
        return 'user_' . Auth::id();
    }
}
