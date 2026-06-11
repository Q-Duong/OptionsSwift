<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BlogRequestForm extends FormRequest
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
        if ($this->isMethod('post')) {
            return [
                'blog_title' => 'required',
                'blog_image' => 'required',
                'blog_content' => 'required',
            ];
        } elseif ($this->isMethod('put') || $this->isMethod('patch')) {
            return [
                'blog_title' => 'required',
                'blog_content' => 'required',
            ];
        }
        return [];
    }

    public function messages(): array
    {
        return [
            'blog_title.required' => 'Vui lòng điền thông tin',
            'blog_content.required' => 'Vui lòng điền thông tin',
            'blog_image.required' => 'Vui lòng chọn file'
        ];
    }
}
