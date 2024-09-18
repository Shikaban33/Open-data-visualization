import requests
from bs4 import BeautifulSoup
import dateutil.parser as dparser
import re
import pymysql
import datetime


kiekis=0

# Define the base URL
base_url = "https://data.gov.lt/datasets/?page="

# Initialize an empty list to store the scraped data
all_data = []

# Determine the total number of pages (you can adjust this based on the actual number of pages)
total_pages = 3
#ir_remove
def remove_ir_not_in_word(input_string):
    # Replace "ir" only if it's not part of a word
    modified_string = re.sub(r'\bir\b', '', input_string, flags=re.IGNORECASE)
    # Remove any extra whitespaces after removing "ir"
    modified_string = re.sub(r'\s+', ' ', modified_string)
    return modified_string.strip()
def connect_DB():
    host = 'localhost'
    user = 'dataupload'
    password = 'dataupload'
    database = 'db2'
    connection = pymysql.connect(host=host, user=user, password=password, database=database)
    #To comment use ctrl+k+c, to uncomment use ctrl+k+u
    return connection
    

# Loop through each page

for page_number in range(1, total_pages + 1):
    #print(page_number)
    url = f"{base_url}{page_number}"
    response = requests.get(url)
    soup = BeautifulSoup(response.text, "html.parser")

    dataset_items = soup.find_all("div", class_="content")
    format_items = soup.find_all("div", class_="tags mt-3 mb-1")


# Creating a connection object.
    

    for item in dataset_items:
        #TITLE
        title = item.find("a", class_="dataset-list-title").text.strip()
        #DESCRIPTION
        if item.find("p", class_="dataset-list-description my-1"):       
             description = item.find("p", class_="dataset-list-description my-1").text.strip()
        else:
             description = "0" 
        #DATE_pub

        date1= item.find("span", class_="tag is-white has-text-dark")
        date2= date1.find_next();

        date_inp1 = date1.text.strip()
        date_inp2 = date2.text.strip()
        date=dparser.parse(str(date_inp1), fuzzy=True).date()
        date1=dparser.parse(str(date_inp2), fuzzy=True).date()
            

        #ORGANISATION
        if item.find("a", class_="tag is-white has-text-dark dataset-list-organization"):       
             organization = item.find("a", class_="tag is-white has-text-dark dataset-list-organization").text.strip()
        else:
             description = "0" 
        #FORMAT
        if item.find("a", class_="tag is-info is-light"):       
             format = item.find("a", class_="tag is-info is-light").text.strip()
        else:
             format = "0"
        #LINK
        link = item.find("a", class_="dataset-list-title")["href"]
        #additional
        add_url = "https://data.gov.lt"
        url1 = f"{add_url}{link}"
        response = requests.get(url1)
        additional = BeautifulSoup(response.text, "html.parser")
        #for det in additional:
        dataset_details = additional.find("tbody")
        category_naming = dataset_details.find("ul", class_="mt-0 ml-4")
        #busena
        if dataset_details.find(lambda tag:tag.name=="td" and "Atvertas" in tag.text):
            state=dataset_details.find(lambda tag:tag.name=="td" and "Atvertas" in tag.text).text.strip()
        elif dataset_details.find(lambda tag:tag.name=="td" and "Inventorintas" in tag.text):
            state=dataset_details.find(lambda tag:tag.name=="td" and "Inventorintas" in tag.text).text.strip()
        else:
            state="0"
        #kategorija
        category=[]
        if category_naming.find_all("li"):
            for li in category_naming.find_all("li"): 
                category.append(li.text.strip())
        else:
            category="0"
            
        views = additional.find(attrs={'id':'total_hits'}).text.strip()

      
        #zyme
        tag=[]
        if additional.find_all("a", class_="tag"):
            for tag_a in additional.find_all("a", class_="tag"): 
                #print(li.text, end=" ") 
                tag.append(tag_a.text.strip())
        else:
            tag="0"

        #print(tag)

        all_data.append({"title": title, "Publication_date":date, "Update_date": date1,"organization":organization,"description": description, "format":format, "link": "https://data.gov.lt"+link,"category":category,"state":state,"views":views,"tag":tag})
        
   

