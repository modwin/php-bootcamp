<?php

namespace WPROG2\S9_External_APIs\Ex9_1_Json_APIs\public;

class PolisenApiClient
{

    public function getEvents() : EventFilter
    {
        return new EventFilter();
    }
}