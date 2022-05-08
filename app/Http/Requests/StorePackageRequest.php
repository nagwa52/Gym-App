<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class StorePackageRequest extends FormRequest {
    /**
     * Determine if the Package is authorized to make this request.
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
            'price' => 'required|numeric',
            'sessions_amount' => 'required|numeric',
            'has_packages_id' => 'exists:gyms,id',
        ];
    }
}
