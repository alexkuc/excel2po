<?php

declare(strict_types=1);

namespace App\Converter\Excel;

class Cell
{
  public function __construct(
    public readonly string $column,
    public readonly int $row,
    public readonly string $value
  ) {
  }
}
