import re
import time
from functools import reduce

import pandas as pd

from custom_serialization import decode_vdovmart, encode_vdovmart
from formating import format_json, format_xml

def process_data(file_path):
    # Read data
    data = pd.read_csv(file_path)

    # Process price and currency
    data[['price', 'currency']] = data['price'].str.split(' ', expand=True)
    data['price'] = data['price'].str.replace("$", "").astype(float)

    # Process 'with_tax' column
    data['with_tax'] = data['with_tax'].apply(
        lambda x: float(re.sub(r'[^\d\.]+', '', ".".join(str(x).strip().split(".")[:2])))
        if x != "none" else 0
    )

    # Map currency
    currency_mapping = {"USD": "GOLD", "GOLD": "USD"}
    data["currency"] = data["currency"].map(currency_mapping)

    return data

def main():
    # Process data
    data = process_data("item_data.csv")
    prices = data["price"].to_list()

    # Perform operations
    print(list(filter(lambda x: x > 1, prices)))
    print(reduce(lambda x, y: x + y, prices))
    print(time.time())

    # Add new columns
    data["time"] = time.time()
    data["total"] = reduce(lambda x, y: x + y, prices)

    print(data)

    # Format and encode data
    format_json(data.to_dict())
    format_xml(data.to_dict())

    encoded = encode_vdovmart(data.to_dict())
    with open("vdovmart/encoded.vdov", "w", encoding="UTF-8") as file:
        file.write(encoded)

    # Decode and save
    message = decode_vdovmart(encoded)
    with open("vdovmart/decoded.vdov", "w", encoding="UTF-8") as file:
        file.write(str(message))

    # Create new DataFrame and compare
    new_df = pd.DataFrame(message)
    print(data, new_df)

if __name__ == "__main__":
    main()