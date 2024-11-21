import itertools
import datetime

class GenerateSku:
    def __init__(self, attributes):
        self.attributes = attributes
        self.options = {
            'prefixName': 'Test',
            'separator': '-',
            'price': True,
            'basePrice': 0,
            'vat': True,
            'vatType': 'percentage',
            'vatAmount': 50,
            'discount': False,
            'discountType': 'percentage',
            'discountAmount': 10,
            'uppercase': True,
        }

    def generate_combinations(self, sets, required_sets):
        if not sets:
            yield self.options['prefixName']
            return

        # Handle combinations
        for subset_size in range(1, len(sets) + 1):
            for subset in self.get_subsets(sets, subset_size):
                for combination in self.cartesian_product(subset):
                    # Ensure required attributes are included in every combination
                    if self.contains_required_attributes(combination, required_sets):
                        # Calculate price of the combination
                        price = self.calculate_price(combination)
                        yield [
                            self.options['prefixName'] + self.options['separator'] + self.options['separator'].join(combination),
                            price
                        ]

    def contains_required_attributes(self, combination, required_sets):
        for required_set in required_sets:
            found = False
            for item in combination:
                if item in required_set:
                    found = True
                    break
            if not found:
                return False  # If any required set is missing, return false
        return True  # All required attributes are found in the combination

    def get_subsets(self, sets, size):
        result = []
        indexes = range(len(sets))
        for combination in self.combinations(indexes, size):
            subset = [sets[i] for i in combination]
            result.append(subset)
        return result

    def combinations(self, array, size):
        return list(itertools.combinations(array, size))

    def cartesian_product(self, arrays):
        if not arrays:
            return [[]]
        result = [[]]
        for array in arrays:
            new_result = []
            for product in result:
                for item in array:
                    new_result.append(product + [item])
            result = new_result
        return result

    def calculate_price(self, combination):
        subtotal = self.options['basePrice']
        
        # Add the prices of selected attribute values
        for value in combination:
            for attribute in self.attributes:
                for attribute_value in attribute['values']:
                    if attribute_value['value'] == value and 'price' in attribute_value:
                        subtotal += attribute_value['price']
        
        # Apply discount if enabled
        discount = 0
        if self.options['discount']:
            if self.options['discountType'] == 'percentage':
                discount = (self.options['discountAmount'] / 100) * subtotal
            else:  # Fixed amount
                discount = self.options['discountAmount']
        
        # Apply VAT if enabled
        vat = 0
        if self.options['vat']:
            if self.options['vatType'] == 'percentage':
                vat = ((subtotal - discount) * self.options['vatAmount']) / 100
            else:  # Fixed amount
                vat = self.options['vatAmount']
        
        # Calculate the final total price
        total_price = subtotal - discount + vat
        
        return {
            'subtotal': subtotal,
            'discount': discount,
            'vat': vat,
            'totalPrice': total_price
        }

    def get_memory_usage(self):
        return f"{self.get_memory_in_mb()} MB"

    def get_memory_in_mb(self):
        # Using a mock function to simulate memory usage in MB.
        return 5  # Example fixed memory usage

# Attribute sets (example data)
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

# Extract enabled attributes and their values
sets = []
required_sets = []
for attribute in attributes:
    if attribute['enabled']:
        values = [v['value'] for v in attribute['values']]  # Extract 'value'
        if values:
            sets.append(values)

            # Handle required attributes
            if attribute['required']:
                required_sets.append(values)

# File setup
timestamp = datetime.datetime.now().strftime("%Y%m%d_%H%M%S")
filename = f"combinations_{timestamp}.txt"

# Create and open file
with open(filename, 'w') as file:
    # Write header
    file.write("SKU, Subtotal, Total Price, VAT, Discount, Memory Usage\n")
    
    # Create SKU Generator
    sku_generator = GenerateSku(attributes)
    
    # Generate combinations and write to file
    for combination in sku_generator.generate_combinations(sets, required_sets):
        sku = combination[0].upper() if sku_generator.options['uppercase'] else combination[0].lower()
        price_details = combination[1]
        
        log_entry = f"{sku}, {price_details['subtotal']}, {price_details['totalPrice']}, {price_details['vat']}, {price_details['discount']}, {sku_generator.get_memory_usage()}\n"
        file.write(log_entry)

print(f"Combinations written to {filename}")
