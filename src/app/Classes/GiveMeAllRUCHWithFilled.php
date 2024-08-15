<?php

namespace PatrykSawicki\OrlenPaczkaApi\app\Classes;

use Illuminate\Support\Facades\Cache;

class GiveMeAllRUCHWithFilled extends Api
{
    /**
     * Get the list of items.
     *
     * @return array
     */
    public function list(): array
    {
        $cacheName = 'op_' . strtolower(self::class);

        return Cache::remember($cacheName, config('op.cache_time'), function () {
            return $this->postData('GiveMeAllRUCHWithFilled')['PointPwR'];
        });
    }

    /**
     * Prepared items for our map.
     *
     * @return array
     */
    public function listForMap(): array
    {
        $cacheName = 'op_' . strtolower(self::class) . '_for-map';

        return Cache::remember($cacheName, config('op.cache_time'), function () {
            $data = [];

            $mapPointsList = $this->list();

            foreach ($mapPointsList as $mapPoint) {
                if (!filter_var($mapPoint['Available'], FILTER_VALIDATE_BOOLEAN)) {
                    continue;
                }

                $data[] = [
                    'id' => $mapPoint['DestinationCode'],
                    'lat' => $mapPoint['Latitude'],
                    'lang' => $mapPoint['Longitude'],
                    'title' => $mapPoint['DestinationCode'],
                    'description' => $mapPoint['ZipCode'] . ', ' . $mapPoint['City'] . ', ' . $mapPoint['StreetName'],
                    'OpeningHours' => $mapPoint['OpeningHours'] ?? '?',
                    'PointType' => $mapPoint['PointType'],
                    'location' => $mapPoint['DestinationCode'] . ' - ' . $mapPoint['ZipCode'] . ', ' . $mapPoint['City'] . ', ' . $mapPoint['StreetName'],
                    'location_details' => $mapPoint['Location'] ?? '',
                ];
            }

            return $data;
        });
    }
}
