# EXAMPLE update_parameter_currentvalue
# Updates the current value for a parameter via its customId
# Read the API Documentation @ http://docs.eagle.io/en/latest/api/index.html
# --------------------------------------------------------------------------

# Import packages
import httplib2
import json
import random

# Set node id and value as required
node_id           = '@mysensor'           # note: customId's start with '@' 
node_value        = random.randint(0,100) # generate random number between 0-100 for this example

# Set api key and resource endpoint
api_key           = 'YOUR_API_KEY_HERE'   # you can generate an API key from account settings
api_path          = 'https://api.eagle.io/api/v1/'
api_resource      = 'nodes/' + node_id + '/historic/now'

# Build http request
req_uri           = api_path + api_resource
req_method        = 'PUT'    
req_headers       = {'Content-Type': 'application/json', 'X-Api-Key': api_key}
req_body          = {'value': node_value}  # optionally include timestamp and quality:
                  # {'value': 15, 'timestamp': '2017-07-14T23:38:00Z', 'quality': 149}

# Send http request and get response
http              = httplib2.Http()
response, content = http.request(req_uri, req_method, json.dumps(req_body), req_headers)

# Parse content as json
data              = json.loads(content)

# Ensure we get a 202 Accepted response (as per docs)
if response.status != 202:
    print str(response.status) + ': ' + data['error']['message']
    exit()

# Pretty print the data
print json.dumps(data, indent=4)
