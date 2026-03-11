<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
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
        return [
            'profile_img' => ['nullable', 'image', 'mimes:jpeg,png'],
            'nickname' => ['required', 'string', 'max:20'],
            'address_number' => ['required', 'string', 'regex:/^\d{3}-\d{4}$/'],
            'address' => ['required', 'string'],
            'building' => ['nullable', 'string'],
        ];
    }

    protected function prepareForValidation()
    {
        $addressNumber = $this->input('address_number');

        if (is_string($addressNumber)) {
            $digitsOnly = preg_replace('/\D/u', '', $addressNumber);

            if (strlen($digitsOnly) === 7) {
                $addressNumber = substr($digitsOnly, 0, 3) . '-' . substr($digitsOnly, 3);
            }

            $this->merge([
                'address_number' => $addressNumber,
            ]);
        }
    }
}
