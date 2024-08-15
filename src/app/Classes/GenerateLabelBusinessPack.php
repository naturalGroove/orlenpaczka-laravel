<?php

namespace PatrykSawicki\OrlenPaczkaApi\app\Classes;

class GenerateLabelBusinessPack extends Api
{
    /**
     * Get the list of items.
     * @param array $data
     * @return mixed
     */
    public function pdf(array $data)
    {
        $label = $this->postData('GenerateLabelBusinessPack', $data, 'LabelData');

        if (!is_array($label) || empty($label)) {
            throw new \Exception('OrlenPaczka-GenerateLabelBusinessPack: Error while generating label');
        }

        return base64_decode($label[0]);
    }
}
