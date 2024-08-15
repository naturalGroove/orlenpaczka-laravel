<?php

namespace PatrykSawicki\OrlenPaczkaApi\app\Classes;

class Pack extends Api
{
    /**
     * Get the list of parcels.
     * @param array $data
     * @return mixed
     */
    public function get(array $data)
    {
        $packData = $this->postData('', $data, null, true);

        if (!is_array($packData) || empty($packData)) {
            throw new \Exception('OrlenPaczka-GetGeneratedParcels: Error while getting parcels');
        }

        return $packData;
    }

    /**
     * Get the status of the pack.
     * @param string $packCode
     * @return mixed
     */
    public function status(string $packCode)
    {
        $packStatus = $this->postData('GiveMePackStatus', [
            'PackCode' => $packCode
        ], null, true);

        if (!is_array($packStatus) || empty($packStatus)) {
            throw new \Exception('OrlenPaczka-GiveMePackStatus: Error while getting pack status');
        }

        return $packStatus;
    }

    /**
     * Get the full history of the pack.
     * @param string $packCode
     * @return mixed
     */
    public function statusHistory(string $packCode)
    {
        $packStatus = $this->postData('GiveMePackStatusFullHistory', [
            'PackCode' => $packCode
        ], null, true);

        if (!is_array($packStatus) || empty($packStatus)) {
            throw new \Exception('OrlenPaczka-GiveMePackStatusFullHistory: Error while getting full status history of pack');
        }

        return $packStatus;
    }

    /**
     * Get the status of the pack.
     * @param string $packCode
     * @return mixed
     */
    public function listStatus(array $packCode)
    {
        $packStatusList = $this->postData('GiveMePackStatusList', [
            'PackCode' => $packCode
        ], null, true);

        if (!is_array($packStatusList) || empty($packStatusList)) {
            throw new \Exception('OrlenPaczka-GiveMePackStatus: Error while getting pack status');
        }

        return $packStatusList;
    }

    /**
     * Get the list of parcels.
     * @param array $data
     * @return mixed
     */
    public function listParcels(array $data)
    {
        $parcels = $this->postData('GetGeneratedParcels', $data, null, true);

        if (!is_array($parcels) || empty($parcels)) {
            throw new \Exception('OrlenPaczka-GetGeneratedParcels: Error while getting parcels');
        }

        return $parcels;
    }

    /**
     * Generate the business pack.
     * @param array $data
     * @return mixed
     */
    public function generate(array $data)
    {
        $packData = $this->postData('GenerateBusinessPack', $data, null, true);

        if (!is_array($packData) || empty($packData)) {
            throw new \Exception('OrlenPaczka-GenerateBusinessPack: Error while generating pack');
        }

        return $packData['GenerateBusinessPack'];
    }

    /**
     * Generate the business pack.
     * @param array $data
     * @return mixed
     */
    public function fakeGenerate(array $data)
    {
        $packData = $this->fakePostData('GenerateBusinessPack', $data, null, true);

        if (!is_array($packData) || empty($packData)) {
            throw new \Exception('OrlenPaczka-GenerateBusinessPack: Error while generating pack');
        }

        return $packData['GenerateBusinessPack'];
    }


    /**
     * Generate the business pack.
     * @param array $data
     * @return mixed
     */
    public function generateAllegro(array $data)
    {
        $packData = $this->postData('GenerateLabelBusinessPackAllegro', $data, null, true);

        if (!is_array($packData) || empty($packData)) {
            throw new \Exception('OrlenPaczka-GenerateLabelBusinessPackAllegro: Error while generating pack');
        }

        return $packData;
    }

    /**
     * Cancel the business pack.
     * @param array $data
     * @return mixed
     */
    public function cancel(string $packCode)
    {
        $packData = $this->postData('PutCustomerPackCanceled', [
            'PackCode' => $packCode
        ], null, true);

        if (!is_array($packData) || empty($packData)) {
            throw new \Exception('OrlenPaczka-PutCustomerPackCanceled: Error while cancelling pack');
        }

        return $packData;
    }
}
