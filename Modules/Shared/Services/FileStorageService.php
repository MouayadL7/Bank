<?php

namespace Modules\Shared\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Response;
use Modules\Shared\Exceptions\FileNotFoundException;

class FileStorageService
{
    public function store(UploadedFile $file, string $path, string $disk = 'public'): string
    {
        $filename = $this->generateUniqueFilename($file);
        Storage::disk($disk)->putFileAs($path, $file, $filename);
        return $path . '/' . $filename;
    }

    public function delete(string $path, string $disk = 'public'): bool
    {
        if ($this->exists($path)) {
            return Storage::disk($disk)->delete($path);
        }

        return false;
    }

    public function exists(string $path, string $disk = 'public'): bool
    {
        return Storage::disk($disk)->exists($path);
    }

    public function url(string $path, string $disk = 'public'): string
    {
        return Storage::url($path);
    }

    public function download(string $path, string $disk = 'public')
    {
        $fullPath = Storage::disk($disk)->path($path);

        if (!file_exists($fullPath)) {
            throw FileNotFoundException::notFound($path);
        }

        return Response::download($fullPath);
    }

    protected function generateUniqueFilename(UploadedFile $file): string
    {
        return Str::uuid() . '.' . $file->getClientOriginalExtension();
    }
}
