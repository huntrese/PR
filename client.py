from helper import *
from item_extractor import get_items
import time


def main():
    items_per_page = 10
    total_pages = 5
    
    with open("item_data.csv","w") as f:
        f.write("name,price,quantity,with_tax,href\n")

    for page in range(total_pages):
        time.sleep(2)

        start = page * items_per_page
        print(f"Fetching page {page + 1}")
        
        results = get_market_search_results(start, items_per_page)
        if results and results.get('success'):
            html_content = results.get('results_html')
            if html_content:
                get_items(html_content)
            else:
                print("No HTML content in the response")
        else:
            print(f"Failed to fetch results for page {page + 1}")

if __name__ == "__main__":
    main()