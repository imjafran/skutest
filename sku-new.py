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

    def extract_values(self):
        """Separate required and optional attributes."""
        required_sets = []
        optional_sets = []

        for attribute in self.attributes:
            if attribute["enabled"]:
                values = [f"{v['value']}" for v in attribute["values"]]
                if attribute.get("required", False):
                    required_sets.append(values)
                else:
                    optional_sets.append(values)

        return required_sets, optional_sets

    def generate_combinations(self, required_sets, optional_sets):
        """Generate SKUs including required and optional attributes."""
        if not required_sets and not optional_sets:
            yield self.root_node  # No attributes available
            return

        # Always include all combinations of required attributes
        for required_combination in product(*required_sets if required_sets else [[]]):
            required_part = "-".join(required_combination)
            base_sku = f"{self.root_node}{required_part}" if required_part else self.root_node

            # If there are no optional attributes, yield required part alone
            if not optional_sets:
                yield base_sku
                continue

            # Generate combinations of optional attributes
            for subset_size in range(1, len(optional_sets) + 1):
                for subset in combinations(optional_sets, subset_size):
                    for optional_combination in product(*subset):
                        yield f"{base_sku}-{'-'.join(optional_combination)}"

            # Include just the required part (no optional attributes)
            if required_part:
                yield base_sku

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
limit_mb = 500  # Set the memory limit to 500 MB
threading.Thread(target=memory_monitor, args=(limit_mb,), daemon=True).start()

# Example usage
# Attributes setup
attributes = [
        {
            "name": "Size",
            "enabled": True,
            "required": True,
            "values": [
                {
                    "value": "3030",
                    "price": "1320"
                },
                {
                    "value": "3036",
                    "price": "1320"
                },
                {
                    "value": "3048",
                    "price": "1430"
                },
                {
                    "value": "3630",
                    "price": "1430"
                },
                {
                    "value": "3636",
                    "price": "1430"
                },
                {
                    "value": "3648",
                    "price": "1460"
                },
                {
                    "value": "4230",
                    "price": "1485"
                },
                {
                    "value": "4236",
                    "price": "1485"
                },
                {
                    "value": "4248",
                    "price": "1570"
                },
                {
                    "value": "4830",
                    "price": "1570"
                },
                {
                    "value": "4836",
                    "price": "1570"
                },
                {
                    "value": "4848",
                    "price": "1705"
                },
                {
                    "value": "5430",
                    "price": "1705"
                },
                {
                    "value": "5436",
                    "price": "1705"
                },
                {
                    "value": "5448",
                    "price": "1815"
                },
                {
                    "value": "6030",
                    "price": "1870"
                },
                {
                    "value": "6036",
                    "price": "1870"
                },
                {
                    "value": "6048",
                    "price": "1925"
                }
            ],
            "edit": 0
        },
        {
            "name": "Color",
            "enabled": True,
            "required": True,
            "values": [
                {
                    "value": "Raw",
                    "price": 0
                },
                {
                    "value": "AMW",
                    "price": "300"
                },
                {
                    "value": "ANW",
                    "price": "200"
                },
                {
                    "value": "BLK",
                    "price": "300"
                },
                {
                    "value": "NAV",
                    "price": "300"
                },
                {
                    "value": "CHO",
                    "price": "300"
                },
                {
                    "value": "ESP",
                    "price": "300"
                },
                {
                    "value": "GRY",
                    "price": "200"
                },
                {
                    "value": "LGRY",
                    "price": "200"
                },
                {
                    "value": "PRM",
                    "price": "100"
                },
                {
                    "value": "SAD",
                    "price": "300"
                },
                {
                    "value": "SGRY",
                    "price": "300"
                },
                {
                    "value": "WHT",
                    "price": "200"
                }
            ],
            "edit": 0
        },
        {
            "name": "Trim",
            "enabled": True,
            "required": True,
            "values": [
                {
                    "value": "CLT",
                    "price": 0
                },
                {
                    "value": "FLT",
                    "price": 0
                },
                {
                    "value": "BLT",
                    "price": 0
                }
            ],
            "edit": 0
        },
        {
            "name": "Trim Install",
            "enabled": True,
            "required": True,
            "values": [
                {
                    "value": "INT",
                    "price": 0
                },
                {
                    "value": "TRM",
                    "price": 0
                },
                {
                    "value": "STR",
                    "price": 0
                }
            ],
            "edit": 0
        },
        {
            "name": "Crown Molding",
            "enabled": True,
            "required": True,
            "values": [
                {
                    "value": "NCR",
                    "price": 0
                },
                {
                    "value": "INC",
                    "price": "280"
                },
                {
                    "value": "CLS",
                    "price": "210"
                }
            ],
            "edit": 0
        },
        {
            "name": "Depth",
            "enabled": True,
            "required": True,
            "values": [
                {
                    "value": "18",
                    "price": 0
                },
                {
                    "value": "ID19",
                    "price": "280"
                },
                {
                    "value": "ID20",
                    "price": "280"
                },
                {
                    "value": "ID22",
                    "price": "420"
                }
            ],
            "edit": 0
        },
        {
            "name": "Reduced Height",
            "enabled": True,
            "required": True,
            "values": [
                {
                    "value": "NRH",
                    "price": 0
                },
                {
                    "value": "RH1",
                    "price": "175"
                },
                {
                    "value": "RH2",
                    "price": "175"
                },
                {
                    "value": "RH3",
                    "price": "175"
                },
                {
                    "value": "RH4",
                    "price": "175"
                },
                {
                    "value": "RH5",
                    "price": "175"
                },
                {
                    "value": "RH6",
                    "price": "175"
                }
            ],
            "edit": 0
        },
        {
            "name": "Chimney Extension",
            "enabled": True,
            "required": True,
            "values": [
                {
                    "value": "NET",
                    "price": 0
                },
                {
                    "value": "6ET",
                    "price": "245"
                },
                {
                    "value": "12ET",
                    "price": "308"
                },
                {
                    "value": "24ET",
                    "price": "350"
                }
            ],
            "edit": 0
        },
        {
            "name": "Solid Bottom",
            "enabled": True,
            "required": True,
            "values": [
                {
                    "value": "NSB",
                    "price": 0
                },
                {
                    "value": "YSB",
                    "price": "280"
                }
            ],
            "edit": 0
        },
        {
            "name": "Rushed",
            "enabled": True,
            "required": True,
            "values": [
                {
                    "value": "NRSH",
                    "price": 0
                },
                {
                    "value": "RSH",
                    "price": "350"
                }
            ],
            "edit": 0
        }
    ]

options = {}

sku_generator = GenerateSku(attributes, options)

# Extract required and optional attribute values
required_sets, optional_sets = sku_generator.extract_values()

# Get the current timestamp
timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")

# Open a file to write the combinations with the timestamp in the filename
filename = f'output/python-sku.txt'
try:
    with open(filename, 'w') as file:
        for combination in sku_generator.generate_combinations(required_sets, optional_sets):
            current_time = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
            memory_usage = sku_generator.get_memory_usage()
            log_entry = f"{current_time} | {combination} | {memory_usage}\n"

            # Write to file
            file.write(log_entry)

            # Print to console
            print(log_entry.strip())
except MemoryError:
    print("Memory limit exceeded! Exiting the program.")