#Print the collected data

#for data in all_data:
    #print(f"Title: {data['title']}\n")
    #print(f"Date pub: {data['Publication_date']}\n")
    #print(f"Date upd: {data['Update_date']}\n")
    #print(f"Organization: {data['organization']}\n")
    #print(f"Description: {data['description']}\n")
    #print(f"Format: {data['format']}\n")
    #print(f"Link: {data['link']}\n")
    #print(f"Categeory: {data['category']}\n\n")
    #print(f"State: {data['state']}\n\n")
    #print(f"Views: {data['views']}\n\n")
    #print(f"Tag: {data['tag']}\n\n")
    

org_query_insert = "INSERT INTO organizacija (ORGANIZACIJA_PAVADINIMAS) VALUES (%s)"
org_query_select = "SELECT ORGANIZACIJA_ID FROM organizacija WHERE ORGANIZACIJA_PAVADINIMAS = %s"

bukle_query_insert = "INSERT INTO bukle (BUKLE_BUSENA, BUKLE_DATA) VALUES (%s, %s)"
bukle_query_select = "SELECT BUKLE_ID FROM bukle WHERE BUKLE_BUSENA = %s"

rinkinys_query_insert = "INSERT INTO rinkinys (ORGANIZACIJA_ID, RINKINYS_PAVADINIMAS, RINKINYS_IKEL_DATA, RINKINYS_ATNAUJ_DATA, RINKINYS_APRASAS, RINKINYS_NUORODA) VALUES (%s, %s, %s, %s, %s, %s)"
rinkinys_query_select = "SELECT RINKINYS_ID FROM rinkinys WHERE RINKINYS_PAVADINIMAS = %s"

saug_query_insert = "INSERT INTO saugykla (SAUGYKLA_FORMATAS) VALUES (%s)"
saug_query_select = "SELECT SAUGYKLA_ID FROM saugykla WHERE SAUGYKLA_FORMATAS = %s"

tipas_query_insert = "INSERT INTO tipas (TIPAS_KATEGORIJA) VALUES (%s)"
tipas_query_select = "SELECT TIPAS_ID FROM tipas WHERE TIPAS_KATEGORIJA = %s"

turi_query_insert = "INSERT INTO turi (bukle_id, rinkinys_id) VALUES (%s,%s)"
turi_query_select = "SELECT turi_id FROM turi WHERE bukle_id = %s AND rinkinys_id = %s"

naudoja_query_insert = "INSERT INTO naudoja (saugykla_id, rinkinys_id) VALUES (%s,%s)"
naudoja_query_select = "SELECT naudoja_id FROM naudoja WHERE saugykla_id = %s AND rinkinys_id = %s"

lankomumas_query_insert = "INSERT INTO lankomumas (rinkinys_id,lankomumas_perziuros,lankomumas_data) VALUES (%s,%s,%s)"
lankomumas_query_select = "SELECT lankomumas_id FROM lankomumas WHERE rinkinys_id = %s AND lankomumas_perziuros = %s"

susidaro_query_insert = "INSERT INTO susidaro (tipas_id,rinkinys_id) VALUES (%s,%s)"
susidaro_query_select = "SELECT susidaro_id FROM susidaro WHERE tipas_id = %s AND rinkinys_id = %s"

zyme_query_insert = "INSERT INTO zyme (zyme_pavadinimas) VALUES (%s)"
zyme_query_select = "SELECT zyme_id FROM zyme WHERE zyme_pavadinimas = %s"

buna_query_select = "INSERT INTO buna (rinkinys_id,zyme_id) VALUES (%s,%s)"
buna_query_insert = "SELECT buna_id FROM buna WHERE rinkinys_id = %s AND zyme_id = %s"

rinkinys_query_search = "SELECT rinkinys_id FROM rinkinys WHERE rinkinys_pavadinimas = %s AND rinkinys_atnauj_data != %s"
rinkinys_query_update = "UPDATE rinkinys SET rinkinys_atnauj_data = %s, rinkinys_aprasas = %s, rinkinys_nuoroda = %s WHERE rinkinys_pavadinimas = %s"


