set :deploy_to, "~/smatchup/"
set :user, "polydaddy"
set :branch, "smatchupstandalone"
set :use_sudo, true
set :db_database, :staging_db_database
set :db_username, :staging_db_username
set :db_password, :staging_db_password


server "smatchup.com", :app
