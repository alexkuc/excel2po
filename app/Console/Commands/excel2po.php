<?php

namespace App\Console\Commands;

use Gettext\Translation;
use Gettext\Translations;
use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class excel2po extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'excel2po
                         {domain : Translated strings domain}
                         {excelFile : Excel file with translations}
                         {outputDir : output directory for .po & .mo files}';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Generate .po and .mo files from Excel file';

  /**
   * Create a new command instance.
   *
   * @return void
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * Execute the console command.
   *
   * @return int
   */
  public function handle()
  {
    return 0;
  }

  /**
   * Map first row to respective Excel columns
   * @param Worksheet $sheet Excel sheet
   * @return string[] associative array where $key is cell value and $value is Excel column
   */
  protected function createColumnMapping(Worksheet $sheet): array
  {
    $mapping = [];

    foreach ($sheet->getRowIterator(1, 1) as $row) {

      foreach ($row->getCellIterator() as $col => $cell) {

        $value = $cell->getFormattedValue();

        $mapping[$value] = $col;
      }
    }

    return $mapping;
  }

  /**
   * Create associative array from given Excel column
   * @param Worksheet $sheet Excel sheet
   * @param string $from Excel column with original values
   * @param string $to Excel column with translated values
   * @return string[] associative array where key is $from and value is $to
   */
  protected function createDictionary(
    Worksheet $sheet,
    string $from,
    string $to
  ): array {

    $dictionary = [];

    $rowStart = 2; // skip 1st row, it is header

    $rowEnd = $sheet->getHighestDataRow($from);

    for ($i = $rowStart; $i <= $rowEnd; $i++) {

      $key = $sheet->getCell($from . $i)->getFormattedValue();

      $value = $sheet->getCell($to . $i)->getFormattedValue();

      $dictionary[$key] = $value;
    }

    return $dictionary;
  }

  /**
   * Create Translations class from dictionary
   * @param string $domain text domain
   * @param string $language language
   * @param string[] $dictionary associative array where $key is original string and $value is translated string
   * @return Translations collection of translations
   */
  protected function createTranslations(
    string $domain,
    string $language,
    array $dictionary
  ): Translations {

    $collection = Translations::create($domain, $language);

    foreach ($dictionary as $from => $to) {

      $translation = Translation::create(null, $from);

      $translation->translate($to);

      $collection->add($translation);
    }

    return $collection;
  }
}
