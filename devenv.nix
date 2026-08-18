{
  pkgs,
  lib,
  config,
  inputs,
  ...
}:

{
  packages = [ pkgs.poedit ];

  languages.php.enable = true;
}
