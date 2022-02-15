<?php

declare(strict_types=1);

namespace App\Converter\Excel;

use Illuminate\Support\Arr;

class Row implements RowInterface
{
  /** @var Cell[] */
  protected array $cells = [];

  public function __construct(Cell ...$cells)
  {
    $this->cells = $cells;
  }

  public function getCellByIndex(string $column): ?Cell
  {
    return Arr::first($this->cells, fn (Cell $c) => $c->column === $column);
  }
}