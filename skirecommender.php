<?php
/**
 * Ski Recommender Module - Simplified Version
 * 
 * @author Zakaria Lbouhmadi
 * @version 1.0.0
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class SkiRecommender extends Module
{
    public function __construct()
    {
        $this->name = 'skirecommender';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Zaki-LB';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.7',
            'max' => _PS_VERSION_
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Ski Recommender');
        $this->description = $this->l('Helps customers find their perfect ski based on their characteristics');
    }

    public function install()
    {
        return parent::install()     
            && $this->registerHook('header')
            && $this->registerHook('moduleRoutes')
            && $this->registerHook('displaySitemap');  // Added this hook registration
    }

    public function uninstall()
    {
            return parent::uninstall()
        && $this->unregisterHook('header')
        && $this->unregisterHook('moduleRoutes')
        && $this->unregisterHook('displaySitemap');
    }



    public function hookHeader()
    {
        $this->context->controller->addCSS($this->_path.'views/css/skirecommender.css');
        $this->context->controller->addJS($this->_path.'views/js/skirecommender.js');
    }
  

    public function hookModuleRoutes()
    {
        return [
            'module-skirecommender-form' => [
                'controller' => 'form',
                'rule' => 'ski-calculator',
                'keywords' => [],
                'params' => [
                    'fc' => 'module',
                    'module' => 'skirecommender'
                ]
            ],
            'module-skirecommender-form-pl' => [
                'controller' => 'form',
                'rule' => 'kalkulator-nart',
                'keywords' => [],
                'params' => [
                    'fc' => 'module',
                    'module' => 'skirecommender'
                ]
            ],
            'module-skirecommender-result' => [
                'controller' => 'form',
                'rule' => 'ski-calculator/result',
                'keywords' => [],
                'params' => [
                    'fc' => 'module',
                    'module' => 'skirecommender'
                ]
            ]
        ];
    }
  

    public function hookDisplaySitemap($params)
    {
        return [
            'links' => [
                [
                    'id' => 'skirecommender',
                    'label' => ($this->context->language->iso_code === 'pl') 
                        ? $this->l('Dobierz Narty') 
                        : $this->l('Ski Calculator'),
                    'url' => $this->context->link->getModuleLink('skirecommender', 'form'),
                    'priority' => 0.8
                ]
            ]
        ];
    }
}
