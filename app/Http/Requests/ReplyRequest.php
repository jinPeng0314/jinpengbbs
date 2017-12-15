<?php

namespace App\Http\Requests;

class ReplyRequest extends Request
{
    public function rules()
    {
        return [
            'content' => 'required|min:2',
        ];
    }

    public function messages()
    {
        return [
            'content.required' => '回复内容不能为空!',
            'content.min' => '回复内容至少有2个字符!',
        ];
    }
}
