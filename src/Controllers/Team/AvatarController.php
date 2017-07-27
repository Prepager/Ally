<?php

namespace ZapsterStudios\Ally\Controllers\Team;

use Image;
use App\Team;
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
    public function update(Request $request, Team $team)
    {
        $this->authorize('update', $team);
        $this->validate($request, [
            'avatar' => 'required|image',
        ]);

        $original = $team->getOriginal('avatar');

        $file = $request->file('avatar');
        $path = 'avatars/'.str_slug($team->name).'-'.uniqid().'.png';

        $image = Image::make($file)->fit(300)->encode('png');
        Storage::disk('public')->put($path, $image);

        $team->update([
            'avatar' => $path,
        ]);

        if (! empty($original)) {
            Storage::disk('public')->delete($original);
        }

        return response()->json($team);
    }
}
