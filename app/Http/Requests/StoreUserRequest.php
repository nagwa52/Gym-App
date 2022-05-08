<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class StoreUserRequest extends FormRequest {
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
        $image_validation = Request::hasFile('user_img') ? 'mimes:jpg,png|max:2048' : '';
        return [
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users,email,' . $this->user_id,
            'password' => 'required|min:8',
            'national_id' => 'digits:14',
            'manageable_id' => 'exists:cities,id',
            'user_img' => $image_validation,
        ];
    }
}
