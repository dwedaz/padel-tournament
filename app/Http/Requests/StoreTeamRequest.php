<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Group;

class StoreTeamRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:teams,name'
            ],
            'group_id' => [
                'required',
                'exists:groups,id',
                function ($attribute, $value, $fail) {
                    $group = Group::find($value);
                    if ($group && $group->teams()->count() >= 5) {
                        $fail('This group already has maximum 5 teams.');
                    }
                }
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Team name is required.',
            'name.unique' => 'Team name must be unique.',
            'name.max' => 'Team name cannot exceed 255 characters.',
            'group_id.required' => 'Please select a group.',
            'group_id.exists' => 'Selected group does not exist.'
        ];
    }
}
