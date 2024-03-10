<?php

namespace App\Models;

use PDOException;
use PDO;
use Aura\SqlQuery\QueryFactory;
use Tamtamchik\SimpleFlash\Flash;


class ImageModel
{
    private $pdo, $queryFactory, $flash;

    public function __construct(PDO $pdo, QueryFactory $queryFactory, Flash $flash)
    {
        try {
            $this -> pdo = $pdo;
        } catch (PDOException $e) {
            die($e->getMessage());
        }
        $this->queryFactory = $queryFactory;
        $this->flash = $flash;
    }

    /**
     * addImage() method to add an image to the database and in directory
     *
     * @param array $imageData
     * @return integer|boolean return id of the image in the database or false
     */
    public function addImage(array $imageData): int|bool
    {
        if (self::checkError($imageData) && self::checkType($imageData)) { 
            return self::addImageInDirectory($imageData);
        }
        return false;
    }

    /**
     * checkError() method to check HTTP errors and return true or false
     *
     * @param array $file
     * @return boolean
     * @throws 'The file size has exceeded the upload_max_filesize value in the PHP configuration.'
     * @throws 'The size of the uploaded file has exceeded the MAX_FILE_SIZE value in the HTML form.'
     * @throws 'The uploaded file was only partially received.'
     * @throws 'The file was not uploaded.'
     * @throws 'The temporary folder is missing.'
     * @throws 'The file could not be written to disk.'
     * @throws 'The PHP extension stopped the file download.'
     */
    private function checkError(array $file) : bool
    {
        $fileTmpName = $file['tmp_name'] ?? null;
        $errorCode = $file['error'] ?? null;
    
        if ($errorCode !== UPLOAD_ERR_OK || !is_uploaded_file($fileTmpName)) {
            // Массив с названиями ошибок
            $errorMessages = [
              UPLOAD_ERR_INI_SIZE   => 'The file size has exceeded the upload_max_filesize value in the PHP configuration.',
              UPLOAD_ERR_FORM_SIZE  => 'The size of the uploaded file has exceeded the MAX_FILE_SIZE value in the HTML form.',
              UPLOAD_ERR_PARTIAL    => 'The uploaded file was only partially received.',
              UPLOAD_ERR_NO_FILE    => 'The file was not uploaded.',
              UPLOAD_ERR_NO_TMP_DIR => 'The temporary folder is missing.',
              UPLOAD_ERR_CANT_WRITE => 'The file could not be written to disk.',
              UPLOAD_ERR_EXTENSION  => 'The PHP extension stopped the file download.',
            ];

            // Если в массиве нет кода ошибки, скажем, что ошибка неизвестна
            if (isset($errorMessages[$errorCode])) {
                $this->flash->error($errorMessages[$errorCode]);   
            } else {
                $this->flash->error('An unknown error occurred while uploading the file.');
            }
    
            return false;
        }
      
      return true;
    }

    /**
     * checkType() method to check type image and add type in session
     * return true if image is valid and false if unvalid
     * types of true: gif, jpeg, png, bmp, wbmp, webp, xbm, avif.
     *
     * @param array $file
     * @return boolean
     * @throws Incorrect file format.
     */
    private function checkType(array $file) : bool
    {
        $type = exif_imagetype($file['tmp_name']);
    
        $arrayType = [
            1 =>	IMAGETYPE_GIF,
            2 =>	IMAGETYPE_JPEG,
            3 =>	IMAGETYPE_PNG,
            6 =>	IMAGETYPE_BMP,
            15 =>	IMAGETYPE_WBMP,
            16 =>	IMAGETYPE_XBM,
            18 =>	IMAGETYPE_WEBP,
            19 =>	IMAGETYPE_AVIF
        ];
    
        foreach ($arrayType as $index => $imageType) {
            if ($type == $index) {
                $_SESSION['imageType'] = $imageType;
                return true;
            }
        }

        $this->flash->error('Incorrect file format.');        
        return false;
    }

