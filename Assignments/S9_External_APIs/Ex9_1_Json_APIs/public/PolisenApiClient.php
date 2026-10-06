<?php

namespace WPROG2\S9_External_APIs\Ex9_API_Supplier\public;

class PolisenApiClient
{

    public function getEvents() : EventFilter
    {
        return new EventFilter();
    }
}