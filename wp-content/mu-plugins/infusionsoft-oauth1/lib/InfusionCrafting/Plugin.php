<?php

namespace InfusionCrafting;

class Plugin {
  const TEXT_DOMAIN = 'infusioncrafting';

  public static function root() {
    return realpath(__DIR__.'/../../');
  }

  public static function view(string $path) {
    return static::root() . '/views/' . $path . '.php';
  }
}
