<?php

namespace App\Services;

use App\Exceptions\CsvReadingException;
use Illuminate\Support\Facades\Http;
use App\Exceptions\UnauthorizedTokenException;
use Illuminate\Http\Client\RequestException;
use App\Exceptions\RateLimitException;
use App\Exceptions\ServerException;
use App\Models\AccessToken;
use Illuminate\Http\Client\HttpClientException;

/**
 * This class is responsible for reading csv files. It will get file path and return data in array.
 * Purpose to make this class is to keep CSV reading logic independent.
 * So in our project if we want to change CSV reading mechanism
 * (by using any package or something) we can do it easily without affecting project.
 */
class CsvReaderService
{
    /**
     * @throws CsvReadingException
     */
    public  function getData(string $productsCsvFilePath): array
    {
        $csvData = [];

        $standardFields = [
            "title",
            "ean",
            "asin",
            "sku",
            "purchase_price"
        ];

        $csvFileRecords = array_map('str_getcsv', file($productsCsvFilePath));

        if (!count($csvFileRecords) > 0) {
            activity()->causedBy(auth()->user())->log("user tried to import empty csv file. in " . __CLASS__ . " on line " . __LINE__);
            throw new CsvReadingException("No data in csv file.");
        }

        // Get field names from header column
        $fields = array_map('strtolower', $csvFileRecords[0]);

        if ($fields !== $standardFields) {
            activity()->causedBy(auth()->user())->log("user is trying to upload an invalid CSV. in " . __CLASS__ . " on line " . __LINE__);
            throw new CsvReadingException("csv is not valid. Header fields does not match with sample csv.");
        }

        // Remove the header column
        array_shift($csvFileRecords);

        foreach ($csvFileRecords as $record) {
            // Decode unwanted html entities
            $record = array_map("html_entity_decode", $record);

            // Set the field name as key
            $record = array_combine($fields, $record);
            $record['purchase_price'] = make_integer($record['purchase_price']);
            // Get the clean data
            $csvData[] = $this->clearEncodingString($record);
        }
        return $csvData;
    }

    private function clearEncodingString($value): array
    {
        // cleaning up record to handle any special characters.
        if (is_array($value)) {
            $clean = [];
            foreach ($value as $key => $val) {
                $clean[$key] = mb_convert_encoding($val, 'UTF-8', 'UTF-8');
            }
            return $clean;
        }
        return mb_convert_encoding($value, 'UTF-8', 'UTF-8');
    }
}
