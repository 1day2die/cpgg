<?php

namespace App\Http\Requests\Api\Notifications;

use Illuminate\Foundation\Http\FormRequest;

class SendNotificationRequest extends FormRequest
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
            'via' => 'required|in:mail,database,both',
            'all' => 'required_without:users|boolean',
            'users' => 'required_without:all',
            'title' => 'required|string|min:1',
            'content' => 'required|string|min:1',
        ];
    }
}
