<?php

namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

class UpdateMatchScoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'pk' => 'required|integer|exists:matches,id',
            'value' => 'required|integer|min:0|max:255', // max:255, cuz column is TINYINT unsigned
            'name' => 'required|in:home_team_score,away_team_score',
        ];
    }

}