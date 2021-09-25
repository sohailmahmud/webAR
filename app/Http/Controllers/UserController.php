<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Storage;


class UserController extends Controller
{

    public function admin_index() {
        $this->authorize('admin_index', User::class);
        $users = User::where('status', '>=', 0)
                            ->orderBy('id', 'desc')
                            ->paginate($this->itens_page);

        return view('user.admin_index', ['users' => $users]); 
    }



    public function admin_add(Request $request) {
        $this->authorize('admin_add', User::class);
        if($request->isMethod('post')) {
            $request->validate([
                'name' => ['required', 'string', 'max:100'],
                'email' => ['required', 'string', 'email', 'max:100', 'unique:users'],
                'password' => ['required', 'string', 'min:6', 'confirmed'],
                'role' => ['required', 'numeric', 'in:0,1'],
            ]);
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role
            ]);
            event(new Registered($user));
            $request->session()->flash('status', __('Registered'));
        }
        return view('user.admin_add');
    }



    public function edit(Request $request, User $user) {

        $this->authorize('edit', $user);

        if($request->isMethod('put')) {
            // password is valid
            $pwdEditorOk = $request->user()->role == 'editor' 
                                && Hash::check($request->password, $user->password);
            $pwdAdminOk = $request->user()->role == 'admin'
                                && Hash::check($request->password, Auth::user()->password);

            if($pwdEditorOk || $pwdAdminOk) {

                $validation = [
                    'name' => ['required', 'string', 'max:100'],
                    'email' => ['required', 'string', 'email', 'max:100'],
                    'password' => ['required', 'string', 'min:6'],
                    'role' => ['sometimes', 'numeric', 'in:0,1'], //admin
                    'status' => ['sometimes', 'numeric', 'in:0,1,2'], //admin
                ];

                // e-mail changed
                if($request->email != $user->email) {
                    $validation['email'][] = 'unique:users';
                }
                
                if(!empty($request->newpassword)) {
                    $validation['newpassword'] = ['present', 'string', 'min:6', 'confirmed'];
                }

                // validate
                $request->validate($validation);

                $user->name = $request->name;
                $user->email = $request->email;

                if(!empty($request->newpassword)) {
                    $user->password = Hash::make($request->newpassword);
                }

                if($request->user()->role === 'admin') {
                    $user->role = $request->role;
                    $user->status = $request->status;
                }

                $user->save();
                $request->session()->flash('status', __('Profile updated.'));

            } else {
                $request->session()->flash('err', __('Invalid Password'));
            }
            
        }
        
        return view('user.edit', ['user' => $user, 'role' => $request->user()->role]);
    }

    /*
    * Resend the email verification notification.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
   public function admin_resend(Request $request, User $user)
   {
        $this->authorize('admin_resend', User::class);
       if ($user->hasVerifiedEmail()) {
           return redirect($this->redirectPath());
       }

       $user->sendEmailVerificationNotification();

       $request->session()->flash('status', __('Verification link sent.'));
       return back()->with('resent', true);
    }


    public function admin_delete(Request $request, User $user) {

        $this->authorize('admin_delete', $user);

        // Scenes
        $scenes = $user->scenes;
        if(!empty($scenes)) {
            foreach ($scenes as $scene) {
                // Entities
                $entities = $scene->entities;
                if(!empty($entities)) {
                    // Delete Entities
                    foreach ($entities as $entity) {
                        $entity->props = json_decode($entity->props);
                        $file = $entity->props->asset->file?? false;
                        if ($file) {
                            Storage::delete("assets/$file");
                        }
                        $entity->delete();
                    }
                }
                // Marker
                $marker = $scene->marker?? false;
                if ($marker) {
                    // Delete Marker
                    $file = $marker->file?? false;
                    if ($file) {
                        Storage::delete("markers/$file");
                    }
                    $marker->delete();
                }
                // Delete Scene
                $scene->delete();
            }
        }
        // Delete User
        $user->delete();

        if ($request->query('type') == 'json') {
            return response()->json(['response' => 'OK', 'message' => 'deleted']);
        } else {
            $request->session()->flash('status', __('User deleted.'));
            return redirect()->action('UserController@admin_index');
        }
    }

}
