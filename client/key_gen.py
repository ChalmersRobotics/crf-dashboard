import uploader

if __name__ == '__main__' :

     # "secret" key
    secret = "ThisISTheSuperSecretKey"

    resourceName = "hell"

    print(uploader.generate_token(resourceName, secret))