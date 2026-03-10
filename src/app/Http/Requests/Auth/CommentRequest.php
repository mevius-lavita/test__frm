<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class CommentRequest extends FormRequest
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
            'comment_detail' => 'required|max:255',
        ];
    }

    public function messages()
    {
        return [
            'comment_detail.required' => 'コメントは必須です。',
            'comment_detail.max' => 'コメントは255文字以内でなければなりません。',
        ];
    }
}
