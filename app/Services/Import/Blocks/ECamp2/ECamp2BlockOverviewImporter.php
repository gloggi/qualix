<?php

namespace App\Services\Import\Blocks\ECamp2;

use App\Services\Import\Blocks\ECamp2\ECamp2BlockOverviewParser;
use App\Services\Import\Blocks\BlockListImporter;

class ECamp2BlockOverviewImporter extends BlockListImporter
{
    public function __construct(ECamp2BlockOverviewParser $parser) {
        $this->parser = $parser;
    }
}
