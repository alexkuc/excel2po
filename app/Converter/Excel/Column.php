<?php

declare(strict_types=1);

namespace App\Converter\Excel;

use Illuminate\Support\Arr;

class Column implements ColumnInterface
{
  /** @var Cell[] */
  protected array $cells = [];

  public function __construct(Cell ...$cells)
  {
    $this->cells = $cells;
  }

  public function getCellByRow(int $row): ?Cell
  {
    return Arr::first($this->cells, fn (Cell $c) => $c->row === $row);
  }
}
