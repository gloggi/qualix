<?php

namespace App\Http\Requests;

use App\Providers\ImportServiceProvider;
use App\Services\Import\Blocks\BlockListImporter;

class BlockImportRequest extends FormRequest {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'source' => 'required|in:' . implode(',', array_keys(ImportServiceProvider::$BLOCK_IMPORTER_MAP)),
            'file' => 'required|max:2000',
        ];
    }

    /**
     * Returns the correct importer to use for this request.
     *
     * @return BlockListImporter
     */
    public function getImporter() {
        // check if it is a pdf from ecamp3
        if ($this->file('file')->getMimeType() == 'application/pdf') {
            return app()->get(ImportServiceProvider::$BLOCK_IMPORTER_MAP['eCamp3BlockOverview']);
        } else {
            return app()->get(ImportServiceProvider::$BLOCK_IMPORTER_MAP[$this->input('source')]);
        }
    }
}
