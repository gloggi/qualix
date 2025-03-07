<?php

namespace App\Services\Import\Blocks\ECamp3;

use App\Services\Import\Blocks\BlockListParser;
use Smalot\PdfParser\Config;
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
        // create config
        $config = new Config();
        $config->setDataTmFontInfoHasToBeIncluded(true);

        // use config and parse file
        $parser = new Parser([], $config);
        $pdf = $parser->parseFile($filePath);

        // Extract raw text with positions
        $pages = $pdf->getPages();
        $text = '';

        foreach ($pages as $page) {
            $objs = $page->getDataTm();
            foreach ($objs as $obj) {
                // Add spaces manually between words
                // font size 10 are checklist elements which can also have numbers like (1.2 etc.) and breaks regex.
                $font_size = $obj[3];
                if ($font_size > 10) {
                    $text .= ' ' . $obj[1]; //text element
                }
            }
        }

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
        $pattern = '/.+?\s+(\d+)\.(\d+)\s+(.+?)\s+([A-Za-z]{2,3})\.?\s+(\d{1,2})[\.|\/](\d{1,2})[\.|\/](\d{4})\s+(\d{1,2}:\d{2})(?:\sPM|\sAM)?\s*-(\s+[A-Za-z]{2,3}\.?\s+\d{1,2}[\.|\/]\d{1,2}[\.|\/]\d{4})?\s*(\d{1,2}:\d{2})(?:\sPM|\sAM)?/u';
        preg_match_all($pattern, $text, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $day_number = (int)$match[1]; // Extract day number
            $block_number = (int)$match[2]; // Extract block number
            $full_block_number = "{$day_number}.{$block_number}"; // Format full block number without letter

            $name = trim($match[3]); // Extract block name
            $weekday = $match[4];  // e.g., "Fr"
            $day = (int)$match[5];  // e.g., "18"
            $month = (int)$match[6];  // e.g., "4"
            $year = (int)$match[7]; //e.g., 2025

            if (in_array($weekday, array('Mon','Tue','Wed','Thu','Fri','Sat','Sun'))) {
                // english date format... makes no sense...
                $day = (int)$match[6];  // e.g., "18"
                $month = (int)$match[5];  // e.g., "4"
            }
            // Format block_date as DD.MM.YYYY or DD/MM/YYYY (or MM/DD/YYYY)
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
