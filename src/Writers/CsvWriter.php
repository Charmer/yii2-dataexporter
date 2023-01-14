<?php

namespace charmer\dataexporter\Writers;

class CsvWriter extends BaseWriter
{
    public function writeHeader()
    {
        fputcsv($this->tmpFile, $this->fields);
    }

    public function write(array $data)
    {
        $this->exportedCount++;
        fputcsv($this->tmpFile, $data);
    }
}