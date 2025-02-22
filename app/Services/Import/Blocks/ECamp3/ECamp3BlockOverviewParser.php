<?php

namespace App\Services\Import\Blocks\ECamp3;

use App\Services\Import\Blocks\BlockListParser;
use Smalot\PdfParser\Parser;
use Illuminate\Support\Collection;

class ECamp3BlockOverviewParser implements BlockListParser
{
    /**
     * Parses the given PDF file and extracts block data.
     *
     * @param string $filePath
     * @return Collection
     */
    public function parse(string $filePath): Collection
    {
        $parser = new Parser();
        $pdf = $parser->parseFile($filePath);
        $text = $pdf->getText();
        $text = mb_convert_encoding($text, 'UTF-8', 'auto');
        return $this->extractBlocks($text);
    }

    /**
     * Extract block data from the text.
     *
     * @param string $text
     * @return Collection
     */
    private function extractBlocks(string $text): Collection
    {
        $blocks = collect();

        // Adjusted regex
        $pattern = '/[A-Za-z]+(\d+)\.(\d+) (.+?)([A-Za-z]{2}) (\d{1,2})\.(\d{1,2})\.(\d{4}) (\d{2}:\d{2}) - (\d{2}:\d{2})/u';

        preg_match_all($pattern, $text, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $day_number = (int)$match[1]; // Extract day number
            $block_number = (int)$match[2]; // Extract block number
            $full_block_number = "{$day_number}.{$block_number}"; // Format full block number without letter

            $name = trim($match[3]); // Extract block name
            $day = (int)$match[5];  // e.g., "18"
            $month = (int)$match[6];  // e.g., "4"
            $year = (int)$match[7]; //e.g., 2025

            // Format block_date as DD.MM.YYYY
            $block_date = sprintf('%02d.%02d.%d', $day, $month, $year);

            $blocks->push([
                'full_block_number' => $full_block_number,
                'day_number' => $day_number,
                'block_number' => $block_number,
                'name' => $name,
                'block_date' => $block_date
            ]);
        }

        return $blocks;
    }
}
