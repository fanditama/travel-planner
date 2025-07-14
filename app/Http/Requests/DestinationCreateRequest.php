<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class DestinationCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    // public function authorize(): bool
    // {
    //     return true;
    // }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'max:100'],
            'location' => ['required', 'max:100'],
            'latitude' => ['nullable', 'max:10'],
            'longitude' => ['nullable', 'max:11'],
            'category' => ['nullable', 'in:Alam,Sejarah,Petualangan,Kuliner,Santai'],
            'average_rating' => ['nullable', 'min:2'],
            'image_url' => ['nullable', 'url', 'max:100'],
            'approx_price_range' => ['nullable', 'max:100'],
            'best_time_to_visit' => ['nullable', 'max:100'],
            'tag' => ['nullable', 'max:100'],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response([
            "errors" => $validator->getMessageBag()
        ], 400));
    }
}
