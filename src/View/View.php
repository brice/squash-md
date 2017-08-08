<?php

namespace View;

use League\CommonMark\CommonMarkConverter;

class View
{
    protected $return;

    /**
     * @param $data
     * @return $this
     */
    public function format(array $data)
    {
        $previousTableName = false;
        foreach ($data as $datum) {
            list($tableName,) = array_keys($datum);
            if (!$previousTableName ||$previousTableName != $tableName) {
                $this->addLine('# '.$tableName);
                $previousTableName = $tableName;
            }
            $this->addLine('* '.$datum[$tableName]);
        }
        return $this;
    }

    /**
     * @return mixed
     */
    public function write()
    {
        return $this->return;
    }

    /**
     * @return string
     */
    public function writeHTML()
    {
        $converter = new CommonMarkConverter();
        return $converter->convertToHtml($this->return);
    }

    /**
     * @param $line
     */
    private function addLine(string $line) {
        $this->return .= $line."\n";
    }
}