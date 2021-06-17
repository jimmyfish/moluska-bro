<?php


namespace App\Http\Services\Photo;


use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;

class ImageProcessingService
{
    private $allowedMimeTypes = [
        'image/jpeg',
        'image/png',
        'image/jpg'
    ];

    private $allowedSize = 2 * 1024 * 1024;

    public function save(UploadedFile $picture)
    {

    }

    public function sanitize(File $picture)
    {

        return $picture;
    }

    /**
     * @param UploadedFile $picture
     * @return bool
     */
    public function validateSize(UploadedFile $picture)
    {
        return $picture->getSize() <= $this->allowedSize;
    }

    public function validateMimeType(UploadedFile $picture): bool
    {
        return in_array($picture->getMimeType(), $this->allowedMimeTypes);
    }
}
