<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGameRequest extends FormRequest
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
            'team1_id' => [
                'required',
                'exists:teams,id',
                'different:team2_id'
            ],
            'team2_id' => [
                'required',
                'exists:teams,id',
                'different:team1_id'
            ],
            'name' => [
                'required',
                'in:qualification,semi-final,final'
            ],
            'set' => [
                'required',
                'integer',
                'min:1',
                'max:99'
            ],
            'status' => [
                'nullable',
                'string',
                'max:255'
            ],
            'who_is_serving' => [
                'nullable',
                'in:team1,team2'
            ],
            'team1_score' => [
                'integer',
                'min:0',
                'max:999'
            ],
            'team2_score' => [
                'integer',
                'min:0',
                'max:999'
            ],
            'winner_id' => [
                'nullable',
                'exists:teams,id',
                function ($attribute, $value, $fail) {
                    if ($value && !in_array($value, [$this->team1_id, $this->team2_id])) {
                        $fail('The winner must be one of the playing teams.');
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
            'team1_id.required' => 'Please select Team 1.',
            'team1_id.exists' => 'Selected Team 1 does not exist.',
            'team1_id.different' => 'Team 1 and Team 2 must be different.',
            'team2_id.required' => 'Please select Team 2.',
            'team2_id.exists' => 'Selected Team 2 does not exist.',
            'team2_id.different' => 'Team 1 and Team 2 must be different.',
            'name.required' => 'Game type is required.',
            'name.in' => 'Game type must be qualification, semi-final, or final.',
            'set.required' => 'Set number is required.',
            'set.integer' => 'Set number must be a number.',
            'set.min' => 'Set number must be at least 1.',
            'set.max' => 'Set number cannot exceed 99.',
            'who_is_serving.in' => 'Serving team must be either Team 1 or Team 2.',
            'team1_score.integer' => 'Team 1 score must be a number.',
            'team1_score.min' => 'Team 1 score cannot be negative.',
            'team1_score.max' => 'Team 1 score cannot exceed 999.',
            'team2_score.integer' => 'Team 2 score must be a number.',
            'team2_score.min' => 'Team 2 score cannot be negative.',
            'team2_score.max' => 'Team 2 score cannot exceed 999.',
        ];
    }
}
