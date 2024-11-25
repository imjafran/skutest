<?php

ini_set('memory_limit', '500M');

class GenerateSku
{
    private $attributes;
    public $options;

    public function __construct($attributes)
    {
        $this->attributes = $attributes;
        $this->options = [
            'prefixName' => 'Test',
            'separator' => '-',
            'price' => false,
            'basePrice' => 0,
            'vat' => true,
            'vatType' => 'percentage',
            'vatAmount' => 50,
            'discount' => false,
            'discountType' => 'percentage',
            'discountAmount' => 10,
            'uppercase' => true,
        ];
    }

    public function __destruct()
    {
        unset($this->attributes);
    }

    // Generate combinations recursively considering required attributes and calculating price
    public function generateCombinations($sets, $requiredSets)
    {
        if (empty($sets)) {
            yield $this->options['prefixName'];
            return;
        }

        // Handle combinations
        for ($subsetSize = 1; $subsetSize <= count($sets); $subsetSize++) {
            foreach ($this->getSubsets($sets, $subsetSize) as $subset) {
                foreach ($this->cartesianProduct($subset) as $combination) {
                    // Ensure required attributes are included in every combination
                    if ($this->containsRequiredAttributes($combination, $requiredSets)) {
                        yield [ $this->options['prefixName'] . $this->options['separator'] . implode( $this->options['separator'], $combination)];
                    }
                }
            }
        }
    }

    // Check if combination contains all required attributes
    private function containsRequiredAttributes($combination, $requiredSets)
    {
        foreach ($requiredSets as $requiredSet) {
            $found = false;
            foreach ($combination as $item) {
                if (in_array($item, $requiredSet)) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                return false; // If any required set is missing, return false
            }
        }
        return true; // All required attributes are found in the combination
    }

    // Generate all subsets of a given size
    private function getSubsets($sets, $size)
    {
        $result = [];
        $indexes = range(0, count($sets) - 1);
        foreach ($this->combinations($indexes, $size) as $combination) {
            $subset = [];
            foreach ($combination as $index) {
                $subset[] = $sets[$index];
            }
            $result[] = $subset;
        }
        return $result;
    }

    // Generate combinations of indexes
    private function combinations($array, $size)
    {
        $results = [];
        $this->combineRecursive($array, [], $size, $results);
        return $results;
    }

    private function combineRecursive($array, $temp, $size, &$results)
    {
        if ($size == 0) {
            $results[] = $temp;
            return;
        }

        for ($i = 0; $i < count($array); $i++) {
            $newTemp = $temp;
            $newTemp[] = $array[$i];
            $remaining = array_slice($array, $i + 1);
            $this->combineRecursive($remaining, $newTemp, $size - 1, $results);
        }
    }

   // Optimize the `cartesianProduct` method
    private function cartesianProduct($arrays)
    {
        if (empty($arrays)) {
            yield [];
            return;
        }

        $firstArray = array_shift($arrays); // Take the first array
        foreach ($firstArray as $value) {
            foreach ($this->cartesianProduct($arrays) as $product) {
                yield array_merge([$value], $product);
            }
        }
    }

   

    // Get memory usage in MB
    public function getMemoryUsage()
    {
        return round(memory_get_usage(true) / 1024 / 1024, 2) . " MB";
    }
}

// Attribute sets (example data)
$attributes = [
    [
        "name" => "Trim Install",
        "enabled" => true,
        "required" => true,
        "values" => [
            ["value" => "INT", "price" => 0],
            ["value" => "TRM", "price" => 0],
            ["value" => "STR", "price" => 0]
        ],
    ],
    [
        "name" => "Crown Molding",
        "enabled" => true,
        "required" => true,
        "values" => [
            ["value" => "NCR", "price" => 0],
            ["value" => "INC", "price" => "280"],
            ["value" => "CLS", "price" => "210"]
        ],
    ],
    [
        "name" => "Depth",
        "enabled" => true,
        "required" => true,
        "values" => [
            ["value" => "18", "price" => 0],
            ["value" => "ID19", "price" => "280"],
            ["value" => "ID20", "price" => "280"],
            ["value" => "ID22", "price" => "420"]
        ],
    ],
    [
        "name" => "Reduced Height",
        "enabled" => true,
        "required" => true,
        "values" => [
            ["value" => "NRH", "price" => 0],
            ["value" => "RH1", "price" => "175"],
            ["value" => "RH2", "price" => "175"],
            ["value" => "RH3", "price" => "175"],
            ["value" => "RH4", "price" => "175"],
            ["value" => "RH5", "price" => "175"],
            ["value" => "RH6", "price" => "175"]
        ],
    ],
    [
        "name" => "Chimney Extension",
        "enabled" => true,
        "required" => true,
        "values" => [
            ["value" => "NET", "price" => 0],
            ["value" => "6ET", "price" => "245"],
            ["value" => "12ET", "price" => "308"],
            ["value" => "24ET", "price" => "350"]
        ],
    ],
    [
        "name" => "Solid Bottom",
        "enabled" => true,
        "required" => true,
        "values" => [
            ["value" => "NSB", "price" => 0],
            ["value" => "YSB", "price" => "280"]
        ],
    ],
    [
        "name" => "Rushed",
        "enabled" => true,
        "required" => true,
        "values" => [
            ["value" => "NRSH", "price" => 0],
            ["value" => "RSH", "price" => "350"]
        ],
    ]
];

// Extract enabled attributes and their values
$sets = [];
$requiredSets = [];
foreach ($attributes as $attribute) {
    if ($attribute['enabled']) {
        $values = array_column($attribute['values'], 'value'); // Extract 'value'
        if (!empty($values)) {
            $sets[] = $values;

            // Handle required attributes
            if ($attribute['required']) {
                $requiredSets[] = $values;
            }
        }
    }
}

// File setup

$json_file = fopen("output/php-sku.json", 'w');
$csv_file = fopen("output/php-sku.csv", 'w');

if (!$json_file || !$csv_file) {
    die("Unable to create output file.\n");
}

// Generate combinations and write to file
$skuGenerator = new GenerateSku($attributes);

// Write header
// $header = "SKU, Subtotal, Total Price, VAT, Discount, Memory Usage\n";

fwrite($json_file, '[');
fwrite($csv_file, "SKU, Memory Usage\n");

foreach ($skuGenerator->generateCombinations($sets, $requiredSets) as $combination) {
    $sku = $skuGenerator->options['uppercase'] ? strtoupper($combination[0]) : strtolower($combination[0]);
   
    $memory_usage = $skuGenerator->getMemoryUsage();

    // Write in JSON format
    $json_object = json_encode([
        'SKU' => $sku,
        'Memory Usage' => $memory_usage
    ]) . ",\n";
    fwrite($json_file, $json_object);
    fflush($json_file); // Flush the file buffer

    // Write in CSV format
    $csv_object = $sku . ',' . $memory_usage . "\n";
    fwrite($csv_file, $csv_object);
    fflush($csv_file); // Flush the file buffer

    // Explicitly unset variables to free memory
    unset($sku, $memory_usage, $json_object, $csv_object, $combination);
}

// Truncate the trailing comma and newline
fseek($json_file, -2, SEEK_END); // Move back 2 characters to overwrite ",\n"
fwrite($json_file, "]");
fflush($json_file);

fclose($json_file);
fclose($csv_file);

echo "Combinations written\n";
