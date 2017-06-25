<?php

namespace ZapsterStudios\TeamPay\Controllers;

use App\Http\Controllers\Controller as BaseController;

class Controller extends BaseController
{
    /**
     * Merge request with created slug.
     *
     * @param  bool  $disallowSlug
     * @param  string  $slug
     * @param  string  $name
     * @return array
     */
    public function requestSlug($request, $extra = [], $except = '', $slug = 'slug', $name = 'name')
    {
        return array_merge([
            $slug => str_slug($request->$name),
        ], $extra, $request->except($except));
    }
}
