# -*- coding: utf-8 -*-
import urllib
import re, sys, json
import itertools,collections
from SPARQLWrapper import SPARQLWrapper, JSON
"""
SPARQL class which provides the connection between system and SPARQL-endpoint
"""
endpoint = "http://dbpedia.org/sparql/"
sparql = SPARQLWrapper(endpoint)

sparql_wiktionary = SPARQLWrapper("http://wiktionary.dbpedia.org/sparql")


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


def return_class_of_resource(uri_array):
    uri_classes = []
    for uri in uri_array:
        sparql.setQuery("PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> SELECT DISTINCT ?classes WHERE {<"+uri+"> rdf:type ?classes. FILTER regex(?classes, \"dbpedia.org\")}")
        sparql.setReturnFormat(JSON)
        results = sparql.query().convert()
        for result in results["results"]["bindings"]:
            try:
                uri_classes.append(result["classes"]["value"])
                
            except:
                pass

    unique, non_unique = unique_items(uri_classes,len(uri_array))
    
#    remove duplicates!
    non_unique = list(set(non_unique))
    return non_unique

def return_properties_of_resource(uri_array):
    uri_properties = []
    for uri in uri_array:
        sparql.setQuery("PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> SELECT DISTINCT ?properties ?value WHERE {<"+uri+"> ?properties ?value. FILTER regex(?properties, \"dbpedia.org\")}")
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
        if value[0]==len(uri_array):
            uri_properties.append([key,value[1]])
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


#def test():
#    output = {'properties': [],'classes': []}
#    output['properties'].append({
#            'url': 'http//test',
#            'value': 1
#        })
#    output['properties'].append({
#            'url': 'http//test1',
#            'value': 2
#        })
#    output['properties'].append({
#            'url': 'http//test2',
#            'value': 3
#        })
#    output['classes'].append({
#            'url': 'http//test/class',
#        })
#    
#    tmp = {"FRONT": "19.50", "RACK": "17.63", "REAR": "21.06", "ROOM": "15.6"}
#    bulkData = json.dumps(output, ensure_ascii = 'False')
#    print bulkData

    
def createJsonObject(array_property,array_class,array_yago):
    output = {'properties': [],'classes': [], 'yago':[]}
    for entry in array_property:
        output['properties'].append({
            'url': entry[0],
            'value': entry[1]
        })
        
    for entry in array_class:
        output['classes'].append({
            'url': entry
        })
    for entry in array_yago:
        output['yago'].append({
            'url': entry
        })
    return json.dumps(output, ensure_ascii = 'False')



def sortClasses(classes):
    #sort only dbpedia classes, because tried to compare yago and dbpedia classes returns alsways false
    yago = []
    classes_tmp = []
    
    for x in classes:
        if "yago" in x:
            yago.append(x)
        else:
            classes_tmp.append(x)
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

    return classes_tmp, yago



def main():
    
    debug= False
    data = []
    resource_array = []
    if not debug:
        try:
            data = json.loads(sys.argv[1])
        except:
            print "ERROR in json.load()"
            sys.exit(1)
    
        
        for entry in data:
            if "http://dbpedia.org/resource/" not in entry:
                entry = "http://dbpedia.org/resource/"+entry
            resource_array.append(entry)
    else:
        resource_array.append("http://dbpedia.org/resource/Thessaloniki")
        resource_array.append("http://dbpedia.org/resource/Athens")
                
    class_array = []
    property_array=[]
    
    try:
        class_array =return_class_of_resource(resource_array)
        class_array, yago_array = sortClasses(class_array)
        #make a simple sort over array, based on subclass or not.
        
    #    this array has to be filled from the command line input
    #    class_array = ["http://dbpedia.org/ontology/Place","http://dbpedia.org/ontology/PopulatedPlace","http://dbpedia.org/ontology/Village"]
    #    for x in itertools.combinations(class_array, 2):
    #        print x
    #        print askSubclass(x[1],x[0])
    #        print
    except:
        pass
    
    try:
        property_array = return_properties_of_resource(resource_array)
    except:
        pass
    
    print createJsonObject(property_array,class_array, yago_array)

    
if __name__ == "__main__":
    main()
    
 

 
 
 



