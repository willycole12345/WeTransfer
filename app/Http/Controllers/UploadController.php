<?php

namespace App\Http\Controllers;

use App\Http\Requests\DownloaduploadRequest;
use App\Mail\UploadNotification;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\UploadRequest;
use App\Http\Resources\LoginResource;
use App\Models\UploadSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Str;
use ZipArchive;

class UploadController extends Controller
{
    public function create() {
        return "welcome";
    }
    public function store(UploadRequest $request)
    {
       
        $response = array();
        $uploadedRecord = $request->validated();
       // $rec= $request->file('files');
      $number_of_uploaded_image = count($uploadedRecord['files']);
    // dd($number_of_uploaded_image);

        if($number_of_uploaded_image > 4 ){
            $response['message']= "you can not upload more the 5 images at the moment";
            $response['status']="failed";
          return new LoginResource($response);
        }

        $token_key = str()->random(32);
        $expire = $request->input('expires_in', 1);


        $sess_upload = UploadSession::create([
            'token' => $token_key,
            'expires_in' => $expire,
            'expires_at' => now()->addDays($expire),
            'email_to_notify' => $uploadedRecord['email_to_notify'],
            'download_password' =>(($uploadedRecord['password']) ? Hash::make($uploadedRecord['password']) : null),
        ]);

        foreach ($uploadedRecord['files'] as $file) {
          //  dd($file);
         $originalName = $file->getClientOriginalName();
        $uniqueName = uniqid() . '_' . $originalName;
          //  $file->storeAs("uploads/{$token_key}", $storedName, 'public');
        Storage::disk('uploads')->putFileAs('', $file, $uniqueName);

            $sess_upload->files()->create([
                'original_name' => $file->getClientOriginalName(),
                'stored_name' => $uniqueName,
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
            ]);
        }

        
        $links= url('/api/download/' . $token_key);
       
        $return = Mail::to($request->email_to_notify)->send(new UploadNotification($links,$request->email_to_notify));
        $response['download_link'] = url('/api/download/' . $token_key);
        $response['status'] = true;

        return new LoginResource($response);
    
    }

    public function view(DownloaduploadRequest $request,$token)
{
    $vailadatepassword = $request->validated();
    $record_details = UploadSession::with('files')->where('token', $token)->firstOrFail();

    if ($record_details) {
            $check_if_expire = now()->greaterThan($record_details->expires_at);

            if ($check_if_expire) {
                $response['message'] = 'Download link has expired.';
                $response['status'] = 'failed.';
                 return new LoginResource($response);
            }
        }

    if ($record_details->download_password) {
        $password = $vailadatepassword['password'];
        if (!$password || !Hash::check($password, $record_details->download_password)) {
             $response['message'] = 'Password incorrect.';
                $response['status'] = 'failed.';
                 return new LoginResource($response);
        }
    }

    $zipName = 'download_' . $token . '.zip';
    $zipPath = Storage::disk('tmp')->path($zipName);

    $zip = new ZipArchive;
    if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
        foreach ($record_details->files as $file) {
            $localPath = Storage::disk('uploads')->path($file->stored_name);

            if (file_exists($localPath)) {
                $zip->addFile($localPath, $file->original_name);
                $file->increment('download_count');
            }
        }
        $zip->close();
    } else {
         $response['message'] = 'Failed to create ZIP.';
       $response['status'] = 'failed.';
    return new LoginResource($response);

    }

    return response()->download($zipPath)->deleteFileAfterSend(true);
}

    public function Get_Uploaded_Details($token){

            $record = UploadSession::with('files')->where('token', $token)->firstOrFail();
            //  return new LoginResource($record);
            $record_details = array(
                    'Expires_day' => $record->expires_at,
                    'Number_of_files' => $record->files->count(),
                    'TotalSize' => $record->files->sum('size'),
                    'TotalDownloads' => $record->files->sum('download_count'),
                    'files' => $record->files->map(function ($file) {
                    return [
                        'File_name' => $file->original_name,
                        'File_size' => $file->size,
                        'Downloads' => $file->download_count,
                    ];
             }),
        );
        return new LoginResource($record_details);
    }
}
