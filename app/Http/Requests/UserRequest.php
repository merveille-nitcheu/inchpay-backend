<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class UserRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    // rules to validation
    public function rules(): array
    {
        return [
            'nom' => 'required|min:4',
            'email' => 'required|email|unique:users,email',
            'telephone' => 'min:9|numeric',
            'password' => 'required',
        ];
    }

    // messages errors
    public function messages(): array
    {
        return [
            'email.email' => 'veuillez entrer une adresse email valide',
            'email.unique' => 'l\'email existe déja. veuillez en choisir un autre',

        ];
    }
    // show errors
    protected function failedValidation(Validator $validator)
    {

        $errors = (new ValidationException($validator))->errors();
        throw new HttpResponseException(response()->json(
            [
                'error' => $errors,
                'status_code' => 422,
            ],
            JsonResponse::HTTP_UNPROCESSABLE_ENTITY
        ));
    }
}
