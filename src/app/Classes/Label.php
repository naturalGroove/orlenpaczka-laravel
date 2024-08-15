<?php

namespace PatrykSawicki\OrlenPaczkaApi\app\Classes;

class Label extends Api
{
    /**
     * Get the label date in PDF format.
     * @param string $packCode
     * @return mixed
     */
    public function pdf(string $packCode)
    {
        $data = [
            'PackCode' => $packCode,
            'Format' => 'PDF'
        ];

        return $this->getLabelData($data);
    }

    /**
     * Get the label date in ZPL format.
     * @param string $packCode
     * @return mixed
     */
    public function zpl(string $packCode)
    {
        $data = [
            'PackCode' => $packCode,
            'Format' => 'ZPL'
        ];

        return $this->getLabelData($data);
    }

    /**
     * Get the label date in EPL format.
     * @param string $packCode
     * @return mixed
     */
    public function epl(string $packCode)
    {
        $data = [
            'PackCode' => $packCode,
            'Format' => 'EPL'
        ];

        return $this->getLabelData($data);
    }

    /**
     * Main method to get the label data.
     * @param array $data
     * @return mixed
     */
    protected function getLabelData(array $data)
    {
        $label = $this->postData('LabelPrintDuplicateTwo', $data, 'LabelPrintDuplicateTwoResult');

        if (!is_array($label) || empty($label)) {
            throw new \Exception('OrlanPaczka-Label: Error while generating label');
        }

        //return $label;
        return base64_decode($label['Label']);
    }
}
