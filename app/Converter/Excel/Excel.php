<?php

declare(strict_types=1);

namespace App\Converter\Excel;

use App\Converter\Excel\Cell;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Component\Filesystem\Filesystem;

class Excel implements ExcelInterface
{
  protected ?Spreadsheet $spreadsheet = null;

  public function __construct(protected Filesystem $fs)
  {
  }

  public function loadFile(string $file): bool
  {
    if (!$this->fs->exists($file)) {
      return false;
    }

    $reader = IOFactory::createReaderForFile($file);

    $this->spreadsheet = $reader->load($file);

    return true;
  }

  /** @return ColumnInterface[] */
  public function getColumns(): array
  {
    if ($this->spreadsheet === null) {
      return [];
    }

    $iterator = $this->spreadsheet->getActiveSheet()->getColumnIterator();

    $columns = [];

    foreach ($iterator as $column) {

      $cells = [];

      foreach ($column->getCellIterator() as $cell) {
        $cells[] = new Cell(
          $cell->getColumn(),
          $cell->getRow(),
          $cell->getFormattedValue()
        );
      }

      $columns[] = new Column(...$cells);
    }

    return $columns;
  }

  public function getRow(int $index): ?RowInterface
  {
    if ($this->spreadsheet === null) {
      return null;
    }

    // unfortunately PHPSpreadsheet does not properly handle zero row
    if ($index === 0) {
      return null;
    }

    try {
      $iterator = $this->spreadsheet->getActiveSheet()->getRowIterator(
        $index,
        $index
      );
    } catch (Exception) {
      return null;
    }

    if (iterator_count($iterator) === 0) {
      return null;
    }

    $cells = [];

    try {
      foreach ($iterator as $row) {
        foreach ($row->getCellIterator() as $cell) {
          $cells[] = new Cell(
            $cell->getColumn(),
            $cell->getRow(),
            $cell->getFormattedValue()
          );
        }
      }
    } catch (Exception) {
      return null;
    }

    return new Row(...$cells);
  }
}
