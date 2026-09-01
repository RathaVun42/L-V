<?php
namespace App\Classes;

use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Throwable;

class ImageClass{
    private FilesystemAdapter $disk;
    private string $directory;
    

    public function __construct(
        string $disk = 'public', //refer to the disk inside storage/
        string $directory = 'default'

    )
    {
        $Disk = Storage::disk($disk);
        if(!$Disk instanceof FilesystemAdapter){
            throw new \RuntimeException('Configured disk must resolve to FilesystemAdapter.');
        }
        $this->disk = $Disk;
        $this->directory = trim($directory, '/');
    }

    public function store(UploadedFile $uploadedFile){
        $tmp_path = $uploadedFile->getRealPath(); //return file path or false
        if(!$tmp_path){
            throw new \RuntimeException('Uploaded image source path is invalid.');
        }
        $extension = $uploadedFile->guessExtension() //return extension base on mime type
            ?? $uploadedFile->extension()
            ?? 'jpg';
        $file_name = Str::uuid().'.'.$extension;
        $stored_path = $this->disk->putFileAs($this->directory, $uploadedFile, $file_name);
        if (!$stored_path){
            throw new \RuntimeException('Failed to store uploaded image.');
        }
        return $stored_path;
    }
    public function delete(?string $imagePath) : bool {
        if(!$imagePath){
            return false;
        }
        if($this->disk->exists($imagePath)){
            $this->disk->delete($imagePath);
            return true;
        }
        
        return false;
    }
}