<?php

namespace App\Http\Requests;

use App\Providers\ImportServiceProvider;
use App\Services\Import\Blocks\BlockListImporter;
use Illuminate\Foundation\Http\FormRequest;

class BlockImportRequest extends FormRequest
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
       return app()->get(ImportServiceProvider::$BLOCK_IMPORTER_MAP[$this->input('source')]);
    }
}
