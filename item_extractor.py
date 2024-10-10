from bs4 import BeautifulSoup
from helper import *
import time

def format_item(name, price, quantity, href, buyers):
    # Process quantity and with_tax
    price = price.replace(",", ".") if price else None
    quantity = quantity.replace(",", ".") if quantity else None
    with_tax = buyers.text.strip() if buyers else "none"
    with_tax = with_tax.replace(",", ".") if with_tax else None

    return name, price, quantity, href, with_tax

def validate_item(name, price, quantity, href, buyers):

    if None in (name, price, quantity, href):
        return False, None

    if not name.strip() or not price.strip() or not quantity.strip():
        return False, None


    return True

def get_items(data):
    soup = BeautifulSoup(data, 'html.parser')
    names = soup.find_all("span", class_="market_listing_item_name")
    prices = soup.find_all("span", {"class": "normal_price", "data-currency": "1"})
    quantities = soup.find_all("span", class_="market_listing_num_listings_qty")
    links = soup.find_all("a", {"class": "market_listing_row_link"})
    
    item_info = zip(names, prices, quantities, links)
    
    for item in item_info:
        name, price, quantity, href = item
        host = "steamcommunity.com"
        port = 443
        path = href["href"].split(host)[1]

        new_data = send_https_request(host, port, path)
        with open("second_response.html", "w", encoding="UTF-8") as file:
            file.write(new_data)

        soup = BeautifulSoup(new_data, 'html.parser')
        buyers = soup.find("span", {"class": "market_listing_price_with_fee"})

        # Validate item data and process with_tax

        is_valid = validate_item(name.text, price.text, quantity.text, href["href"], buyers)
        if is_valid:
            name, price, quantity, href, buyers = format_item(name.text, price.text, quantity.text, href["href"], buyers)

            print(name, price, quantity, href, buyers)
            with open("item_data.csv", "a", encoding="UTF-8") as f:
                f.write(f"{name},{price},{quantity},{buyers},{href}\n")
        else:
            print(f"Validation failed for item: {name.text}, skipping.")

        time.sleep(1)

if __name__ == "__main__":
    with open("main_response.html", "r", encoding="UTF-8") as file:
        get_items(file)
