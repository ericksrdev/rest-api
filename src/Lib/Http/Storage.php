<?php

namespace App\Lib\Http;

class Storage
{
    /**
     * Stores a uploaded file
     * @param array $file array in $_FILES format
     * @param bool $copy  flag to use copy or move_uploaded_file
     *
     * @return null|string It returns the path of the uploaded file if it's successfully uploaded
     *                     otherwise it returns null
     */
    public static function storeInLocalDisk(array $file, $copy = false)
    {
        $target_file_name = 'profile_picture_' . uniqid();

        $target_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        $target_storage_folder = 'public/storage/';

        $target_file_path = $target_storage_folder . $target_file_name . '.' . $target_extension;

        if ($copy)
        {
            $result = copy($file['tmp_name'], $target_file_path);
        }
        else
        {
            $result = move_uploaded_file($file['tmp_name'], $target_file_path);
        }

        if ($result === true)
        {
            return $target_file_path;
        }

        return null;
    }

    /**
     * Validates if the request file is a valid image
     * @param array $file array in $_FILES format
     * @return bool
     */
    public static function uploadIsImage(array &$file)
    {
        $validatorResult = getimagesize($file['tmp_name']);

        if ($validatorResult)
        {
            $file['mime'] = $validatorResult['mime'];
            return true;
        }

        return false;
    }
}