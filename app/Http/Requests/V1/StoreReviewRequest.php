<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreReviewRequest extends FormRequest
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
            'comment'=>['sometimes','string','min:3','max:256'],
            'rating'=>['required','integer','between:1,5'],
            'place_id'=>['required','exists:places,id'],
            'activity_type_id'=>['required','exists:activity_types,id']
        ];
    }
     protected function prepareForValidation()
    {
        $this->merge([
            'user_id'=>Auth::id()
        ]);
    }
}
