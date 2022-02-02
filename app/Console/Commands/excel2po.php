<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class excel2po extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'excel2po
                         {domain : Translated strings domain}
                         {excelFile : Excel file with translations}
                         {outputDir : output directory for .po & .mo files}';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Generate .po and .mo files from Excel file';

  /**
   * Create a new command instance.
   *
   * @return void
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * Execute the console command.
   *
   * @return int
   */
  public function handle()
  {
    return 0;
  }
}
