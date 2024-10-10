import json
import re
from xml.dom.minidom import parseString


# Custom JSON formatting
def format_json(dictionary):
    def format_dict(dic, indent=0):
        if not isinstance(dic, dict):
            return f"\"{str(dic)}\""

        result = "{\n"
        for idx, (key, value) in enumerate(dic.items()):
            comma = "," if idx < len(dic) - 1 else ""
            result += f"{' ' * (indent + 4)}\"{key}\": {format_dict(value, indent + 4)}{comma}\n"
        result += f"{' ' * indent}" + "}"
        return result

    json_str = format_dict(dictionary)
    # print(json_str)
    with open("formats/message.json","w") as file:
        file.write(json_str)

# Custom XML formatting
def format_xml(dictionary):
    def format_dict(dic, indent=0):
        if not isinstance(dic, dict):
            return f"{str(dic)}"

        result = "\n"
        for idx, (key, value) in enumerate(dic.items()):
            if type(key) == int:
                key = "li"
            result += f"{' ' * (indent + 4)}<{key}> {format_dict(value, indent + 4)}</{key}>\n"
        result += f"{' ' * indent}"
        return result

    xml_str = f"<body>{format_dict(dictionary)}</body>"
    # print(xml_str)
    with open("formats/message.xml","w") as file:
        file.write(xml_str)