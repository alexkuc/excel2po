<?php

declare(strict_types=1);

namespace App\Converter\Excel;

interface ColumnInterface
{
  public function getCellByRow(int $row): ?Cell;
}
