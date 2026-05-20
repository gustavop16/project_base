<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UploadService
{
    public function prepareData(array $data, $file): array
    {    
        $data['name']   = $file['title'];
        $data['file']   = $file['path_file'];
        return $data;
    }
    
    public function upload($path, $file)
    {
        $path = $file->store($path, 'public');
        return [
            //'path_file' => $path,
            'name'     => $file->getClientOriginalName(),
            'file'      => basename($path)
        ];
    }
} 
