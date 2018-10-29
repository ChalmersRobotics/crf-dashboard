#!/usr/bin/env python3
# -*- coding: utf-8 -*-
import requests
import os
import hashlib


""" generates a valid token for the specified resource using the provided secret key """
def generate_token(resourceName, secretKey):
    # generate the authorization token by hashing the concatenation of the secret and the resource name
    return hashlib.sha256(secretKey.encode() + resourceName.encode() + secretKey.encode()).hexdigest()
    



""" Uploads the image, returns True if successful and False otherwise. If there was an error, this is also printed to stdout
 also takes a formatted url string where {0} will be replaced by the resource name """
def upload_image(resourceName, token, filePath, urlString):
    # create the url
    url = urlString.format(resourceName)

    # prepare http headers for authorization
    headers = {'Token' : str(token)}

    # Prepare files for upload
    files = {'image':(os.path.basename(filePath), open(filePath, 'rb'))}

    try:
        # excecute POST request to upload
        r = requests.post(url, files=files, headers=headers)

        # TODO: maybe check response code?
        #if(r.status_code != 200):
            # there was an error

        # get the JSON response (this throws ValueError if no JSON available)
        res = r.json()

        # if we got this far, we have a JSON object
        if(res['success']):
            return True
        
        # print the error messages from the server
        print("The following error was returned by the server:")
        print('\n'.join(res['error']))
        return False
        
    except ValueError as ve:
        print("Server didn't return a correctly formatted JSON response, got:")
        print(r.text)
        return False

    except Exception as e:
        print("An error occured while connecting to the server:")
        print(e)
        return False

