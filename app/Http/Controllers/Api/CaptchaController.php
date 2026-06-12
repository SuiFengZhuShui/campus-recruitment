<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CaptchaController extends Controller
{
    public function show(Request $request)
    {
        $code = $this->generateCode();
        session(['captcha_code' => $code]);

        $width = 120;
        $height = 44;
        $img = imagecreatetruecolor($width, $height);

        $bgColor = imagecolorallocate($img, 240, 240, 245);
        imagefill($img, 0, 0, $bgColor);

        // Add noise lines
        for ($i = 0; $i < 5; $i++) {
            $lineColor = imagecolorallocate($img, rand(160, 200), rand(160, 200), rand(160, 200));
            imageline($img, rand(0, $width), rand(0, $height), rand(0, $width), rand(0, $height), $lineColor);
        }

        // Add noise dots
        for ($i = 0; $i < 80; $i++) {
            $dotColor = imagecolorallocate($img, rand(150, 200), rand(150, 200), rand(150, 200));
            imagesetpixel($img, rand(0, $width), rand(0, $height), $dotColor);
        }

        // Draw text
        $fontSize = 22;
        $textColor = imagecolorallocate($img, 40, 40, 80);
        $x = 10;
        for ($i = 0; $i < strlen($code); $i++) {
            $y = rand(26, 34);
            imagestring($img, 5, $x, $y - 14, $code[$i], $textColor);
            $x += 24 + rand(-2, 4);
        }

        ob_start();
        imagepng($img);
        $imageData = ob_get_clean();
        imagedestroy($img);

        return response($imageData)->header('Content-Type', 'image/png')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate');
    }

    private function generateCode(): string
    {
        $chars = '23456789abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ';
        $code = '';
        for ($i = 0; $i < 4; $i++) {
            $code .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $code;
    }
}
