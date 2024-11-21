<?php

class GenerateSku
{
    private $attributes;
    private $rootNode;

    public function __construct($attributes)
    {
        $this->attributes = $attributes;
        $this->rootNode = 'Test-';
    }

    public function __destruct()
    {
        unset($this->attributes);
    }

    // Generate combinations recursively considering required attributes
    public function generateCombinations($sets, $requiredSets)
    {
        if (empty($sets)) {
            yield $this->rootNode;
            return;
        }

        // Handle combinations
        for ($subsetSize = 1; $subsetSize <= count($sets); $subsetSize++) {
            foreach ($this->getSubsets($sets, $subsetSize) as $subset) {
                foreach ($this->cartesianProduct($subset) as $combination) {
                    // Ensure required attributes are included in every combination
                    if ($this->containsRequiredAttributes($combination, $requiredSets)) {
                        yield $this->rootNode . implode('-', $combination);
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
            ["value" => "red", "price" => 0],
            ["value" => "yellow", "price" => 0],
            ["value" => "green", "price" => 0]
        ]
    ],
    [
        "name" => "Size",
        "enabled" => true,
        "required" => true,  // This is now a required attribute
        "values" => [
            ["value" => "XS", "price" => 0],
            ["value" => "SM", "price" => 0],
            ["value" => "MD", "price" => 0]
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
foreach ($skuGenerator->generateCombinations($sets, $requiredSets) as $combination) {
    $currentTime = date("Y-m-d H:i:s");
    $memoryUsage = $skuGenerator->getMemoryUsage();
    $logEntry = "$currentTime | $combination | $memoryUsage" . PHP_EOL;

    fwrite($file, $logEntry);

    // Optional: Print to console for every 1000 combinations
    static $count = 0;
    if (++$count % 1000 === 0) {
        echo $logEntry;
    }
}

fclose($file);

echo "Combinations written to $filename\n";
