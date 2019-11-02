<?php

namespace App\Tools\File;

class Uploader
{

    const FIELD = 'file';
    const UPLOAD_ERR_INI_SIZE = 'La taille du fichier dépasse celle autorisée , upload_max_filesize = ';
    const UPLOAD_ERR_FORM_SIZE = 'La taille du fichier est trops importante.';
    const UPLOAD_ERR_NO_TMP_DIR = 'Le paramétrage du répertoire temporaire est incorrecte.';
    const UPLOAD_ERR_CANT_WRITE = 'Échec de l\'écriture du fichier sur le disque.';
    const UPLOAD_ERR_EXTENSION = 'Ce type de fichier n\'est pas autorisé.';
    const UPLOAD_ERR_PARTIAL = 'Le fichier n\'a été que partiellement téléchargé.';
    const UPLOAD_ERR_NO_FILE = 'Aucun fichier n\'a été téléchargé.';
    const UPLOAD_ERR_UNKOWN = 'Erreur inconnue.';

    /**
     * file
     *
     * @var array
     */
    protected $file;

    /**
     * target path
     *
     * @var string
     */
    protected $targetPath = 'assets/upload/';

    /**
     * error flag
     *
     * @var Boolean
     */
    protected $error;

    /**
     * error code
     *
     * @var Int
     */
    protected $errorCode;

    /**
     * error message
     *
     * @var String
     */
    protected $errorMsg;

    /**
     * file name
     *
     * @var String
     */
    protected $filename;

    /**
     * file extension
     *
     * @var String
     */
    protected $fileext;

    /**
     * tmp file name
     *
     * @var String
     */
    protected $filetmpname;

    /**
     * file target name
     *
     * @var String
     */
    protected $filetarget;

    /**
     * file type
     *
     * @var String
     */
    protected $filetype;

    /**
     * file size
     *
     * @var Int
     */
    protected $filesize;


    /**
     * instanciate
     *
     */
    public function __construct()
    {
        $this->setErrorCode(UPLOAD_ERR_NO_FILE);
        $this->setFile();
    }

    /**
     * return error
     *
     * @return boolean
     */
    public function isError(): bool
    {
        return $this->error;
    }

    /**
     * set target path
     *
     * @param string $path
     * @return Uploader
     */
    public function setTargetPath(string $path): Uploader
    {
        $this->targetPath = $path;
        $this->setFileInfos();
        return $this;
    }

    /**
     * move file from tmp path to target
     *
     * @return Uploader
     */
    public function process(): Uploader
    {
        if (false === $this->error) {
            $destFilename = $this->targetPath . $this->filename;
            $result = move_uploaded_file($this->filetmpname, $destFilename);
            if (false === $result) {
                $this->setErrorCode(UPLOAD_ERR_CANT_WRITE);
            }
        }
        return $this;
    }

    /**
     * get upload file infos
     *
     * @return array
     */
    public function getInfos(): array
    {
        return [
            'error' => $this->error,
            'errorCode' => $this->errorCode,
            'errorMsg' => $this->errorMsg,
            'datas' => [
                'name' => $this->filename,
                'ext' => $this->fileext,
                'tmp' => $this->filetmpname,
                'target' => $this->filetarget,
                'type' => $this->filetype,
                'size' => $this->filesize
            ],
        ];
    }

    /**
     * set file either from global $_FILES or $files
     *
     * @return Uploader
     */
    protected function setFile(array $files = []): Uploader
    {
        $this->file = (!empty($files)) ? $files : $_FILES;
        $this->setFileInfos();
        return $this;
    }

    /**
     * set error code
     *
     * @param integer $errorCode
     * @return Uploader
     */
    protected function setErrorCode(int $errorCode): Uploader
    {
        $this->errorCode = $errorCode;
        $this->error = ($errorCode != 0);
        $this->setErrorMessage();
        return $this;
    }

    /**
     * set error message from error code
     *
     * @return Uploader
     */
    protected function setErrorMessage(): Uploader
    {
        if ($this->error === false) {
            $this->errorMsg = '';
            return $this;
        }
        switch ($this->errorCode) {
            case UPLOAD_ERR_INI_SIZE:
                $this->errorMsg = self::UPLOAD_ERR_INI_SIZE;
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $this->errorMsg = self::UPLOAD_ERR_FORM_SIZE;
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $this->errorMsg = self::UPLOAD_ERR_NO_TMP_DIR;
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $this->errorMsg = self::UPLOAD_ERR_CANT_WRITE;
                break;
            case UPLOAD_ERR_EXTENSION:
                $this->errorMsg = self::UPLOAD_ERR_EXTENSION;
                break;
            case UPLOAD_ERR_PARTIAL:
                $this->errorMsg = self::UPLOAD_ERR_PARTIAL;
                break;
            case UPLOAD_ERR_NO_FILE:
                $this->errorMsg = self::UPLOAD_ERR_NO_FILE;
                break;
            default:
                $this->errorMsg = self::UPLOAD_ERR_UNKOWN;
                break;
        }
        return $this;
    }

    /**
     * set files infos
     *
     */
    protected function setFileInfos(): Uploader
    {
        $isValid = isset($this->file[self::FIELD])
            && !empty($this->file[self::FIELD]);
        if ($isValid) {
            $this->setErrorCode(0);
            $fileInfo = $this->file[self::FIELD];
            $this->filename = $fileInfo['name'];
            $basename = basename($this->filename);
            $this->fileext = (!empty($basename)) ? substr($basename, -4) : '';
            $this->filetmpname = $fileInfo['tmp_name'];
            $this->filetarget = $this->targetPath . $basename;
            $this->errorCode = $fileInfo['error'];
            $this->filetype = $fileInfo['type'];
            $this->filesize = $fileInfo['size'];
            unset($fileInfo);
        } else {
            $this->setErrorCode(UPLOAD_ERR_NO_FILE);
        }
        return $this;
    }
}
