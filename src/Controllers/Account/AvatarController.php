<?php

namespace ZapsterStudios\Ally\Controllers\Account;

use Image;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use ZapsterStudios\Ally\Controllers\Controller;

class AvatarController extends Controller
{
    /**
     * Update an existing users avatar.
     *
     * @param  Request  $request
     * @return Response
     */
    public function update(Request $request)
    {
        $this->authorize('update', User::class);
        $this->validate($request, [
            'avatar' => 'required|image',
        ]);

        $user = $request->user();
        $original = $user->getOriginal('avatar');

        $file = $request->file('avatar');
        $path = 'avatars/'.str_slug($user->name).'-'.uniqid().'.png';

        $image = Image::make($file)->fit(300)->encode('png');
        Storage::disk('public')->put($path, $image);

        $user->update([
            'avatar' => $path,
        ]);

        if (! empty($original)) {
            Storage::disk('public')->delete($original);
        }

        return response()->json($user);
    }
}
