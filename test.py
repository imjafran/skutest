import psutil
import resource
import os
from itertools import product, combinations
from datetime import datetime

class GenerateSku:
    def __init__(self, attributes, options):
        self.attributes = attributes
        self.options = options
        self.root_node = 'Test-'

    def __del__(self):
        del self.attributes
        del self.options

    def generate_combinations(self, sets):
        if not sets:
            yield self.root_node # Root Node
            return

        # Generate all possible non-empty subsets of sets
        for subset_size in range(1, len(sets) + 1):
            for subset in combinations(sets, subset_size):
                for combination in product(*subset):
                    yield self.root_node + '-'.join(combination)

    def get_memory_usage(self):
        # process = psutil.Process(os.getpid())
        # return f"{process.memory_info().rss / 1024 ** 2:.2f} MB"
        # Returns peak memory usage in MB
        peak_memory_kb = resource.getrusage(resource.RUSAGE_SELF).ru_maxrss
        return f"{peak_memory_kb / 1024:.2f} MB"

# Example usage
attributes = []
options = {}

sku_generator = GenerateSku(attributes, options)
sets = [
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
]

# Get the current timestamp
timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")

# Open a file to write the combinations with the timestamp in the filename
filename = f'python-combinations_{timestamp}.txt'
with open(filename, 'w') as file:
    for combination in sku_generator.generate_combinations(sets):
        current_time = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
        memory_usage = sku_generator.get_memory_usage()
        log_entry = f"{current_time} | {combination} | {memory_usage}\n"
        
        # Write to file
        file.write(log_entry)
        
        # Print to console
        print(log_entry.strip())