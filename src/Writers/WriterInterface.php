<?php

namespace charmer\dataexporter\Writers;

interface WriterInterface
{
//    public function writeHeader(array $data);
    public function write(array $data);

    public function getFields();

    public function setFields(array $fields): void;
}