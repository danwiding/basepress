The ftp passwords required to deploy via capistrano are located in the bf files above. To read the plaintext and be able to deploy run the following commands. The all files are encyrpted with that system's ssh password. Staging ssh for staging both deploy and wp. Production ssh for production both deploy and wp.

openssl bf -d -a -p -in deploy-sensitive-staging.bf -out deploy-sensitive-staging.json
openssl bf -d -a -p -in deploy-sensitive-production.bf -out deploy-sensitive-production.json

If you make a change to either file you must encrypt the json config file back to the bf file. Use the following commands
openssl bf -a -p -salt -in deploy-sensitive-production.json -out deploy-sensitive-production.bf
openssl bf -a -p -salt -in deploy-sensitive-staging.json -out deploy-sensitive-staging.bf

In general
Decrypt -
openssl bf -d -a -p -in <encryptedFile> -out <decryptedFile>
Encrypt -
openssl bf -a -p -in <decryptedFile> -out <encryptedFile>