
## How to Run

### Python

1. Create and activate a virtual environment:
    ```sh
    python -m venv venv
    source venv/bin/activate  # On Windows use `venv\Scripts\activate`
    ```

2. Install dependencies:
    ```sh
    pip install -r requirements.txt
    ```

3. Run the SKU generator:
    ```sh
    python sku.py
    ```

4. Run the price calculator:
    ```sh
    python price.py
    ```

### PHP

1. Ensure PHP is installed on your system.

2. Run the SKU generator:
    ```sh
    php sku.php
    ```

3. Run the price calculator:
    ```sh
    php price.php
    ```

## Files

- [`data/data.json`](data/data.json ): Contains attribute data for SKU generation.
- [`output`](output ): Directory where output files are saved.
- [`price.php`](price.php ): PHP script for generating SKUs and calculating prices.
- [`price.py`](price.py ): Python script for generating SKUs and calculating prices.
- [`sku-new.py`](sku-new.py ): Updated Python script for generating SKUs.
- [`sku.php`](sku.php ): PHP script for generating SKUs.
- [`sku.py`](sku.py ): Python script for generating SKUs.

## Classes and Methods

### Python

- [`GenerateSku`](price.py ) class in [`sku.py`](sku.py ), [`sku-new.py`](sku-new.py ), and [`price.py`](price.py ):
    - [`__init__(self, attributes, options)`](price.py ): Initializes the SKU generator.
    - [`generate_combinations(self, sets, required_sets)`](price.py ): Generates SKU combinations.
    - [`calculate_price(self, combination)`](price.py ): Calculates the price of a combination.
    - [`get_memory_usage(self)`](sku-new.py ): Returns the memory usage.

### PHP

- [`GenerateSku`](price.py ) class in [`sku.php`](sku.php ) and [`price.php`](price.php ):
    - `__construct($attributes)`: Initializes the SKU generator.
    - `generateCombinations($sets, $requiredSets)`: Generates SKU combinations.
    - `calculatePrice($combination)`: Calculates the price of a combination.
    - `getMemoryUsage()`: Returns the memory usage.

## License

This project is licensed under the MIT License.
 