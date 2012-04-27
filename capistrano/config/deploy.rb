set :stages, %w(production staging)
set :default_stage, "staging"
require 'capistrano/ext/multistage'

#set(:thestage) { "#{stage}" }
#sensitiveFile = File.open("deploy/deploy-sensitive-#{thestage}.bf")
set :scm, :git
set :deploy_via, :remote_cache
set :copy_exclude, [".hg", ".DS_Store", ".hgignore"]
set :git_enable_submodules,1
set :use_sudo, false
#forwards ssh keys from local machine
ssh_options[:forward_agent] = true
set :keep_releases, 15


#sensitiveFile = File.open("deploy-sensitive-#{stage}.bf")
#encryptedSensitveJSON64encode = sensitiveFile.read
#encryptedSensitveJSON64decode = Base64.decode64(encryptedSensitveJSON64encode)
#cipher = OpenSSL::Cipher.new("bf-cbc")
#cipher.decrypt
#key = OpenSSL::PKCS5.pbkdf2_hmac_sha1(:password.to_s, OpenSSL::Random.random_bytes(8), 2000, cipher.key_len)
#cipher.key =key
##cipher.iv = 0;
#decryptedSensitiveJSON = cipher.update(encryptedSensitveJSON64decode)
#decryptedSensitiveJSON << cipher.final
#config = JSON.parse(decryptedSensitiveJSON)

namespace :JuntoDeploy do
    task :DecryptAndReadConfiguration, :roles => :app do
        run_locally "openssl bf -d -a -p -in config/deploy/deploy-sensitive-#{stage}.bf -out config/deploy/deploy-sensitive-#{stage}.json -pass pass:'#{password}'"
        jsonFile = File.open("config/deploy/deploy-sensitive-#{stage}.json")
        config = JSON.parse(jsonFile.read)
        set :ftp_username, config["ftp_username"].to_s
        set :ftp_password, config["ftp_password"].to_s
        set :ftp_server, config["ftp_server"].to_s
        ftp = Net::FTP.new("#{ftp_server}","#{ftp_username}","#{ftp_password}")
        ftp.gettextfile("remoteftp/junto/projects/#{application}/sensitiveconfig/wp-sensitive-#{stage}.json", "config/deploy/wp-sensitive-#{stage}.json")
        ftp.close
        wpjsonFile = File.open("config/deploy/wp-sensitive-#{stage}.json")
        wpconfig = JSON.parse(wpjsonFile.read)
        set :db_database, config["DB_NAME"].to_s
        set :db_username, config["DB_USER"].to_s
        set :db_password, config["DB_PASSWORD"].to_s
    end
end

namespace :JuntoDeploy do
      task :CreateSharedFolders, :roles => :app do
            run "mkdir -p #{shared_path}/uploads"
            run "mkdir -p #{shared_path}/blogs.dir"
            run "lftp -u '#{ftp_username}','#{ftp_password}' -e \"mkdir -p /remoteftp/juntobackups/#{application}/#{stage}/db-and-shared/ondeploy;mkdir -p /remoteftp/juntobackups/#{application}/#{stage}/db-and-shared/scheduled; quit\" '#{ftp_server}'"
    end
end

namespace :JuntoDeploy do
    task :LinkCurrentSharedFolders, :roles => :app do
        run "ln -nfs #{shared_path}/uploads #{release_path}/juntobasepress/wordpress/wp-content/uploads"
        run "ln -nfs #{shared_path}/blogs.dir #{release_path}/juntobasepress/wordpress/wp-content/blogs.dir"
        run "chmod -R 777 #{release_path}"
    end
end

namespace :JuntoDeploy do
    task :SetLocalConfiguration, :roles => :app do

        run "lftp -u '#{ftp_username}','#{ftp_password}' -e \"cd /remoteftp/junto/projects/#{application}/sensitiveconfig; get wp-sensitive-#{stage}.json -o #{release_path}/config/sensitive/wp-sensitive-local.json; quit\" '#{ftp_server}'"
        run "cp #{release_path}/config/wordpress-app/wp-config-#{stage}.php #{release_path}/config/wordpress-app/wp-config-local.php"
        run "cp #{release_path}/config/htaccess/.htaccess-#{stage} #{release_path}/juntobasepress/wordpress/.htaccess"
        run "cp #{release_path}/juntobasepress/external-configs/php-config/php.ini-#{stage} #{release_path}/juntobasepress/wordpress/php.ini"
    end
end

namespace :JuntoDeploy do
    task :RunDbMigrations, :roles => :app do
        run "php #{release_path}/juntobasepress/tools/mysql-php-migrations/migrate.php latest"
    end
end

namespace(:deploy) do
  desc "Backup Everything"
  task :backup, :roles => :app do
    run "mysqldump -u'#{db_username}' -p'#{db_password}' '#{db_database}' > #{release_path}/dbBackupBeforeDeploy#{release_name}.sql"
    run "tar zcf #{shared_path}/onDeployDbAndSharedBackup.tgz #{shared_path}/uploads #{shared_path}/blogs.dir #{release_path}/dbBackupBeforeDeploy#{release_name}.sql --strip-components=2"
    run "openssl bf -a -salt -in #{shared_path}/onDeployDbAndSharedBackup.tgz -out #{shared_path}/onDeployDbAndSharedBackup.bf -pass pass:'#{password}'"
    run "lftp -u '#{ftp_username}','#{ftp_password}' -e \"cd /remoteftp/juntobackups/#{application}/#{stage}/db-and-shared/ondeploy; put #{shared_path}/onDeployDbAndSharedBackup.bf -o onDeployDbAndSharedBackupBefore#{release_name}.bf; quit\" '#{ftp_server}'"
  end
end

after "deploy:setup", "JuntoDeploy:CreateSharedFolders"
after "deploy:update_code", "JuntoDeploy:DecryptAndReadConfiguration"
before "deploy:symlink", "JuntoDeploy:LinkCurrentSharedFolders"
before "deploy:symlink", "JuntoDeploy:SetLocalConfiguration"
before "deploy:symlink", "deploy:backup"
after "JuntoDeploy:SetLocalConfiguration", "JuntoDeploy:RunDbMigrations"
#after "deploy:restart", "deploy:cleanup"
