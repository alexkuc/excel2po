<?php

declare(strict_types=1);

namespace App\Converter\Excel;

interface ExcelInterface
{
  public function loadFile(string $file): bool;

  /** @return ColumnInterface[] */
  public function getColumns(): array;

  public function getRow(int $index): ?RowInterface;
}
