<?php

namespace App\Http\Traits ;


use Symfony\Component\HttpFoundation\File\File;


trait ImageTrait
{
    private function setImageName (File $image ,String $imageReference  )
    {
        $imageMimeType = explode('/' , $image->getMimeType() )  ;
        $imageName = time()."_{$imageReference}.{$imageMimeType[1]}" ;
        return $imageName ;
    }

    private function uploadImage (File $image ,String $imageName ,String $path , string $oldPath = null)
    {
        $image->move(public_path('images'.DIRECTORY_SEPARATOR.$path) , $imageName) ;
        if (! is_null($oldPath))
        {
            unlink(public_path($oldPath));
        }
        return true ;
    }
}





?>

