<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Facades\Config;

class Media extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $response = [
            'id' => $this->id,
            'media_type' => $this->media_type,
            'path' => (empty($this->path)) ? '' : env('BASE_URL').Config::get('constants.MEDIA_IMAGE_PATH').$this->path,
            'thumb' => (empty($this->path)) ? '' : env('BASE_URL').Config::get('constants.MEDIA_IMAGE_PATH').$this->path,
            ];

        if($this->media_type == 'pdf') {
            $thumb_path = 'thumb_' . str_replace('pdf', 'jpg', $this->path);
            $actual_thumb_path = public_path().Config::get('constants.MEDIA_IMAGE_PATH').$thumb_path;
            $response['thumb'] = (!file_exists($actual_thumb_path)) ? env('BASE_URL') . 'image/pdf.png' : env('BASE_URL') . Config::get('constants.MEDIA_IMAGE_PATH') . $thumb_path;

        }
        return $response;
    }
}