    /**
     * addImageInDirectory() method create and add image in directory and database
     * get type of image in session, create new image, href, format and name
     * call addImageInBD() and transfer data to this method
     * 
     *
     * @param array $image
     * @return int New image ID in database
     * @throws Error in creating an image.
     * @throws Error in saving the image to the folder.
     */
    private function addImageInDirectory(array $image): int
    {
        switch ($_SESSION['imageType']) {
    
            case IMAGETYPE_GIF:
                $file = imagecreatefromgif($image['tmp_name']);
                $format = 'gif';
                break;
    
            case IMAGETYPE_JPEG:
                $file = imagecreatefromjpeg($image['tmp_name']);
                $format = 'jpg';
                break;
    
            case IMAGETYPE_WEBP:
                $file = imagecreatefromwebp($image['tmp_name']);
                $format = 'webp';
                break;
    
            case IMAGETYPE_BMP:
                $file = imagecreatefromwbmp($image['tmp_name']);
                $format = 'wbmp';
                break;
    
            case IMAGETYPE_AVIF:
                $file = imagecreatefromavif($image['tmp_name']);
                $format = 'avif';
                break;
    
            case IMAGETYPE_PNG:
                $file = imagecreatefrompng($image['tmp_name']);
                $format = 'png';
                break;
    
            case IMAGETYPE_WBMP:
                $file = imagecreatefromwbmp($image['tmp_name']);
                $format = 'wbpm';
                break;
    
            case IMAGETYPE_XBM:
                $file = imagecreatefromxbm($image['tmp_name']);
                $format = 'xbm';
                break;
              
            default:
                $this->flash->error('Error in creating an image.');        
                return false;
        }
    
        do { $fileName = self::createNameImage($image); } while (self::checkImageName($fileName));
    
        // Размеры файла
        $width = imagesx($file);
        $height = imagesy($file);
    
        $t_im = imageCreateTrueColor($width,$height);
        imageCopyResampled($t_im, $file, 0, 0, 0, 0, $width, $height, $width, $height);
        unset($file);
    
        $href = '/public/upload/' . $fileName. '.' . $format;
        $request = [
            'name' => $fileName, 
            'href' => '/public/upload/', 
            'format' => $format
        ]; 
    
        switch ($_SESSION['imageType']) {
    
            case IMAGETYPE_GIF:
                imagegif($t_im, $_SERVER['DOCUMENT_ROOT'] . $href, 100);
                break;
    
            case IMAGETYPE_JPEG:
                imagejpeg($t_im, $_SERVER['DOCUMENT_ROOT'] . $href, 100);
                break;
    
            case IMAGETYPE_WEBP:
                imagewebp($t_im, $_SERVER['DOCUMENT_ROOT'] . $href, 100);
                break;
    
            case IMAGETYPE_BMP:
                imagebmp($t_im, $_SERVER['DOCUMENT_ROOT'] . $href, 100);
                break;
    
            case IMAGETYPE_AVIF:
                imageavif($t_im, $_SERVER['DOCUMENT_ROOT'] . $href, 100);
                break;
    
            case IMAGETYPE_PNG:
                imagepng($t_im, $_SERVER['DOCUMENT_ROOT'] . $href, 100);
                break;
    
            case IMAGETYPE_WBMP:
                imagewbmp($t_im, $_SERVER['DOCUMENT_ROOT'] . $href, 100);
                break;
    
            case IMAGETYPE_XBM:
                imagexbm($t_im, $_SERVER['DOCUMENT_ROOT'] . $href, 100);
                break;
              
            default:
                $this->flash->error('Error in saving the image to the folder.');        
                return false;
        }

        imagedestroy($t_im);
        unset($_SESSION['imageType']);
        
        $id = self::addImageInBD($request);
        $request = [];
        return $id;
    }

    /**
     * createNameImage() method create a new name image
     *
     * @param array $file
     * @return string
     */
    private function createNameImage(array $file): string
    {
        $hash_file = hash_file('md5', $file['tmp_name']);
        $randomValue = mt_rand(100000, 999999);
        $time = gettimeofday(true);
        
        return md5($hash_file . $time . $randomValue);
    }

    /**
     * checkImageName() method check name in database
     * If database has this name return true
     *
     * @param string $image
     * @return boolean
     */
    private function checkImageName(string $image) : bool 
    {
        $select = $this->queryFactory->newSelect();
        $select->cols(['name'])->from("users_images")->where("name = :name");
        $select->limit(1);

        $sth = $this->pdo->prepare($select->getStatement());
        $sth->execute(['name' => $image]);
        $imageData = $sth->fetch(PDO::FETCH_ASSOC);
    
        if ($image == (is_object($imageData) ? $imageData['name'] : null)) {
            return true;
        }
    
        return false;
    }

    /**
     * addImageInBD() method add image data to the database
     *
     * @param array $params
     * @return integer ID of the image in the database
     */
    private function addImageInBD(array $params) : int
    {
        $insert = $this->queryFactory->newInsert();
        $insert->into("users_images")->cols($params);
        $sth = $this->pdo->prepare($insert->getStatement());
        $sth->execute($insert->getBindValues());
        $name = $insert->getLastInsertIdName('id');
        $id = $this->pdo->lastInsertId($name);
        return $id;
    }

    /**
     * deleteImageInBD() method delete image data in the database
     *
     * @param integer $imageId
     * @return boolean
     */
    private function deleteImageInBD(int $imageId) : bool
    {
        $delete = $this->queryFactory->newDelete();
        $delete->from("users_images")->where("id = {$imageId}");
        $sth = $this->pdo->prepare($delete->getStatement());
        return $sth->execute();
    }
    
    /**
     * deleteImage() method delete image data in the database and in the folder
     *
     * @param integer $userId
     * @return boolean
     * @throws Error when deleting from the database.
     * @throws Error when deleting from a folder.
     */
    public function deleteImage(int $userId): bool
    {
        $select = $this->queryFactory->newSelect();
        $select->cols(['*'])->from("users_info");
        $select->join(
            'LEFT',             // the join-type
            'users_images AS image',        // join to this table ...
            'image.id = users_info.image_id' // ... ON these conditions
        );
        $select->where("user_id = {$userId}");
        $sth = $this->pdo->prepare($select->getStatement());
        $sth->execute();
        $image = $sth->fetch(PDO::FETCH_ASSOC);

        if (!$image) {
            return false;
        }

        if (unlink($_SERVER['DOCUMENT_ROOT'] . $image['href'] . $image['name'] . '.' . $image['format'])) {
            if (self::deleteImageInBD($image['image_id'])) {
                return true;
            } else {
                $this->flash->error('Error when deleting from the database.');
            }
        } else {
            $this->flash->error('Error when deleting from a folder.');
        }
            
        return false;
    }
}
