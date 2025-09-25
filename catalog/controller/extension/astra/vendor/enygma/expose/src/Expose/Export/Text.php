<?php

namespace AstraPrefixed\Expose\Export;

class Text extends \AstraPrefixed\Expose\Export
{
    public function render()
    {
        $lines = array();
        $data = $this->getData();
        foreach ($data as $report) {
            $line = '';
            $line .= 'Variable: ' . $report->getVarName();
            $line .= ' | Value: ' . $report->getVarValue();
            $line .= ' | Path: ' . \json_encode($report->getVarPath());
            $line .= "\n########################\n";
            foreach ($report->getFilterMatch() as $filter) {
                $line .= "Description: (" . $filter->getId() . ") " . $filter->getDescription() . "\n";
                $line .= "Impact: " . $filter->getImpact();
                $line .= " | Tags: " . \implode(', ', $filter->getTags());
                $line .= "\n";
            }
            $lines[] = $line;
        }
        return \implode("\n", $lines);
    }
}
