<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
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
            'item_name' => ['required', 'string'],
            'item_detail' => ['required', 'string', 'max:255'],
            'item_img' => ['required', 'image', 'mimes:jpeg,png'],
            'category_ids' => ['required', 'array', 'min:1'],
            'category_ids.*' => ['exists:categories,id'],
            'condition_id' => ['required', 'in:良好,目立った傷や汚れなし,やや傷や汚れあり,状態が悪い'],
            'price' => ['required', 'integer', 'min:0'],
        ];
    }
}