host = 'localhost'
user = 'dataupload'
password = 'dataupload'
database = 'db2'
    # Establishing the connection 
connection = pymysql.connect(host=host, user=user, password=password, database=database)
#print(kiekis)
cursor = connection.cursor()

def upld_data_db(upl_data_sel, upl_data_ins,query_select,query_insert):
    cursor.execute(query_select, upl_data_sel)
    result_org = cursor.fetchone()
    if result_org:
        org_name = result_org[0]
        
        #print("Rado:",org_name)
    else:
        cursor.execute(query_insert,upl_data_ins)
        connection.commit()
        org_name = cursor.lastrowid
        #print("Nerado")
    return org_name

def update_data_db(upl_data_sel, upl_data_ins,query_select,query_insert):
    cursor.execute(query_select, upl_data_sel)
    result_org = cursor.fetchone()
    if result_org:
        org_name = result_org[0]
        cursor.execute(query_insert,upl_data_ins)
        connection.commit()
        #print("Rado:",org_name)
    else:
        org_name = cursor.lastrowid
        #print("Nerado")
    return org_name

for upld_data in all_data:

    org_id_current=upld_data_db((upld_data['organization'],),(upld_data['organization'],),org_query_select,org_query_insert)

    cursor.execute(rinkinys_query_search, (upld_data['title'],upld_data['Update_date'],))
    existence = cursor.fetchone()
    cursor.execute(rinkinys_query_select, (upld_data['title'],))
    existence2 = cursor.fetchone()
    #tikrina ar jau egzistuoja irasas     upld_data['Update_date']
    if existence:
        #print("Egzistuoja",existence[0])
        rink_id_current=update_data_db((upld_data['title'],),(upld_data['Update_date'],upld_data['description'],upld_data['link'],upld_data['title'],),rinkinys_query_select,rinkinys_query_update)
        #print("GAutas id po updateo = ", rink_id_current)
    elif not existence2:
        rink_id_current=upld_data_db((upld_data['title'],),(org_id_current, upld_data['title'],upld_data['Publication_date'],upld_data['Update_date'],upld_data['description'],upld_data['link'],),rinkinys_query_select,rinkinys_query_insert)
        #print("Neegzistuoja = ",rink_id_current)   
    else:
        cursor.execute(rinkinys_query_select, (upld_data['title'],))
        result_org = cursor.fetchone()
        rink_id_current = result_org[0]
        #print("Egzistuoja, bet neatnaujintas = ", rink_id_current)

    now = date.today()
    bukle_id_current=upld_data_db((upld_data['state'],),(upld_data['state'],now,),bukle_query_select,bukle_query_insert)
    turi_id_current=upld_data_db((bukle_id_current,rink_id_current),(bukle_id_current,rink_id_current),turi_query_select,turi_query_insert)
    saug_id_current=upld_data_db((upld_data['format'],),(upld_data['format'],),saug_query_select,saug_query_insert)
    naud_id_current=upld_data_db((saug_id_current,rink_id_current),(saug_id_current,rink_id_current),naudoja_query_select,naudoja_query_insert)
    lank_id_current=upld_data_db((rink_id_current,upld_data['views'],),(rink_id_current,upld_data['views'],now),lankomumas_query_select,lankomumas_query_insert)

    for cat in upld_data['category']:
        kategor_id_current=upld_data_db((cat,),(cat,),tipas_query_select,tipas_query_insert)
        susidaro_id_current=upld_data_db((kategor_id_current,rink_id_current),(kategor_id_current,rink_id_current),susidaro_query_select,susidaro_query_insert)
    for tag in upld_data['tag']:
        zyme_id_current=upld_data_db((tag,),(tag,),zyme_query_select,zyme_query_insert)
        buna_id_current=upld_data_db((rink_id_current,zyme_id_current),(rink_id_current,zyme_id_current),buna_query_select,buna_query_insert)

cursor.close()
connection.close()