<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ThemeController extends Controller
{
    /**
     * Available themes.
     */
    const THEMES = [
        'classic-red' => '经典红',
        'dark-purple' => '暗夜紫',
        'deep-space' => '深空蓝',
        'fresh-green' => '清新绿',
        'pure-white' => '纯净白',
        'warm-campus' => '暖校园',
    ];

    /**
     * Switch active theme.
     */
    public function switch(Request $request)
    {
        $theme = $request->input('theme');
        if ($theme === '' || $theme === null) {
            session()->forget('theme');
        } elseif (isset(self::THEMES[$theme])) {
            session(['theme' => $theme]);
        }
        return redirect()->back();
    }
}
