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

    public static function label(): Label
    {
        return new Label();
    }

    public static function pack(): Pack
    {
        return new Pack();
    }
}
