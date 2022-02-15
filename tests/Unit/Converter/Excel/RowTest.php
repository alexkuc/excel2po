<?php

declare(strict_types=1);

namespace Tests\Unit\Converter\Excel;

use App\Converter\Excel\Row;
use App\Converter\Excel\Cell;
use PHPUnit\Framework\TestCase;

class RowTest extends TestCase
{
  protected function cellFactory(): array
  {
    return [
      'A' => new Cell('A', 1, 'cell_a'),
      'B' => new Cell('B', 2, 'cell_b'),
      'C' => new Cell('C', 3, 'cell_c'),
      'D' => new Cell('D', 4, 'cell_d'),
      'E' => new Cell('E', 5, 'cell_e'),
    ];
  }

  public function test_getCellByIndex_retuns_null()
  {
    $row = new Row(...$this->cellFactory());

    $this->assertNull($row->getCellByIndex('F'));
  }

  public function test_getCellByIndex_returns_cell()
  {
    $row = new Row(...$this->cellFactory());

    $this->assertEquals($this->cellFactory()['C'], $row->getCellByIndex('C'));
  }
}
