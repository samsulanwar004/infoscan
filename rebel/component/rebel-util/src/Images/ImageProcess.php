<?php

namespace Rebel\Component\Util\Images;

class ImageProcess
{
    /**
     * @var bool $isUploadedToS3
     */
    protected $isUploadedToS3 = false;
    /**
     * @var bool $isResize
     */
    protected $isResize = false;
    /**
     * @var array $sizes
     */
    protected $sizes = [];

    /**
     * Size image file.
     * @param array
     *
     * @return array
     */
    public function resize(array $images)
    {
        if (empty($this->sizes) || false === $this->isResize) {
            return [];
        }

        $output = [];
        foreach ($images as $image) {

            // read the sizes configuration
            $resized = [];
            foreach ($this->sizes as $size) {
                // do resize
                $resized[$size] = [
                    'original_filename' => '',
                    'new_filename' => '',
                    'binary_file' => $image,
                ];
            }

            $output[] = $resized;
        }

        return $output;
    }

    /**
     * Handle the upload process and upload to aws.
     *
     * @param mixed|array $images
     *
     * @return mixed
     * @throws \Exception
     */
    public function handle($images)
    {
        if (is_array($images)) {
            $files = $this->resize($images);
            foreach ($files as $file) {
                $this->uploadToS3($file);
            }

            return true;
        }

        if (!$images instanceof \Symfony\Component\HttpFoundation\File\File) {
            throw new \Exception('The image must instance of \Symfony\Component\HttpFoundation\File\File');
        }

        $file = $this->resize([$images]);
        $this->uploadToS3($file);
    }

    /**
     * Upload to s3 process
     * @param $file
     *
     * @return bool
     */
    public function uploadToS3($file)
    {
        try {
            if (!$this->isUploadedToS3) {
                return false;
            }

            // do upload
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Set the sizes
     * @param array $sizes
     *
     * @return $this
     */
    public function setSizes(array $sizes)
    {
        $this->sizes = $sizes;

        return $this;
    }
}
