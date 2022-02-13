<?php

namespace Tests\Feature;

use Tests\TestCase;
use Symfony\Component\Filesystem\Filesystem;

class excel2poTest extends TestCase
{
  protected function tearDown(): void
  {
    $outputDir = $this->getParams()['outputDir'];

    $fs = new Filesystem();

    $fs->remove($outputDir);

    parent::tearDown();
  }

  protected function getParams(): array
  {
    return [
      'domain'    => 'test-domain',
      'excelFile' => __DIR__ . '/fixtures/excel2po.xlsx',
      'outputDir' => rtrim(sys_get_temp_dir(), '/') . '/excel2po',
    ];
  }

  public function test_command(): void
  {
    $args = $this->getParams();

    $this->artisan('excel2po', $args)->assertSuccessful();

    $this->assertSame(
      md5_file(__DIR__ . '/fixtures/en.po'),
      md5_file($args['outputDir'] . '/en.po')
    );

    $this->assertSame(
      md5_file(__DIR__ . '/fixtures/en.mo'),
      md5_file($args['outputDir'] . '/en.mo')
    );

    $this->assertSame(
      md5_file(__DIR__ . '/fixtures/ru.po'),
      md5_file($args['outputDir'] . '/ru.po')
    );

    $this->assertSame(
      md5_file(__DIR__ . '/fixtures/ru.mo'),
      md5_file($args['outputDir'] . '/ru.mo')
    );
  }
}
