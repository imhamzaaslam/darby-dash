<?php

namespace App\Http\Requests\project;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Project;

class UpdateFolderRequest extends FormRequest
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
        $project = Project::where('uuid', $this->projectUuid)->firstOrFail();

        return [
            'name' => 'required|string|max:255|unique:folders,name,' . $this->route('folderUuid') . ',uuid,project_id,' . $project->id,
            'projectUuid' => 'required|string|exists:projects,uuid',
        ];
    }
}
