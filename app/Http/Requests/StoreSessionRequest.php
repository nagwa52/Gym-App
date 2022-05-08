<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use App\Rules\NotOverlapped;

class StoreSessionRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }



    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'name' => 'required|min:3',
            'starts_at' => ['sometimes', 'required', 'date_format:Y-m-d H:i:s', new NotOverlapped($this->session_id)],
            'finishes_at' => ['sometimes', 'required', 'date_format:Y-m-d H:i:s', 'after:starts_at', new NotOverlapped($this->session_id)],
        ];
    }
}
