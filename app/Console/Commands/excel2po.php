<?php

namespace App\Console\Commands;

use Gettext\Translation;
use Gettext\Translations;
use Illuminate\Console\Command;
use Gettext\Generator\Generator;
use Gettext\Generator\MoGenerator;
use Gettext\Generator\PoGenerator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\Filesystem\Filesystem;
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
  public function handle(
    Filesystem $fs,
    PoGenerator $poGenerator,
    MoGenerator $moGenerator
  ): int {
    $excelFile = $this->argument('excelFile');

    $outputDir = $this->argument('outputDir');

    if (!$fs->exists($excelFile)) {
      $this->error('Excel found not found!');
      return static::FAILURE;
    }

    if (!$fs->exists($outputDir)) {
      $fs->mkdir($outputDir);
    }

    $reader = IOFactory::createReaderForFile($excelFile);

    $spreadsheet = $reader->load($excelFile);

    $sheet = $spreadsheet->getActiveSheet();

    $columns = $this->createColumnMapping($sheet);

    $from = array_intersect_key($columns, ['msgid' => '']);

    unset($columns['msgid']);

    $domain = $this->argument('domain');

    /** @var Translations[] */
    $collection = [];

    foreach ($columns as $language => $col) {

      $dictionary = $this->createDictionary($sheet, $from['msgid'], $col);

      $translations = $this->createTranslations($domain, $language, $dictionary);

      $collection[$language] = $translations;
    }

    /** @var bool[] */
    $poStatus = [];

    foreach ($collection as $language => $translations) {
      $poStatus[$language] = $this->generateFile(
        $translations,
        $poGenerator,
        $language,
        $outputDir
      );
    }

    foreach ($poStatus as $language => $status) {
      if (!$status) {
        $this->error('Failed to generate .po file for ' . $language);
      }
    }

    /** @var bool[] */
    $moStatus = [];

    foreach ($collection as $language => $translations) {
      $moStatus[$language] = $this->generateFile(
        $translations,
        $moGenerator,
        $language,
        $outputDir
      );
    }

    foreach ($moStatus as $language => $status) {
      if (!$status) {
        $this->error('Failed to generate .mo file for ' . $language);
      }
    }

    return in_array(false, $poStatus, true) || in_array(false, $moStatus, true);
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

  /**
   * Make sure that trailing slash is always there
   * @param string $path original path
   * @return string $path with trailing slash
   */
  protected function normalizePath(string $path): string
  {
    return rtrim($path, '/') . '/';
  }

  /**
   * Create translation file from given translations and generator
   * @param Translations $translations translations
   * @param string $language language
   * @param string $outputDir output directory
   * @return bool true on success and false on failure
   */
  protected function generateFile(
    Translations $translations,
    Generator $generator,
    string $language,
    string $outputDir
  ): bool {

    $ext = null;

    if (is_a($generator, PoGenerator::class)) {
      $ext = 'po';
    }

    if (is_a($generator, MoGenerator::class)) {
      $ext = 'mo';
    }

    if ($ext === null) {
      return false; // unsupported generator
    }

    $generator = new $generator();

    $dir = $this->normalizePath($outputDir);

    $filename = sprintf('%s/%s.%s', $dir, $language, $ext);

    $outcome = $generator->generateFile($translations, $filename);

    return $outcome;
  }
}
