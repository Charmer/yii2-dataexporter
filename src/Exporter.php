<?php

namespace charmer\dataexporter;

use charmer\dataexporter\Writers\WriterInterface;
use yii\data\DataProviderInterface;
use yii\data\Pagination;
use yii\grid\GridView;

class Exporter
{
    private mixed $tmpFile;
    private WriterInterface $writer;
    private DataProviderInterface $dataProvider;

    private int $pageCount;
    private Pagination $pagination;

    public function initTmpFile(): void
    {
        $this->tmpFile = tmpfile();
    }

    public function getTmpFileMetaData(): array
    {
        return stream_get_meta_data($this->tmpFile);
    }

    public function closeTmpFile(): void
    {
        fclose($this->tmpFile);
    }

    public function setWriter(WriterInterface $writer)
    {
        $this->writer = $writer;
    }

    /**
     * @param DataProviderInterface $dataProvider
     */
    public function setDataProvider(DataProviderInterface $dataProvider): void
    {
        $this->dataProvider = $dataProvider;
    }

    /**
     * @return int
     */
    public function getPageCount(): int
    {
        return $this->pageCount;
    }

    public function prepare(): void
    {
        $this->writer->setTmpFile($this->tmpFile);
        $this->pagination = $this->dataProvider->getPagination();
        $this->pagination->totalCount = $this->dataProvider->getTotalCount();
        $this->pageCount = $this->pagination->getPageCount();
        $this->writer->setTotalCount($this->dataProvider->getTotalCount());
        $this->dataProvider->prepare();

        if (method_exists($this->writer::class, 'initWriter')) {
            $this->writer->initWriter();
        }
    }

    public function export(): void
    {
        if (method_exists($this->writer::class, 'writeHeader')) {
            $this->writer->writeHeader();
        }

        for ($i=0; $i < $this->getPageCount(); $i++) {
            $this->pagination->setPage($i);
            $this->dataProvider->refresh();

            foreach ($this->dataProvider->getModels() as $model){
                $this->writer->write($model->getAttributes($this->writer->getFields()));
            }
        }

        if (method_exists($this->writer::class, 'closeWriter')) {
            $this->writer->closeWriter();
        }
    }
}