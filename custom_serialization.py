def encode_vdovmart(data):
    if not isinstance(data, dict):
        data=dict(data)
    encoded=""
    for key,values in data.items():
        for index, value in enumerate(values.values()):
            # print(value)
            start=f"{key}:{index}"
            end=f"{index}:{key}"
            encoded+=f"{start}\n\t{value}\n{end}\n"
    return encoded

def decode_vdovmart(data,type="dict"):
    dic={}
    lines=[x for x in data.split("\n") if x]

    index=0
    while index < len(lines):
        line = lines[index]

        if ":" in line:
            arg1, arg2 = line.split(":", 1)  # Use maxsplit=1 to avoid splitting on extra ":" characters
            if arg1 not in dic:
                dic[arg1] = {arg2: ""}
            else:
                dic[arg1][arg2] = ""

            new_index = index + 1
            while new_index < len(lines):
                new_line = lines[new_index].replace("\t","")
                
                if ":" in new_line and "http" not in new_line:
                    new_arg1, new_arg2 = new_line.split(":", 1)
                    if arg1 == new_arg2 and arg2 == new_arg1:
                        index = new_index  # Move index to new position
                        break
                else:
                    # Append the content to the existing value, if any
                    if dic[arg1][arg2]:
                        dic[arg1][arg2] += "\n" + new_line
                    else:
                        dic[arg1][arg2] = new_line

                new_index += 1
        
        index += 1
    if type=="list":
        return list(dic.items())
    if type=="set":
        return set(dic.items())
    if type=="tup":
        return tuple((dic.keys(),dic.values()))
    return dic