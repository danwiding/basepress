set :deploy_to, "~/nomidate/"
set :user, "marcycap"
set :branch, "smatchupstandalone"
set :use_sudo, false
set :port, 2222
set :db_database, :staging_db_database
set :db_username, :staging_db_username
set :db_password, :staging_db_password


server "polymathicmedia.com", :app
