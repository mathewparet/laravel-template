<?php
namespace App\Traits;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait HasImage
{
    /**
     * @param \Illuminate\Http\UploadedFile|\Illuminate\Http\UploadedFile[]|array|null $file
     */
    public function uploadImage(UploadedFile|array|null $file): self
    {
        $this->deleteExistingImage();

        if($file) {
            $this->{$this->getImageAttributeName()} = $file?->store($this->getImagePath()
                                                                    , ['disk' => $this->getImageDisk()]
                                                            );
        }

        return $this;
    }

    private function deleteExistingImage(): void
    {
        if(!blank($this->{$this->getImageAttributeName()})) {
            Storage::disk($this->getImageDisk())->delete($this->{$this->getImageAttributeName()});

            $this->{$this->getImageAttributeName()} = null;
        }
    }

    public function deleteImage(): self
    {
        $this->deleteExistingImage();

        return $this;
    }
}