<?php

declare(strict_types=1);

namespace App\Converter\Excel;

interface RowInterface
{
  public function getCellByIndex(string $column): ?Cell;
}
