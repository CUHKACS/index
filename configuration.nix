{ config, pkgs, ... }:

{
  imports = [ <nixpkgs/nixos/modules/virtualisation/google-compute-image.nix> ];

  environment.systemPackages = with pkgs; [
    vim wget htop git
  ];

  time.timeZone = "Asia/Hong_Kong";

  security.sudo.wheelNeedsPassword = false;
  users.extraUsers = {
    acsoc = {
      isNormalUser = true;
      extraGroups = [ "wheel" ];
    };
    boris = {
      isNormalUser = true;
      extraGroups = [ "wheel" ];
    };
  };

  services = {

    mysql = {
      enable = true;
      package = pkgs.mysql;
      dataDir = "/var/db/mysql";
      #initialDatabases = [
      #  { name = ""; schema = /.sql; }
      #];
    };

    phpfpm = {
      pools = {
        default = {
          listen = "/var/run/php-fpm.sock";
          extraConfig = ''
            user = nobody
            pm = dynamic
            pm.max_children = 75
            pm.start_servers = 10
            pm.min_spare_servers = 5
            pm.max_spare_servers = 20
            pm.max_requests = 500
          '';
        };
      };
    };

    nginx = {
      enable = true;
      recommendedTlsSettings = true;
      recommendedOptimisation = true;
      recommendedGzipSettings = true;
      virtualHosts = {
        "index.0x9b.moe" = {
          #serverAliases = [ "index.0x9b.moe" ];
          default = true;
          enableACME = true;
          enableSSL = true;
          forceSSL = true;
          root = "/data/index/www";
          locations = {
            "/" = {
              tryFiles = "$uri @php";
            };
            "@php" = {
              extraConfig = "rewrite (.*) /index.php?$1 last;";
            };
            "~ \.php$" = {
              extraConfig = "fastcgi_pass unix:/var/run/php-fpm.sock;";
            };
          };
        };
      };
    };

  };
}
