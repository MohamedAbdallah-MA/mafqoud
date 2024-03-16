<?php

namespace App\Http\Traits ;

use Exception;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\File;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;


trait ImageTrait
{
    use ApiResponseTrait;
    public function setImageName (File $image ,String $imageReference  ) : string
    {
        $imageName = time()."_{$imageReference}" ;
        return $imageName ;
    }

    public function getImageUrl (String $imagePath)
    {
        try {
            return Cloudinary::getUrl($imagePath);
        } catch (Exception $e) {
            return $this->apiResponse(500 , $e->getMessage() , $e);
        }
    }

    public function uploadImage (File $image ,String $imageName ,String $path , string $oldPath = null) 
    {
        try {
            $uploadedImage = Cloudinary::upload($image->getRealPath(), [
                'folder' => 'mafqoud/images/'.$path,
                'public_id' => $imageName,
            ]);
        } catch (Exception $e) {
            return $this->apiResponse(500 , $e->getMessage() , $e);
        }

        //delete old image
        if (! is_null($oldPath))
        {
            $this->deleteImage($oldPath) ;
        }

        return  $uploadedImage ;
    }

    public function deleteImage (String $imagePath) 
    {   
        try {
            Cloudinary::destroy($imagePath);
        } catch (Exception $e) {
            return $this->apiResponse(500 , $e->getMessage() , $e);
        }
    }

    public function deleteImages (array $imagesPaths)
    {
        foreach ($imagesPaths as $imagePath)
        {
            try {
                $this->deleteImage($imagePath);
            } catch (Exception $e) {
                return $this->apiResponse(500 , $e->getMessage() , $e);
            }
        }
    }
}






