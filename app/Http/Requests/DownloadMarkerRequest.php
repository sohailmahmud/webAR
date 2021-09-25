<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DownloadMarkerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $validation = [
            //'scene_id' => 'required|numeric|exists:scenes,id',
            'download_type' => 'required|in:png,pdf',
            'preview' => 'sometimes|numeric|in:0,1',
            'marker_size' => 'sometimes|numeric|min:1|max:5',           //PDF only
            'quantity_markers' => 'sometimes|numeric|min:1|max:20',     //PDF only
            
        ];
        return $validation;
    }
}
