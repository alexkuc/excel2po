<?php

declare(strict_types=1);

namespace Tests\Unit\Converter\Excel;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use App\Converter\Excel\{Cell, Row, Column, Excel};
use App\Converter\Excel\{RowInterface, ColumnInterface};

class ExcelTest extends TestCase
{
  protected static function makeExcel(): Excel
  {
    return new Excel(new Filesystem());
  }

  protected static function getFilePath(): string
  {
    return __DIR__ . '/ExcelTest.xlsx';
  }

  /** @return ColumnInterface[] */
  protected static function getColumns(): array
  {
    return [
      new Column(
        new Cell('A', 1, 'cell_A1'),
        new Cell('A', 2, 'cell_A2'),
        new Cell('A', 3, 'cell_A3'),
        new Cell('A', 4, 'cell_A4'),
      ),
      new Column(
        new Cell('B', 1, 'cell_B1'),
        new Cell('B', 2, 'cell_B2'),
        new Cell('B', 3, 'cell_B3'),
        new Cell('B', 4, 'cell_B4'),
      ),
      new Column(
        new Cell('C', 1, 'cell_C1'),
        new Cell('C', 2, 'cell_C2'),
        new Cell('C', 3, 'cell_C3'),
        new Cell('C', 4, 'cell_C4'),
      ),
    ];
  }

  /** @return RowInterface[] */
  protected static function getRows(): array
  {
    return [
      1 => new Row(
        new Cell('A', 1, 'cell_A1'),
        new Cell('B', 1, 'cell_B1'),
        new Cell('C', 1, 'cell_C1'),
      ),
      new Row(
        new Cell('A', 2, 'cell_A2'),
        new Cell('B', 2, 'cell_B2'),
        new Cell('C', 2, 'cell_C2'),
      ),
      new Row(
        new Cell('A', 3, 'cell_A3'),
        new Cell('B', 3, 'cell_B3'),
        new Cell('C', 3, 'cell_C3'),
      ),
      new Row(
        new Cell('A', 4, 'cell_A4'),
        new Cell('B', 4, 'cell_B4'),
        new Cell('C', 4, 'cell_C4'),
      ),
    ];
  }

  public function test_loadFile_returns_true_on_valid_file()
  {
    $excel = static::makeExcel();

    $this->assertTrue($excel->loadFile(static::getFilePath()));
  }

  public function test_loadFile_returns_false_on_invalid_file()
  {
    $excel = static::makeExcel();

    $this->assertFalse($excel->loadFile('/non/existing/path'));
  }

  public function test_getColumns_returns_columns_matching_file()
  {
    $excel = static::makeExcel();

    $excel->loadFile(static::getFilePath());

    $columns = $excel->getColumns();

    $expected = static::getColumns();

    $this->assertContainsOnlyInstancesOf(Column::class, $columns);

    for ($i = 0; $i < 3; $i++) {
      $this->assertEquals($expected[$i], $columns[$i]);
    }
  }

  public function test_getColumns_returns_empty_array_on_onvalid_file()
  {
    $excel = static::makeExcel();

    $excel->loadFile('/does/not/exist');

    $columns = $excel->getColumns();

    $this->assertEmpty($columns);
  }

  public function test_getColumns_returns_empty_array_without_loaded_file()
  {
    $excel = static::makeExcel();

    $columns = $excel->getColumns();

    $this->assertEmpty($columns);
  }

  public function test_getRow_returns_null_on_invalid_file()
  {
    $excel = static::makeExcel();

    $excel->loadFile('/does/not/exist');

    $row = $excel->getRow(1);

    $this->assertNull($row);
  }

  public function test_getRow_returns_null_without_loaded_file()
  {
    $excel = static::makeExcel();

    $row = $excel->getRow(1);

    $this->assertNull($row);
  }

  public function test_getRow_returns_null_for_negative_row()
  {
    $excel = static::makeExcel();

    $excel->loadFile(static::getFilePath());

    $row = $excel->getRow(-1);

    $this->assertNull($row);
  }

  public function test_getRow_returns_null_for_zero_row()
  {
    $excel = static::makeExcel();

    $excel->loadFile(static::getFilePath());

    $row = $excel->getRow(0);

    $this->assertNull($row);
  }

  public function test_getRow_returns_null_for_too_high_row()
  {
    $excel = static::makeExcel();

    $excel->loadFile($this->getFilePath());

    $row = $excel->getRow(5);

    $this->assertNull($row);
  }

  public function test_getRow_returns_rows_matching_file()
  {
    $excel = static::makeExcel();

    $excel->loadFile(static::getFilePath());

    $expected = static::getRows();

    for ($i = 1; $i < 5; $i++) {

      $row = $excel->getRow($i);

      $this->assertInstanceOf(Row::class, $row);

      $this->assertEquals($expected[$i], $row);
    }
  }
}
