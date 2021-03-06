# -*- coding: utf-8 -*-
import sys,ConfigParser
import MySQLdb
#sys.path.append("/home/swalter/local/lib/python2.7/site-packages/SPARQLWrapper-1.5.2-py2.7.egg")
import urllib
import re, json
import itertools,collections
from SPARQLWrapper import SPARQLWrapper, JSON
"""
SPARQL class which provides the connection between system and SPARQL-endpoint
"""
endpoint = "http://dbpedia.org/sparql/"
sparql = SPARQLWrapper(endpoint)

sparql_wiktionary = SPARQLWrapper("http://wiktionary.dbpedia.org/sparql")

sqlconnection = ""
cursor = ""

def unique_items(iterable,number):
#    tuples = map(tuple, iterable)
    counts = collections.Counter(iterable)
    unique = []
    non_unique = []
    for t in iterable:
        if counts[t] == 1:
            unique.append(t)
        elif counts[t] == number:
            non_unique.append(t)
    return unique, non_unique


def return_class_of_resource(label_array):
 
    uri_classes = []
    counter = 0;
    for label in label_array:
        cursor.execute("SELECT uri FROM `classes`,(SELECT classid FROM `classrelation` WHERE resourceid = (SELECT id FROM `resourcelabel` WHERE en='%s')) as test WHERE classes.id = test.classid ;",label)
        #query = "PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> SELECT DISTINCT ?classes WHERE {?x rdfs:label \""+label+"\"@en .?x rdf:type ?classes. FILTER regex(?classes, \"dbpedia.org\")}"
        #sparql.setQuery(query)
        #sparql.setReturnFormat(JSON)
        #results = sparql.query().convert()
        #if len(results["results"]["bindings"]):
        #    counter +=1;
        #for result in results["results"]["bindings"]:
        #    try:
        #        uri_classes.append(result["classes"]["value"])
        #
        #    except:
        #        pass

    if counter < len(label_array):
        if counter > 1:
            unique, non_unique = unique_items(uri_classes,counter)
            non_unique = list(set(non_unique))
            return non_unique
        else:
            return uri_classes
    if len(label_array)==1:
        return uri_classes
    else:
        unique, non_unique = unique_items(uri_classes,len(label_array))
        
    #    remove duplicates!
        non_unique = list(set(non_unique))
        return non_unique

def classSpecial(name):
    uri_classes = []
    query = "SELECT DISTINCT ?classes WHERE {?x rdf:type ?classes. FILTER regex(?classes, \"dbpedia.org/ontology\"). FILTER regex(?classes, \""+name+"\")}"
    #print query
    sparql.setQuery(query)
    sparql.setReturnFormat(JSON)
    results = sparql.query().convert()
    for result in results["results"]["bindings"]:
        try:
            uri_classes.append(result["classes"]["value"])
            
        except:
            pass
    return uri_classes

    
def return_properties_of_resource(label_array):
    uri_properties = []
    for label in label_array:
        query = "PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> SELECT DISTINCT ?properties ?value WHERE {?x rdfs:label \""+label+"\"@en .?x ?properties ?value. FILTER regex(?properties, \"dbpedia.org/ontology\"). FILTER regex(?value, \"dbpedia.org\")}"
        sparql.setQuery(query)
        sparql.setReturnFormat(JSON)
        results = sparql.query().convert()
        for result in results["results"]["bindings"]:
            try:
                #print result
                if "http" in result["value"]["value"]:
                    uri_properties.append([result["properties"]["value"],result["value"]["value"]])
                
            except:
                pass

    temp = {}
    for property, value in uri_properties:
        if property not in temp: 
            temp[property] = (1,value)
        else:
            if temp[property][1] ==value: 
                number = temp[property][0]
                temp[property] = (number+1, value)
    
    uri_properties=[]
    
    for key,value in temp.iteritems():
        if value[0]==len(label_array):
            uri_properties.append([key,value[1]])
#    print uri_properties
    return uri_properties
    


def askSubclass(uri1, uri2):
    """
    returns the label for a given property
    """

    sparql.setQuery(" PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#> ASK WHERE { <"+uri1+"> rdfs:subClassOf <"+uri2+"> .} ")
    sparql.setReturnFormat(JSON)
    results = sparql.query().convert()
    label = ""
    for result in results:
            try:
                string = str(results[result])
                if "False" in string:
                    return False
                elif "True" in string:
                    return True


            except:
                pass
    return False


    
def createJsonObject(array_property,array_class,array_yago, array_category):
    output = {'properties': [],'classes': [], 'yago':[], 'category':[]}
    for entry in array_property:
        output['properties'].append({
            'url': entry[0],
            'value': entry[1]
        })
        
    for entry in array_class:
        output['classes'].append({
            'url': entry[0],
            'value': entry[1],
            'examples': entry[2]
        })
    for entry in array_yago:
        output['yago'].append({
            'url': entry[0],
            'value': entry[1],
            'examples': entry[2]
        })
    for entry in array_category:
        output['category'].append({
            'url': entry
        })
        
    return json.dumps(output, ensure_ascii = 'False')


