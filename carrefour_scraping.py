from selenium import webdriver
from webdriver_manager.firefox import GeckoDriverManager
from selenium.webdriver.firefox.options import Options
import sys 



options = Options()
#options.headless = True
driver = webdriver.Firefox(options=options,executable_path=GeckoDriverManager().install())

def get_html_source(url):
	driver.get(url)
	# driver.sleep(5)
	return driver.page_source

def research_zone_for_php(url,product):
	htmltotalSource = get_html_source(url)
	first = "\"search\":{\"data\":["
	end = "\"keyword\":\""+product+"\""; # -> end is after first !!
	index_of_first = htmltotalSource.find(first)
	sub_first_and_more = htmltotalSource[index_of_first:]
	sub_between_f_and_e = sub_first_and_more[0:sub_first_and_more.find(end)+len(end)] + "}"
	return sub_between_f_and_e


def find_totalPage(source) :
	save_find = source.find("totalPage")
	str_plus_4 = source[save_find+11:save_find+15] # 100 page max 
	sub_total_page = str_plus_4[0:str_plus_4.find(',')]
	return int(sub_total_page)

"""url = sys.argv[1]
prod = sys.argv[2]
begin = 2
htmlsource = research_zone_for_php(url,prod) 
int_total_page = find_totalPage(htmlsource)
url_ = ""
retour = htmlsource
for i in range(begin,3):#int_total_page+1):
	url_ = url + "&noRedirect=0&page=" + str(begin) 
	retour += research_zone_for_php(url_,prod)"""
#print(retour)

## SELENIUM SO HEAVY !!!

