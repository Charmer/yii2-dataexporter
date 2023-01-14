<?php

namespace charmer\dataexporter\Writers;

abstract class BaseWriter implements WriterInterface
{
    protected mixed $tmpFile;
    protected array $fields = [];
    protected int $totalCount;
    protected int $exportedCount = 0;

    /**
     * @param mixed $tmpFile
     */
    public function setTmpFile(mixed $tmpFile): void
    {
        $this->tmpFile = $tmpFile;
    }

    /**
     * @return array
     */
    public function getFields(): array
    {
        return array_keys($this->fields);
    }

    /**
     * @param array $fields
     */
    public function setFields(array $fields): void
    {
        $this->fields = $fields;
    }

    /**
     * @param int $totalCount
     */
    public function setTotalCount(int $totalCount): void
    {
        $this->totalCount = $totalCount;
    }

    /**
     * @param int $exportedCount
     */
    public function setExportedCount(int $exportedCount): void
    {
        $this->exportedCount = $exportedCount;
    }

    /**
     * @return int
     */
    public function getExportedCount(): int
    {
        return $this->exportedCount;
    }

}