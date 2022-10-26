<?php

namespace App\Http\Controllers;

use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use iImage;

class MediaController extends Controller
{
    public static function uploadToS3($image, $item){
        $image = iImage::make($image)->encode('jpg', 75);
        $s3 = Storage::disk('s3');
        $path = 'covers/' . $item->id . '/' . MediaController::generateRandomString(20) . '.jpg';
        $s3->put($path, $image);
        if ($path != false) {
            $media = new Media(['path' => $path]);
            $item->cover()->save($media);
        }
        return isset($media) ? $media : false;
    }

    public static function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
