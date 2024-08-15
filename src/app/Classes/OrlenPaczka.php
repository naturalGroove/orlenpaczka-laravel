<?php

namespace PatrykSawicki\OrlenPaczkaApi\app\Classes;

class OrlenPaczka
{
    public static function setCredentials(string $apiId, string $apiKey): void
    {
        Api::setCredentials($apiId, $apiKey);
    }

    public static function giveMeAllRUCHWithFilled(): GiveMeAllRUCHWithFilled
    {
        return new GiveMeAllRUCHWithFilled();
    }

    public static function generateLabelBusinessPack(): GenerateLabelBusinessPack
    {
        return new GenerateLabelBusinessPack();
    }
}