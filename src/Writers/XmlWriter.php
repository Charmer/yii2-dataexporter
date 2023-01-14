<?php

namespace charmer\dataexporter\Writers;

class XmlWriter extends BaseWriter
{
    public function initWriter()
    {
        fputs($this->tmpFile, '<?xml version="1.0" encoding="UTF-8"?><items>');
    }

    public function closeWriter()
    {
        fputs($this->tmpFile, '</items>');
    }

    public function write(array $data)
    {
        $row = '<item>';

        foreach ($data as $key => $value) {
            $row .= '<'.$key.'>'.$value.'</'.$key.'>';
        }

        $row .= "</item>";

        fputs($this->tmpFile, $row);
    }
}