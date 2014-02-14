# -*- coding: utf-8 -*-
import sys

#sys.path.append("/home/swalter/local/lib/python2.7/site-packages/SPARQLWrapper-1.5.2-py2.7.egg")
import urllib
import re, json
import itertools,collections
from SPARQLWrapper import SPARQLWrapper, JSON
endpoint = "http://dbpedia.org/sparql/"
sparql = SPARQLWrapper(endpoint)


def createJsonObject(array):
    output = {'entities': []}
    for entry in array:
        output['entities'].append({
            'name': entry
        })
        
    return json.dumps(output, ensure_ascii = 'False')

def createMultiJsonObject(array):
    output = {'entities': []}
    for entry in array:
        output['entities'].append({
            'name': entry[0],
            'secondlanguage': entry[1]
        })
        
    return json.dumps(output, ensure_ascii = 'False')


def returnTerminals(classes, properties,language,boolean):
    query = ""
    if boolean == "AND" and len(classes)>1:
        for x in classes:
            query += " ?entity rdf:type <"+x+"> . "
    elif len(classes)==1:
        for x in classes:
            query += " ?entity rdf:type <"+x+"> . "
    elif boolean == "OR" and len(classes)>1:
        for i in range(0,len(classes)):
            tmp = classes[0]
            tmp_query = "{  ?entity rdf:type <"+tmp+"> ."
            
            for x in classes:
                if tmp not in x:
                    tmp_query += "OPTIONAL{ ?entity rdf:type <"+x+"> .}"            
            tmp_query += "} UNION"
            query += tmp_query
    if query.endswith(" UNION"):
        query = query[:-6]
    
    for x in properties:
        tmp = x.split(" with ")
        if "http" not in tmp[1]:
            query += " ?entity <"+tmp[0]+">  \""+tmp[1]+"\" . "
        else:
            query += " ?entity <"+tmp[0]+">  <"+tmp[1]+"> . "
        
    if language == "none" or len(language)==0:
        query = "SELECT DISTINCT ?entityname WHERE{"+query+" ?entity rdfs:label ?entityname. FILTER (lang(?entityname) = \"en\")}"
        sparql.setQuery(query)
        #print query
        sparql.setReturnFormat(JSON)
        results = sparql.query().convert()
        entities = []
        for result in results["results"]["bindings"]:
            try:
                entities.append(result["entityname"]["value"])
                
            except:
                pass
        return createJsonObject(entities)
    else:
        query = "SELECT DISTINCT ?entityname ?entityname2 WHERE{"+query+" ?entity rdfs:label ?entityname. FILTER (lang(?entityname) = \"en\") . OPTIONAL{ ?entity rdfs:label ?entityname2. FILTER (lang(?entityname2) = \""+language+"\")}}"
        sparql.setQuery(query)
        #print query
        sparql.setReturnFormat(JSON)
        results = sparql.query().convert()
        entities = []
        for result in results["results"]["bindings"]:
            try:
                entities.append([result["entityname"]["value"],result["entityname2"]["value"]])
                
            except:
                try:
                    entities.append([result["entityname"]["value"],""])
                except:
                    pass
#                    print "Unexpected error:", sys.exc_info()[0]
        return createMultiJsonObject(entities)

def main():
    debug= False
    classes = []
    properties = []
    resource_array = []
    language = ""
    boolean = "AND"
    if not debug:
        try:
            classes = json.loads(sys.argv[1])
        except:
            print "ERROR in json.load()"
            #sys.exit(1)
            
#        properties are optinal, therefore no error, if empty
        try:
            properties = json.loads(sys.argv[2])
        except:
            pass
        try:
            language = sys.argv[3]
        except:
            pass
        try:
            boolean = sys.argv[4]
        except:
            pass
        
        
    
        
    else:
        classes = ["http://dbpedia.org/ontology/Place"] 
        classes.append("http://dbpedia.org/ontology/Currency") 
        properties = []
        language = "zh"
        boolean = "OR"
        #print ("classes",classes)
        #print ("properties",properties)
        #print ("language",language)
        #print ("boolean",boolean) 
    
    print returnTerminals(classes,properties,language,boolean)

    

    
if __name__ == "__main__":
    main()