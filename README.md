## excel2po

Store all your translations in a single Excel file and immediately generate .po & .mo files from that using a single command.

### Requirements

-   php 8.1
-   composer

### Installation

Just run `composer install` and you are done.

### Usage

`php artisan excel2po <domain> <excel> <destination>`

| argument    | purpose                        |
| ----------- | ------------------------------ |
| domain      | text domain for language files |
| excel       | Excel spreadsheet              |
| destination | directory for .po & .mo files  |

### Excel Format

Please refer to [this Excel file](tests/Feature/fixtures/excel2po.xlsx) as a reference on how to structure your translations spreadsheet. First row acts as a header specifying the language. Column named `msgid` is a hard requirement, it acts as a .pot file.

### Purpose

Why did I create this cli tool? Previously, I was using [POEdit](https://poedit.net) and it was more than enough. However, very recently I was faced with 2 issues:

-   multiple languages
-   existing process that rely on Excel

If you have to translate only few languages, [POEdit](https://poedit.net) is plenty. However, if you deal with, say 10+ languages, opening, saving closing get tedious very fast...

Not all organizations use [POEdit](https://poedit.net) and not everyone is in the capacity to change existing processes. Manually converting large Excel files into .pot and then generating .po/.mo from that is a length process...
