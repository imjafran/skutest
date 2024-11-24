import psutil
import os
import threading
import time
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
            yield self.root_node  # Root Node
            return

        # Generate all possible non-empty subsets of sets
        for subset_size in range(1, len(sets) + 1):
            for subset in combinations(sets, subset_size):
                for combination in product(*subset):
                    yield self.root_node + '-'.join(combination)

    def get_memory_usage(self):
        process = psutil.Process(os.getpid())
        return f"{process.memory_info().rss / 1024 ** 2:.2f} MB"


def memory_monitor(limit_mb):
    """Monitor memory usage and terminate the program if it exceeds the limit."""
    process = psutil.Process(os.getpid())
    limit_bytes = limit_mb * 1024 * 1024
    while True:
        mem_usage = process.memory_info().rss
        if mem_usage > limit_bytes:
            print(f"Memory limit exceeded: {mem_usage / 1024**2:.2f} MB (Limit: {limit_mb} MB)")
            os._exit(1)  # Forcefully terminate the program
        time.sleep(1)  # Check memory usage every second


# Start memory monitor in a separate thread
limit_mb = 50  # Set the memory limit to 500 MB
threading.Thread(target=memory_monitor, args=(limit_mb,), daemon=True).start()


# Example usage
attributes = []
options = {}

sku_generator = GenerateSku(attributes, options)
sets = [
    ["3030", "3036", "3048", "3630", "3636", "3648", "4230", "4236", "4248", "4830", "4836", "4848", "5430", "5436", "5448", "6030", "6036", "6048"],
    ["Raw", "AMW", "ANW", "BLK", "NAV", "CHO", "ESP", "GRY", "LGRY", "PRM", "SAD", "SGRY", "WHT"],
    ["CLT", "FLT", "BLT"],
    ["INT", "TRM", "STR"],
    ["NCR", "INC", "CLS"],
    ["18", "ID19", "ID20", "ID22"],
    ["NRH", "RH1", "RH2", "RH3", "RH4", "RH5", "RH6"],
    ["NET", "6ET", "12ET", "24ET"],
    ["NSB", "YSB"],
    ["NRSH", "RSH"]
]

# Get the current timestamp
timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")

# Open a file to write the combinations with the timestamp in the filename
filename = f'output/python-sku.txt'
try:
    with open(filename, 'w') as file:
        for combination in sku_generator.generate_combinations(sets):
            current_time = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
            memory_usage = sku_generator.get_memory_usage()
            log_entry = f"{current_time} | {combination} | {memory_usage}\n"

            # Write to file
            file.write(log_entry)

            # Print to console
            print(log_entry.strip())
except MemoryError:
    print("Memory limit exceeded! Exiting the program.")
