def returnTerminals():
    tmp = {"TERMINAL": "ATHENS"}
    return json.dumps(tmp, ensure_ascii = 'False')

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
            sys.exit(1)
            
#        properties are optinal, therefore no error, if empty
        try:
            properties = json.loads(sys.argv[2])
        except:
            pass
    
        
    else:
        pass
    
    
    print returnTerminals()
                
    

    
if __name__ == "__main__":
    main()