<?php declare(strict_types=1);

namespace App\Service\Uploaders;

use App\Service\ImageOptimizer;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class AvatarPictureUploader extends BaseFileUploader
{
    const MAX_WIDTH = 300;
    const MAX_HEIGHT = 300;
    private ImageOptimizer $imageOptimizer;

    public function __construct($targetDirectory, SluggerInterface $slugger, Filesystem $filesystem)
    {
        parent::__construct($targetDirectory, $slugger, $filesystem);
        $this->imageOptimizer = new ImageOptimizer($this::MAX_WIDTH, $this::MAX_HEIGHT);
    }

    public function upload(UploadedFile $file): bool|string
    {
        $pictureFileName = parent::upload($file);
        $this->imageOptimizer->resize($this->getTargetDirectory() . $pictureFileName);

        return $pictureFileName;
    }

    public function delete(string $fileName): void
    {
        if ($fileName != 'default_avatar.png') {
            parent::delete($fileName);
        }
    }
}