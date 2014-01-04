import urllib
import re, sys, json
import itertools,collections
from SPARQLWrapper import SPARQLWrapper, JSON
endpoint = "http://dbpedia.org/sparql/"
sparql = SPARQLWrapper(endpoint)


def createJsonObject(array):
    output = {'entities': []}
    for entry in array:
        output['entities'].append({
            'url': entry,
        })
        
    return json.dumps(output, ensure_ascii = 'False')


def returnTerminals(classes, properties):
    query = ""
    for x in classes:
        query += " ?entity rdf:type <"+x+"> . "
    for x in properties:
        tmp = x.split(" with ")
        if "http" not in tmp[1]:
            query += " ?entity <"+tmp[0]+">  \""+tmp[1]+"\" . "
        else:
            query += " ?entity <"+tmp[0]+">  <"+tmp[1]+"> . "
        
    query = "SELECT DISTINCT ?entity WHERE{"+query+"}"
    sparql.setQuery(query)
    sparql.setReturnFormat(JSON)
    results = sparql.query().convert()
    entities = []
    for result in results["results"]["bindings"]:
        try:
            entities.append(result["entity"]["value"])
            
        except:
            pass
    return createJsonObject(entities)

def main():
    debug= False
    classes = []
    properties = []
    resource_array = []
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
    
        
    else:
        classes = ["http://dbpedia.org/ontology/Place"] 
        properties = ["http://dbpedia.org/ontology/country with http://dbpedia.org/resource/Greece"]
    
    
    print returnTerminals(classes,properties)

    

    
if __name__ == "__main__":
    main()