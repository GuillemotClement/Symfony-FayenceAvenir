<?php

namespace App\Service;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Uploader
{
  // on passe dans le construct le component FileSystem, et les deux propriété transmis via config.yaml
  public function __construct(private Filesystem $fs, private $profileFolder, private $profileFolderPublic, private $articleFolder, private $articleFolderPublic)
  {
  }

  public function uploadProfileImage(UploadedFile $picture, string $oldPicturePath = null):string 
  {
    $folder = $this->profileFolder;
    $ext = $picture->guessClientExtension() ?? 'bin';
    $filename = bin2hex(random_bytes(10)) . '.' . $ext;
    $picture->move($folder, $filename);
    if($oldPicturePath){
      $this->fs->remove($folder . '/' . pathinfo($oldPicturePath, PATHINFO_BASENAME));
    }
    return $this->profileFolderPublic . '/' . $filename;
  }

  public function uploadArticleImage(UploadedFile $picture, string $oldPicturePath = null):string
  {
    $folder =$this->articleFolder;
    $ext = $picture->guessClientExtension() ?? 'bin';
    $filename = bin2hex(random_bytes(10)) . '.' . $ext;
    $picture->move($folder, $filename);
    if($oldPicturePath){
      $this->fs->remove($folder . '/' . pathinfo($oldPicturePath, PATHINFO_BASENAME));
    }
    return $this->articleFolderPublic . '/' . $filename;
  }
}