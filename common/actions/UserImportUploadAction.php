<?php

namespace common\actions;

use common\models\UserImport;
use trntv\filekit\actions\UploadAction;
use League\Flysystem\FilesystemInterface;
use League\Flysystem\File as FlysystemFile;

/**
 * Class UploadAction
 * public function actions(){
 *   return [
 *           'upload'=>[
 *               'class'=>'trntv\filekit\actions\UploadAction',
 *           ]
 *       ];
 *   }.
 */
class UserImportUploadAction extends UploadAction
{
    /**
     * @return array
     *
     * @throws \HttpException
     */
    public function run()
    {
        $file = null;
        $result = parent::run();
        $path = $result[0]['path'];
        $fs = $this->getFileStorage()->getFilesystem();
        if ($fs instanceof FilesystemInterface) {
            $file = new FlysystemFile($fs, $path);
        }
        $userImport = new UserImport();
        $userImport->file = $file;
        $userImport->path = $path;
        $output = $userImport->import();

        return $output;
    }
}
