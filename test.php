<?php

class GenerateSku
{
    private $attributes;
    private $options;
    private $rootNode;

    public function __construct($attributes = [], $options = [])
    {
        $this->attributes = $attributes;
        $this->options = $options;
        $this->rootNode = 'Test-';
    }

    public function __destruct()
    {
        unset($this->attributes, $this->options);
    }

    // Generate combinations recursively
    public function generateCombinations($sets)
    {
        if (empty($sets)) {
            yield $this->rootNode;
            return;
        }

        for ($subsetSize = 1; $subsetSize <= count($sets); $subsetSize++) {
            foreach ($this->getSubsets($sets, $subsetSize) as $subset) {
                foreach ($this->cartesianProduct($subset) as $combination) {
                    yield $this->rootNode . implode('-', $combination);
                }
            }
        }
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
        // return round(memory_get_usage(true) / 1024 / 1024, 2) . " MB";

        // Return pick memory usage in MB.
        return round(memory_get_peak_usage(true) / 1024 / 1024, 2) . " MB";
    }
}

// Data (same as Python example)
$sets = [
    ['Size330', 'Size336', 'Size348', 'Size3630', 'Size3636', 'Size3648', 'Size4230', 'Size4236', 'Size4248', 'Size4830', 'Size4836', 'Size4848', 'Size5430', 'Size5436', 'Size5448', 'Size6030', 'Size6036', 'Size6048'],
    ['ColorAMW', 'ColorBLK', 'ColorNAV', 'ColorPRM', 'ColorANW', 'ColorCHO', 'ColorESP', 'ColorGRY', 'ColorLGRY', 'ColorSAD', 'ColorSGRY', 'ColorWHT'],
    ['TrimCLT', 'TrimFLT', 'TrimBLT'],
    ['Trim InstallINT', 'Trim InstallTRM', 'Trim InstallSTR'],
    ['Crown MoldingNCR', 'Crown MoldingINC', 'Crown MoldingCLS'],
    ['DepthID19', 'DepthID20', 'DepthID22'],
    ['Reduced HeightRH1', 'Reduced HeightRH2', 'Reduced HeightRH3', 'Reduced HeightRH4', 'Reduced HeightRH5', 'Reduced HeightRH6'],
    ['Chimney Extension6ET', 'Chimney Extension12ET', 'Chimney Extension24ET'],
    ['Solid BottomYSB'],
    ['RushedRSH']
];

// File setup
$timestamp = date("Ymd_His");
$filename = "php-combinations_$timestamp.txt";
$file = fopen($filename, 'w');

if (!$file) {
    die("Unable to create output file.\n");
}

// Generate combinations and write to file
$skuGenerator = new GenerateSku();
foreach ($skuGenerator->generateCombinations($sets) as $combination) {
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
