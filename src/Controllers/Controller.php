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
    function requestSlug($request, $disallowSlug = false, $slug = 'slug', $name = 'name')
    {
        return array_merge([
            $slug => str_slug($request->$name),
        ], $request->except($disallowSlug ? 'slug' : ''));
    }
}
