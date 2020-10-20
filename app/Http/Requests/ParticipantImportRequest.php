<?php

namespace App\Http\Requests;

use App\Providers\ImportServiceProvider;
use App\Services\Import\Participants\ParticipantListImporter;

class ParticipantImportRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'source' => 'required|in:' . implode(',', array_keys(ImportServiceProvider::$PARTICIPANT_IMPORTER_MAP)),
            'file' => 'required|max:2000',
        ];
    }

    /**
     * Returns the correct importer to use for this request.
     *
     * @return ParticipantListImporter
     */
    public function getImporter() {
       return app()->get(ImportServiceProvider::$PARTICIPANT_IMPORTER_MAP[$this->input('source')]);
    }
}
