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
        "name" => "Color",
        "enabled" => true,
        "required" => true,
        "values" => [
            ["value" => "red", "price" => 10],
            ["value" => "yellow", "price" => 15],
            ["value" => "green", "price" => 20]
        ]
    ],
    [
        "name" => "Size",
        "enabled" => true,
        "required" => false,  // This is now a required attribute
        "values" => [
            ["value" => "XS", "price" => 5],
            ["value" => "SM", "price" => 10],
            ["value" => "MD", "price" => 15]
        ]
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
