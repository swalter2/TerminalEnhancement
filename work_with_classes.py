import urllib
import re, sys
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




def main():
    
    if len(sys.argv) < 3:
        print "ERROR"
    
    else:
        resource_array = []
        for entry in sys.argv[1:]:
            resource_array.append(entry)
                    
        class_array = []
        property_array=[]
        
        try:
            class_array =return_class_of_resource(resource_array)
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
        
        output = ""
        
        if len(class_array) == 0:
            output += "ERROR"
        else:
            for entry in class_array:
                output += entry+"\t"
        output = output[:-1]+"\t\n\t"
        
        if len(property_array) == 0:
            output += "ERROR"
        else:
            for entry,value in property_array:
                output += entry+"\t"+value+"\t\t"
        output = output[:-2]
        
        print output
    
    
if __name__ == "__main__":
    main()
    
 

 

