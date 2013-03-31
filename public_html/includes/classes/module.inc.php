<?php

  class module {
    public $type;
    public $modules;
    
    public function set_type($type) {
      $this->type = $type;
    }
    
    public function load($module_id='') {
      global $system;
      
      if (empty($module_id)) {
        $load_modules = explode(';', $system->settings->get($this->type.'_modules'));
        if (empty($load_modules)) return;
      } else {
        $load_modules = array($module_id);
      }
      
      foreach ($load_modules as $module_id) {
      
      // Uninstall non-existent module
        if (!is_file(FS_DIR_HTTP_ROOT . WS_DIR_MODULES . $this->type . '/' . $module_id .'.inc.php')) {
          
          $installed_modules = explode(';', $system->settings->get($this->type.'_modules'));
          
          $key = array_search($module_id, $installed_modules);
          if ($key !== false) unset($installed_modules[$key]);
          
          $system->database->query(
            "update ". DB_TABLE_SETTINGS ."
            set value = '". $system->database->input(implode(';', $installed_modules)) ."'
            where `key` = '". $this->type ."_modules'
            limit 1;"
          );
          
          $system->database->query(
            "delete from ". DB_TABLE_SETTINGS ."
            where `key` = '". $system->database->input($this->type.'_module_'. $module_id) ."';"
          );
          
          continue;
        }
        
        $module = new $module_id;
        
      // Get settings from database
        $settings = array();
        if ($system->settings->get($this->type.'_module_'.$module_id)) {
          $settings = unserialize($system->settings->get($this->type.'_module_'.$module_id));
        }
        
      // Set settings to module
        $module->settings = array();
        foreach ($module->settings() as $setting) {
          $module->settings[$setting['key']] = isset($settings[$setting['key']]) ? $settings[$setting['key']] : $setting['default_value'];
        }
        
        $module->priority = isset($module->settings[$setting['key']]) ? $module->settings[$setting['key']] : 0;
        
        $this->modules[$module->id] = $module;
        
        $this->sort();
      }
    }
    
    private function sort() {
      if (!function_exists('custom_sort_modules')) {
        function custom_sort_modules($a, $b) {
          if ($a->priority == $b->priority) {
            return ($a->name < $b->name) ? 1 : -1;
          } else if ($a->priority > $b->priority) {
            return 1;
          } else {
            return -1;
          }
        }
      }
      uasort($this->modules, 'custom_sort_modules');
    }
  }

?>