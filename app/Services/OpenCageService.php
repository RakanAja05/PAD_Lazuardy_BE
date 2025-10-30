<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use OpenCage\Geocoder\Geocoder;

class OpenCageService
{

    protected $geocoder;
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        $apiKey = env("OPEN_CAGE_API_KEY");

        if(empty($apiKey)) throw new Exception("OpenCage API tidak ditemukan di file ENV");
        
        $this->geocoder = new Geocoder($apiKey);
    }

    public function fordwardGeocode(string $fullAddress, string $simplifiedAddress): ?array
    {
        Log::info("Geocoding: Primary attempt with full address: " . $fullAddress);
        $result = $this->makeGeocodeCall($fullAddress);

        if ($result)
        {
            $components = $result['results'][0]['components']??[];
            
            if(isset($components['_type']) && in_array($components['_type'], ['country', 'state', 'county', 'city']))
            {
                Log::warning("Hasil geocoding terlalu luas. Attempting fallback.");
            } else 
            {
                Log::info("Geocoding Primary attempt successful and specific.");
                return $this->extractCoordinates($result);
            }
        } else {
            Log::warning("Geocoding Primary attempt returned no results. Attempting fallback.");
        }

        Log::info("Geocoding: Fallback attempt with simplified address: " . $simplifiedAddress);
        $resultFallback = $this->makeGeocodeCall($simplifiedAddress);

        if($resultFallback)
        {
            Log::info("Geocoding Fallback attempt successful.");
            return $this->extractCoordinates($resultFallback);
        }
        Log::error("Gagal mengambil alamat");
        return null;
    }

    public function reverseGeocode(float $latitude, float $longitude)
    {
        $query = "{$latitude}, {$longitude}";
        $result = $this->geocoder->geocode($query);

        if ($result && $result['total_results'] > 0) {
            $first = $result['results'][0];
            return [
                'formatted_address' => $first['formatted'],
                'components' => $first['components']
            ];
        }

        return null;
    }

    public function makeGeocodeCall(string $query)
    {
        try{
            $response = $this->geocoder->geocode($query);
        }
        catch(Exception $e)
        {
            Log::error("OpenCage API Call Error for query '{$query}': " . $e->getMessage());
            return null;
        }

        if(isset($response['total_results'])&& $response['total_results'] > 0) return $response;
        return null;
    }

        private function extractCoordinates(array $result): array
    {
        $first = $result['results'][0];
        return [
            'latitude' => $first['geometry']['lat'],
            'longitude' => $first['geometry']['lng'],
            'formatted_address' => $first['formatted'],
        ];
    }
}
