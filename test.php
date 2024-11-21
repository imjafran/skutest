<?php

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
            'price' => true,
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
                        // Calculate price of the combination
                        $price = $this->calculatePrice($combination);
                        yield [ $this->options['prefixName'] . $this->options['separator'] . implode( $this->options['separator'], $combination), $price];
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

    // Cartesian product of multiple arrays
    private function cartesianProduct($arrays)
    {
        if (empty($arrays)) {
            return [[]];
        }

        $result = [[]];
        foreach ($arrays as $array) {
            $newResult = [];
            foreach ($result as $product) {
                foreach ($array as $item) {
                    $newResult[] = array_merge($product, [$item]);
                }
            }
            $result = $newResult;
        }
        return $result;
    }

   // Calculate the price of the SKU based on the selected values
   private function calculatePrice($combination)
   {
       // Step 1: Start with the base price
       $subtotal = $this->options['basePrice'];
   
       // Step 2: Add the prices of selected attribute values
       foreach ($combination as $value) {
           foreach ($this->attributes as $attribute) {
               foreach ($attribute['values'] as $attributeValue) {
                   if ($attributeValue['value'] === $value && isset($attributeValue['price'])) {
                       $subtotal += $attributeValue['price'];
                   }
               }
           }
       }
   
       // Step 3: Apply discount if enabled
       $discount = 0;
       if ($this->options['discount']) {
           if ($this->options['discountType'] === 'percentage') {
               $discount = ($this->options['discountAmount'] / 100) * $subtotal;
           } else { // Fixed amount
               $discount = $this->options['discountAmount'];
           }
       }
   
       // Step 4: Apply VAT if enabled
       $vat = 0;
       if ($this->options['vat']) {
           if ($this->options['vatType'] === 'percentage') {
               $vat = (($subtotal - $discount) * $this->options['vatAmount']) / 100;
           } else { // Fixed amount
               $vat = $this->options['vatAmount'];
           }
       }
   
       // Step 5: Calculate the final total price
       $totalPrice = $subtotal - $discount + $vat;
   
       return [
           'subtotal' => $subtotal,
           'discount' => $discount,
           'vat' => $vat,
           'totalPrice' => $totalPrice
       ];
   }
   

    // Get memory usage in MB
    public function getMemoryUsage()
    {
        return round(memory_get_peak_usage(true) / 1024 / 1024, 2) . " MB";
    }
}

// Attribute sets (example data)
$attributes = [
    [
        "name" => "Size",
        "enabled" => true,
        "required" => true,
        "values" => [
            ["value" => "3030", "price" => "1320"],
            ["value" => "3036", "price" => "1320"],
            ["value" => "3048", "price" => "1430"],
            ["value" => "3630", "price" => "1430"],
            ["value" => "3636", "price" => "1430"],
            ["value" => "3648", "price" => "1460"],
            ["value" => "4230", "price" => "1485"],
            ["value" => "4236", "price" => "1485"],
            ["value" => "4248", "price" => "1570"],
            ["value" => "4830", "price" => "1570"],
            ["value" => "4836", "price" => "1570"],
            ["value" => "4848", "price" => "1705"],
            ["value" => "5430", "price" => "1705"],
            ["value" => "5436", "price" => "1705"],
            ["value" => "5448", "price" => "1815"],
            ["value" => "6030", "price" => "1870"],
            ["value" => "6036", "price" => "1870"],
            ["value" => "6048", "price" => "1925"]
        ],
    ],
    [
        "name" => "Color",
        "enabled" => true,
        "required" => true,
        "values" => [
            ["value" => "Raw", "price" => 0],
            ["value" => "AMW", "price" => "300"],
            ["value" => "ANW", "price" => "200"],
            ["value" => "BLK", "price" => "300"],
            ["value" => "NAV", "price" => "300"],
            ["value" => "CHO", "price" => "300"],
            ["value" => "ESP", "price" => "300"],
            ["value" => "GRY", "price" => "200"],
            ["value" => "LGRY", "price" => "200"],
            ["value" => "PRM", "price" => "100"],
            ["value" => "SAD", "price" => "300"],
            ["value" => "SGRY", "price" => "300"],
            ["value" => "WHT", "price" => "200"]
        ],
    ],
    [
        "name" => "Trim",
        "enabled" => true,
        "required" => true,
        "values" => [
            ["value" => "CLT", "price" => 0],
            ["value" => "FLT", "price" => 0],
            ["value" => "BLT", "price" => 0]
        ],
    ],
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
$timestamp = date("Ymd_His");
$filename = "combinations.txt";
$file = fopen($filename, 'w');

if (!$file) {
    die("Unable to create output file.\n");
}

// Generate combinations and write to file
$skuGenerator = new GenerateSku($attributes);

// Write header
$header = "SKU, Subtotal, Total Price, VAT, Discount, Memory Usage\n";
fwrite($file, $header);

foreach ($skuGenerator->generateCombinations($sets, $requiredSets) as $combination) {
    $sku = $skuGenerator->options['uppercase'] ? strtoupper($combination[0]) : strtolower($combination[0]);
    $priceDetails = $combination[1];

    $logEntry = sprintf(
        "%s, %s, %s, %s, %s, %s\n",
        $sku,
        $priceDetails['subtotal'],
        $priceDetails['totalPrice'],
        $priceDetails['vat'],
        $priceDetails['discount'],
        $skuGenerator->getMemoryUsage()
    );
    fwrite($file, $logEntry);
}




fclose($file);

echo "Combinations written to $filename\n";
