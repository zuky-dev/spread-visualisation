<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;

trait FilesTrait
{

    /**
     * Store file and create DB entry
     *
     * @param UploadedFile $file
     * @param BaseModel $model
     * @param array $json
     * @return void
     */
    public function saveFile(UploadedFile $file, string $path, string $name): void
    {
        $filepath = $this->directory(storage_path($path));

        $file->move($filepath, $name . '.' . $file->getClientOriginalExtension());
    }

    /**
     * Check if directory exist and create if not
     *
     * @param string $path
     * @return string
     */
    protected function directory(string $path): string
    {
        if(!File::exists(storage_path($path))) {
            File::makeDirectory(storage_path($path), 0777, true, true);
        }

        return $path;
    }

    /**
     * Write a file and the specified folder
     *
     * @param string $directory
     * @param string $name
     * @param string $content
     * @return boolean
     */
    protected function file(string $directory, string $name, string $content): bool
    {
        $directory = $this->directory($directory);

        return File::put($directory . '/' . $name, $content);
    }

    /**
     * Checks if file exists and if path is a file
     *
     * @param string $path
     * @return boolean
     */
    protected function isFile(string $path): bool
    {
        return File::exists(storage_path($path)) && !File::isDirectory(storage_path($path));
    }

    /**
     * Deletes specified path or directory
     *
     * @param string $path
     * @return boolean
     */
    protected function deleteFileOrDir(string $path): bool
    {
        if (File::isDirectory(storage_path($path)))
        {
            return File::deleteDirectory(storage_path($path));
        }
        else
        {
            return File::delete(storage_path($path));
        }
    }

    /**
     * Move specified file from souce to destination
     *
     * @param string $file
     * @param string $source
     * @param string $destination
     * @return void
     */
    protected function moveFile(string $file, string $source, string $destination): void
    {
        $destination = $this->directory($destination);
        File::move($source . '/' . $file, $destination . '/' . $file);
    }

    /**
     * Returns file contents
     *
     * @param string $filePath
     * @return string
     */
    protected function openFile(string $filePath): string
    {
        return File::get(storage_path($filePath));
    }

    /**
     * Returns files MIME Type
     *
     * @param string $filePath
     * @return string
     */
    protected function getMime(string $filePath): string
    {
        return File::mimeType(storage_path($filePath));
    }

    /**
     * Returns files extension
     *
     * @param string $filePath
     * @return string
     */
    protected function getExt(string $filePath): string
    {
        return File::extension(storage_path($filePath));
    }

    /**
     * Return files size
     *
     * @param string $filePath
     * @return string
     */
    protected function getSize(string $filePath): string
    {

        $size = File::size(storage_path($filePath));
        return $this->sizeShort($size);
    }

    private function sizeShort($bytes, $decimals = 2) {
        $size = ['B', 'kB', 'MB', 'GB', 'TP'];
        $factor = floor((strlen($bytes) - 1) / 3);

        return number_format(($bytes / pow(1024, $factor)), $decimals, ',', ' ') . ' ' . $size[$factor];
    }

    /**
     * Return files name
     *
     * @param string $filePath
     * @return string
     */
    protected function getName(string $filePath): string
    {
        return File::name($filePath);
    }
}
