from functools import reduce
import pandas as pd
import time
import re
from formating import *
from custom_serialization import *
data = pd.read_csv("item_data.csv")

data[['price', 'currency']] = data['price'].str.split(' ', expand=True)
data['price'] = data['price'].str.replace("$", "").astype(float)

data['with_tax'] = data['with_tax'].apply(lambda x: float(re.sub(r'[^\d\.]+', '', ".".join(str(x).strip().split(".")[:2]))) if x != "none" else 0)

currency_mapping = {"USD": "GOLD", "GOLD": "USD"}
data["currency"] = data["currency"].map(currency_mapping)

# print(data)

prices = data["price"].to_list()

print(list(filter(lambda x: x > 1, prices)))
print(reduce(lambda x, y: x + y, prices))
print(time.time())

data["time"] = time.time()
data["total"] = reduce(lambda x, y: x + y, prices)

print(data)

format_json(data.to_dict())
format_xml(data.to_dict())



encoded=encode_vdovmart(data.to_dict())
with open("vdovmart/encoded.vdov","w",encoding="UTF-8") as file:
    file.write(encoded)


message=decode_vdovmart(encoded)
with open ("vdovmart/decoded.vdov","w",encoding="UTF-8") as file:
    file.write(str(message))

new_df=pd.DataFrame(message)
print(data,new_df)