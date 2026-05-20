<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AttachmentResource;
use App\Models\Attachment;
use App\Services\AttachmentService;
use App\Services\UploadService;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AttachmentController extends Controller
{
    private Attachment $attachment_model;
    private UploadService $upload_service;
    private AttachmentService $attachment_service;

    public function __construct()
    {
        $this->attachment_model     = new Attachment();
        $this->attachment_service   = new AttachmentService();
        $this->upload_service       = new UploadService();
    }

    public function index($model, $item_id)
    {
        $model  = $this->attachment_service->instanceModel($model);
        $target = $model->find($item_id);
        if($target->attachments){
            return AttachmentResource::collection($target->attachments);
        }
        return response()->json(['data' => [],], 200);
    }

    public function store(Request $request)
    {
        if(isset($request->files)){ 
            $model  = $this->attachment_service->instanceModel($request->model);
            $target = $model->find($request->item_id);
            foreach ($request->file('files') as $key => $value) {
                $file   = $this->upload_service->upload($request->model.'/'.$target->id, $value);
                $target->attachments()->create($file);
            }
        }
        return response()->json(['message' => 'ok',], 200);
    }

    public function destroy(Attachment $attachment)
    {
        $attachment->delete();
        return response()->json(['message' => 'Registro removido com sucesso'], 200);
    }

    public function downloadOrView($model, $item, $filename)
    {
        $path =  "{$model}/{$item}/{$filename}";
        
        $disk = Storage::disk('public');

        if (!$disk->exists($path)) {
            return response()->json([
                'message' => 'Arquivo não encontrado',
            ], 404);
        }
        return $disk->download($path);
    }

}
