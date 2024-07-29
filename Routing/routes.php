<?php

use Response\HTTPRenderer;
use Response\Render\HTMLRenderer;
use Response\Render\JSONRenderer;
use Helpers\DatabaseHelper;
use Helpers\ValidationHelper;
use Helpers\ImageHelper;
use Helpers\StringHelper;
use Exceptions\ValidationException;
use Exceptions\FileUploadException;
use Exceptions\FileDeletionException;
use Exceptions\NotFoundException;

require_once(sprintf("%s/../Constants/FileConstants.php", __DIR__));

return [
    '/' => function(string $path): HTTPRenderer {
        return new HTMLRenderer('form', []);
    },
    '/create' => function(string $path): HTTPRenderer {
        try {
            // validate file
            if (count($_FILES) === 0) {
                throw new ValidationException('The file is not selected.');
            }
            $fileType = $_FILES['file']['type'];
            $fileSize = $_FILES['file']['size'];
            $isValidType = ValidationHelper::imageType($fileType);
            if (!$isValidType) {
                throw new ValidationException(
                    'Invalid file type. Only JPG, JPEG, PNG, and GIF files are allowed.'
                );
            }
            $isValidSize = ValidationHelper::fileSize($fileSize);
            if (!$isValidSize) {
                throw new ValidationException('The file size is too large. The maximum allowed size is 3MB.');
            }
            $clientIp = $_SERVER['REMOTE_ADDR'];
            $uploadedImageCount = DatabaseHelper::getImageCountSameIp($clientIp);
            if ($uploadedImageCount >= UPLOAD_LIMIT_PER_DAY) {
                throw new ValidationException('You have reached the daily upload limit. You can upload a maximum of 3 images per day. Please try again tomorrow.');
            }

            // insert file data to db
            $extension = ImageHelper::imageTypeToExtension($fileType);
            $shareKey = StringHelper::generateRandomStr();
            $deleteKey = StringHelper::generateRandomStr();
            DatabaseHelper::createImage($clientIp, $extension, $shareKey, $deleteKey);

            // save file
            $targetDir = sprintf("%s/../UserFiles/%s", __DIR__, $extension);
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true);
            }
            $targetFile = sprintf("%s/%s.%s", $targetDir, $shareKey, $extension);
            if (!move_uploaded_file($_FILES['file']['tmp_name'], $targetFile)) {
                throw new FileUploadException(
                    'There was an error uploading your file. Please try again.'
                );
            }

            // create urls
            $shareUrl = "http://localhost:8000/share/{$extension}/{$shareKey}";
            $deleteUrl = "http://localhost:8000/delete/{$extension}/{$deleteKey}";

            return new JSONRenderer(["shareUrl" => $shareUrl, "deleteUrl" => $deleteUrl]);
        } catch (ValidationException | FileUploadException $e) {
            return new JSONRenderer(["error" => $e->getMessage()]);
        }
    },
    '/share' => function(string $path): HTTPRenderer{
        try {
            // get image info
            $pathArray = preg_split('/\//', $path);
            $extension = $pathArray[2];
            $shareKey = $pathArray[3];

            // get image data from db
            $image = DatabaseHelper::getImage($extension, $shareKey);
            if (!$image) throw new NotFoundException('The image was not found.');

            // get image from storage
            $imagePath = sprintf(
                "%s/../UserFiles/%s/%s.%s",
                __DIR__,
                $extension,
                $shareKey,
                $extension,
            );
            if (!file_exists($imagePath)) throw new NotFoundException('The image was not found.');

            // update image
            DatabaseHelper::updateImage($image['id']);

            // encode image
            $imageData = file_get_contents($imagePath);
            $imageData = base64_encode($imageData);

            return new HTMLRenderer('share', ['extension' => $extension, 'imageData' => $imageData, 'viewCount' => $image['view_count']]);
        } catch (NotFoundException $e) {
            return new HTMLRenderer('invalid', []);
        }
    },
    '/delete' => function(string $path): HTTPRenderer{
        try {
            // get image info
            $pathArray = preg_split('/\//', $path);
            $extension = $pathArray[2];
            $deleteKey = $pathArray[3];

            // get image data from db
            $image = DatabaseHelper::getImage($extension, $deleteKey, true);
            if (!$image) throw new NotFoundException('The image was not found.');

            // delete image data from db
            DatabaseHelper::deleteImage($image['id']);

            // delete image from storage
            $imagePath = sprintf(
                "%s/../UserFiles/%s/%s.%s",
                __DIR__,
                $extension,
                $image['share_key'],
                $extension,
            );
            if (!file_exists($imagePath)) {
                throw new NotFoundException('The image was not found.');
            }
            if (!unlink($imagePath)) {
                throw new FileDeletionException('Failed to delete file. Please try again.');
            }

            return new HTMLRenderer('delete', ["message" => "Image has been successfully deleted."]);
        } catch (NotFoundException $e) {
            return new HTMLRenderer('invalid', []);
        } catch (FileDeletionException $e) {
            return new HTMLRenderer('delete', ["message" => $e->getMessage()]);
        }
    },
];
