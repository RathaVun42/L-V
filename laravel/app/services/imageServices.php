<?php

namespace App\Services;

use GuzzleHttp\Psr7\UploadedFile;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\UploadedFile as HttpUploadedFile;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;

class ImageServices
{
    private FilesystemAdapter $disk;
    private string $directory;
    private int $thumnailSize;
    private ImageManager $imageManager;

    public function __construct(
        string $disk = 'public',
        string $directory = 'default',
        int $thumnailSize
    )
    {
        $resolveDisk = Storage::disk($disk);
        if(!$resolveDisk instanceof FilesystemAdapter){
            throw new \RuntimeException('Configured disk must resolve to FilesystemAdapter.');

        }
        
        $this->disk = $resolveDisk;
        $this->directory = $directory;
        $this->thumnailSize = $thumnailSize;
        $this->imageManager = new ImageManager(new Driver);
        
    }

    public function store(HttpUploadedFile $file): string{
        $image = $file->getRealPath();
        if(!$image){
            throw new \RuntimeException('Uploaded image source path is invalid.');
        }
        $extension = $file->guessExtension() ?? $file->extension() ?? 'jpg';
        $filename = Str::uuid().'.'. $extension;
        $storedPath = $this->disk->putFileAs($this->directory,$file,$filename);
        

        return $storedPath;
    }
}
