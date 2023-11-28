<?php

namespace App\Http\Traits ;


use Symfony\Component\HttpFoundation\File\File;


trait ImageTrait
{
    private function setImageName (File $image ,String $imageReference  ) : string
    {
        $imageMimeType = explode('/' , $image->getMimeType() )  ;
        $imageName = time()."_{$imageReference}.{$imageMimeType[1]}" ;
        return $imageName ;
    }

    private function uploadImage (File $image ,String $imageName ,String $path , string $oldPath = null) : bool
    {
        $image->move(public_path('images'.DIRECTORY_SEPARATOR.$path) , $imageName) ;
        if (! is_null($oldPath))
        {
            $this->unlinkImage($oldPath) ;
        }
        return true ;
    }

    private function unlinkImage (String $imagePath)
    {
        return unlink(public_path($imagePath));
    }
}





?>

