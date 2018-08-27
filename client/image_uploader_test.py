
import os

import image_uploader as uploader

if __name__ == '__main__' :

    # "secret" key
    secret = "ThisISTheSuperSecretKey"

    resourceName = "crf_webcam_outer"


    # create url
    url = 'http://localhost/crf-dashboard/api/image/{0}'

    # the path to this files directory
    fileDirectory = os.path.dirname(os.path.realpath(__file__))

    # full path to the image file to upload
    filePath = fileDirectory  + '\\images\\crf_webcam_outer.jpg'

    # create the http authorization token
    token = uploader.generate_token(resourceName, secret)

    print(token)

    res = uploader.upload_image(resourceName, token, filePath, url)
    print(res)



