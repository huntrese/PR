import time
start= time.time()

count = 1
while count <= 1_000_000_000:
    count += 1  # Execution will resume here when called again

end=time.time()
print(end-start)
