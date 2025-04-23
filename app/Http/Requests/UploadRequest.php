<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;


class UploadRequest extends FormRequest
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
        //'files' => 'required',
        'files.*' => 'required|mimes:jpg,png,pdf,docx|max:102400',
        'expires_in' => 'integer|min:1|max:30',
        'email_to_notify' => 'email',
        "password"=> ['required','string','confirmed',Password::min(6)->letters()->numbers()->mixedCase()->symbols()],
        ];
    }
}
