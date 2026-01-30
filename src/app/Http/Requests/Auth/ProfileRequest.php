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
    

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|max:20',
            'address_number' => ['required', 'regex:/^\d{3}-\d{4}$/'],
            'address' =>
            'required',
        ];
    }
    public function messages()
    {
        return [
            'name.required' => 'ユーザー名を入力してください',
            'name.max20' => 'ユーザー名は20文字以内で入力してください',
            'address_number.required' => '郵便番号を入力してください',
            'address_number.regex' => '郵便番号はハイフンを入れて8文字で入力してください',
            'address.required' => '住所を入力してください'
        ];
    }
    public function authenticate()
    {
        if (! Auth::attempt($this->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'password' => 'ログイン情報が登録されていません',
            ]);
        }
    }
}
