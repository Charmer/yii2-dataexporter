<?php

namespace charmer\dataexporter\Writers;

class JsonWriter extends BaseWriter
{
    public function initWriter()
    {
        fputs($this->tmpFile, '[');
    }

    public function closeWriter()
    {
        fputs($this->tmpFile, ']');
    }

    public function write(array $data)
    {
        fputs($this->tmpFile, json_encode($data));
        $this->exportedCount++;

        if ($this->exportedCount < $this->totalCount) {
            fputs($this->tmpFile, ',');
        }
    }
}