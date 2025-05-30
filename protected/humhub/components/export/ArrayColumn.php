<?php



namespace humhub\components\export;

/**
 * ArrayColumn exports array values to [[SpreadsheetExport]] widget.
 */
class ArrayColumn extends DataColumn
{
    /**
     * @inheritdoc
     */
    public function renderDataCellContent($model, $key, $index)
    {
        if ($this->content === null) {
            $value = $this->getDataCellValue($model, $key, $index);
            if (is_array($value)) {
                return $this->grid->formatter->format(implode(', ', $value), $this->format);
            }
        }

        return parent::renderDataCellContent($model, $key, $index);
    }
}
