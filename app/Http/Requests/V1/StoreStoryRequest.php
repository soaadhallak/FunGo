<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreStoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'place_id'=>['required','exists:places,id'],
            'txt'=>['nullable','string','min:3','max:255'],
            'image'=>['required','image','mimes:jpeg,png,jpg,gif,webp', 'max:2048']
        ];
    }
    protected function prepareForValidation()
    {
        $this->merge([
            'user_id'=>Auth::id()
        ]);
    }
}