def getCategories(uri_array):
    output = []
    for uri in uri_array:
        uri = uri.replace(" ","_")
        uri = "http://dbpedia.org/resource/"+uri
        query = "select distinct ?x where {<"+uri+"> <http://purl.org/dc/terms/subject> ?x} "
        try:
            sparql.setQuery(query)
            sparql.setReturnFormat(JSON)
            results = sparql.query().convert()
            for result in results["results"]["bindings"]:
                try:
                    output.append(result["x"]["value"])
                    
                except:
                    pass
        except:
            pass
    hm = {}
    for x in output:
        hm[x] = ""
    output = []
    for key in hm:
        #print ("key",key)
        output.append(key)
    return output
        
        
    
def returnNumberEntities(uri):
    query = "SELECT (COUNT(DISTINCT ?x)) as ?value WHERE {?x rdf:type <"+uri+">}"
    value = 0
    try:
        sparql.setQuery(query)
        sparql.setReturnFormat(JSON)
        results = sparql.query().convert()
        for result in results["results"]["bindings"]:
            try:
                value = int((result["value"]["value"]))
                
            except:
                pass
    except:
        value = 0
        
    return value

def getExampleResources(uri,number):
    query = "SELECT ?entityname WHERE {?x rdf:type <"+uri+">. ?x rdfs:label ?entityname. FILTER (lang(?entityname) = \"en\")}LIMIT "+str(number)
    output = ""
#    print ("output query",query)
    try:
        sparql.setQuery(query)
        sparql.setReturnFormat(JSON)
        results = sparql.query().convert()
        for result in results["results"]["bindings"]:
            try:
                output+=(result["entityname"]["value"])+", "
                
            except:
                pass
    except:
        output = ""
        
    if output.endswith(", "):
        output = output[:-2]
#    print("output",output)
    return output


def sortClasses(classes):
    #sort only dbpedia classes, because tried to compare yago and dbpedia classes returns alsways false
    yago = []
    yago_value = []
    classes_value = []
    classes_tmp = []

    for x in classes:
        if "yago" in x:
            yago.append(x)
        elif "dbpedia.org/ontology/" in x:
            classes_tmp.append(x)
            
    #return number of entities for yago classes and sort classes according to frequency 
    for uri in yago:
        value = returnNumberEntities(uri)
        examples = getExampleResources(uri,4)
        yago_value.append([uri,value,examples])
    yago_value = sorted(yago_value, key=lambda entry: entry[1], reverse=True) 
    for entry in yago_value:
        entry[1] ='{0:,}'.format(entry[1])
            
    #sort hierarchy of dbpedia classes
    for i in range(0,len(classes_tmp)):
        change = False
        for i in range(0,len(classes_tmp)-1):
            if  not askSubclass(classes_tmp[i],classes_tmp[i+1]):
                pass
            else:
                tmp = classes_tmp[i]
                classes_tmp[i] = classes_tmp[i+1]
                classes_tmp[i] = tmp
                change = True
        if change:
            break;
    
    for uri in classes_tmp:
        value = '{0:,}'.format(returnNumberEntities(uri))
        examples = getExampleResources(uri,10)
        classes_value.append([uri,value,examples])
    
    return classes_value, yago_value



def main():
    
    config = ConfigParser.ConfigParser()
    config.read('config.conf')
    user = config.get('db', 'user')
    pasw = config.get('db', 'pasw')
    dbase = config.get('db', 'dbase')
    host = config.get('db', 'host')
    global sqlconnection
    sqlconnection = MySQLdb.connect(host,user, pasw, dbase)
    global cursor
    cursor = sqlconnection.cursor()

    debug= True
    data = []
    resource_array = []
    if not debug:
        try:
            data = json.loads(sys.argv[1])
        except:
            print "ERROR in json.load()"
            sys.exit(1)
    
        
        for entry in data:
#            if "http://dbpedia.org/resource/" not in entry:
#                entry = "http://dbpedia.org/resource/"+entry
            if entry.startswith(' '):
                entry = entry[1:]
            if "class:" not in entry:
                resource_array.append(entry.capitalize())
            else:
                resource_array.append(entry)
    else:
        resource_array.append("Bruce Lee")
        resource_array.append("Jackie Chan")
        #resource_array.append("Apple")
        #resource_array.append("class:Name")
                
    class_array = []
    property_array = []
    yago_array = []
    category_array = []
    
    try:

        if len(resource_array)==1 and "class:" in str(resource_array):
            #print "before special"
            class_array =classSpecial(resource_array[0].replace("class:",""))
            #print "after special"
            class_array, yago_array = sortClasses(class_array)
        else:
            class_array =return_class_of_resource(resource_array)
            class_array, yago_array = sortClasses(class_array)
    except:
        pass
    
    try:
        if "class:"  not in str(resource_array):
            property_array = return_properties_of_resource(resource_array)
    except:
        pass
    
    try:
        if "class:" not in str(resource_array):
            category_array = getCategories(resource_array)
            #print ("category_array",category_array)
            #print
    except:
        pass
    
    print createJsonObject(property_array,class_array, yago_array,category_array)

    cursor.close()
    sqlconnection.close()
    
if __name__ == "__main__":
    main()
    
 

 
 
 



