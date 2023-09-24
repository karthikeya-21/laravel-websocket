<?php

namespace App\Http\Controllers;
use App\Http\Requests\UpdateAvatarRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UpdateAvatarController extends Controller
{
    public function update(UpdateAvatarRequest $request){

        $path=$request->file('image')->store('avatars','public');

        if($oldpicture=$request->user()->user_image){
            Storage::delete($oldpicture);
        }
        auth()->user()->update(['user_image'=>$path]);
        return redirect(route('profile.edit'))->with('message','Profile Picture updated Successfully');
    }
}
