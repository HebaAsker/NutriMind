<?php

namespace App\Traits;




Trait ImageTrait
{
    function saveImage($image, $folder)
    {
        if($image){
        $extension=$image->getClientOriginalExtension();
        $file_name=time().'.'.$extension; //the file name will be the time at which the file uploaded+ the file extension
        $path=$folder;
        $image->move($path,$file_name);
        return $file_name;
        }
        else
        {
            $default = 'images/profileImages/profile.png' ;
            return $default;
        }

    }
}
