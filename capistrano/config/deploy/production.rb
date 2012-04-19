set :deploy_to, "/var/www/vhosts/thejun.to/JuntoWebsite/"
set :user, "root"
# Database credentials for production
set :db_database, :production_db_database
set :db_username, :production_db_username
set :db_password, :production_db_password

server "205.186.137.80", :app



