<?php
class SkiRecommenderFormModuleFrontController extends ModuleFrontController
{
    private $equivalentTypes = [
        'piste' => [1911, 1435, 1790, 1793, 1421],  // Piste types
        'race' => [1433, 1553, 1610, 1789, 1792],   // Race types
        'all_mountain' => [1430, 1817, 2030, 2028], // All Mountain types
        'freestyle' => [1744]                        // Freestyle type
    ];

    public function initContent()
    {
        parent::initContent();
        
        // SEO meta data
        $meta = [
            'meta_title' => 'Ski Size Calculator 2024 | Find Your Perfect Ski Length',
            'meta_description' => 'Free ski size calculator ★ Professional ski length and type matching ✓ Expert recommendations ✓ Find your perfect ski size now!',
            'meta_keywords' => 'ski calculator, ski size, ski length, ski finder, ski size calculator',
            'page_name' => 'ski-calculator',
        ];

        // Translate meta if Polish
        if ($this->context->language->iso_code === 'pl') {
            $meta['meta_title'] = 'Kalkulator Doboru Nart 2024 | Dobierz Narty Online Za Darmo';
            $meta['meta_description'] = 'Darmowy kalkulator doboru nart ★ Profesjonalne dopasowanie długości i typu nart ✓ Porady eksperta ✓';
        }

        $this->context->smarty->assign($meta);
        
        // Correct breadcrumb structure
        $path = [
            [
                'title' => $this->trans('Home', [], 'Shop.Theme.Global'),
                'url' => $this->context->link->getPageLink('index')
            ],
            [
                'title' => $this->context->language->iso_code === 'pl' ? 'Dobierz Narty' : 'Ski Calculator',
                'url' => $this->context->link->getModuleLink('skirecommender', 'form')
            ]
        ];

        $this->context->smarty->assign([
            'breadcrumb' => [
                'links' => $path,
                'count' => count($path),
            ],
            'path' => $path // Some themes might need this
        ]);
      
        $tooltips = [
            'gender' => $this->module->l('Gender helps us provide appropriate ski recommendations', 'form'),
            'age' => $this->module->l('Age determines if junior skis are needed', 'form'),
            'weight' => $this->module->l('Weight is important for determining the right ski length', 'form'),
            'height' => $this->module->l('Height helps determine the optimal ski length', 'form'),
            'skill_level' => [
                'title' => $this->module->l('Your skiing experience level:', 'form'),
                'beginner' => $this->module->l('Just starting or learning basics', 'form'),
                'intermediate' => $this->module->l('Comfortable on most slopes', 'form'),
                'advanced' => $this->module->l('Experienced skier', 'form')
            ],
            'style_preference' => [
                'title' => $this->module->l('Your preferred type of skiing:', 'form'),
                'piste' => $this->module->l('Groomed slopes and maintained trails', 'form'),
                'all_mountain' => $this->module->l('Versatile for various conditions', 'form'),
                'race' => $this->module->l('High-performance skiing and racing', 'form'),
                'freestyle' => $this->module->l('Terrain park and freestyle skiing', 'form')
            ]
        ];

        $result_types = [];
        $result_names = [];
        
        $form_data = [
            'gender' => Tools::getValue('gender', 'male'),
            'weight' => Tools::getValue('weight', ''),
            'height' => Tools::getValue('height', ''),
            'age' => Tools::getValue('age', ''),
            'skill_level' => Tools::getValue('skill_level', 'intermediate'),
            'style_preference' => Tools::getValue('style_preference', 'piste')
        ];

        if (Tools::isSubmit('submit_ski_recommender')) {
            if ($this->validateForm($form_data)) {
                $type_ids = $this->determineSkiType(
                    $form_data['gender'],
                    (int)$form_data['weight'],
                    (int)$form_data['height'],
                    (int)$form_data['age'],
                    $form_data['style_preference']
                );
                
              foreach ($type_ids as $id) {
                $attribute = new Attribute($id);
                // Check if attribute exists and has a name for current language
                if (Validate::isLoadedObject($attribute) && isset($attribute->name[Context::getContext()->language->id])) {
                  $result_names[$id] = $attribute->name[Context::getContext()->language->id];
                } else {
                  // Fallback to a default name using type mapping
                  $type_mapping = [
                    // Piste types
                    1911 => 'Piste',
                    1435 => 'On Piste',
                    1790 => 'Piste-Sport Performance',
                    1793 => 'Piste-High Performance',
                    1421 => 'Piste-Performance',
                    
                    // Race types
                    1433 => 'Race',
                    1553 => 'Race-Performance',
                    1610 => 'Race-On Piste',
                    1789 => 'Race-Sport Performance',
                    1792 => 'Race-High Performance',
                    1431 => 'Lady Race',
                    
                    // All Mountain types
                    1430 => 'All Mountain',
                    1817 => 'All Mountain, Piste',
                    2030 => 'Allround',
                    2028 => 'All Mountain',
                    
                    // Freestyle type
                    1744 => 'Freestyle',
                    
                    // Junior
                    2031 => 'Junior'
                    ];
                  $result_names[$id] = isset($type_mapping[$id]) ? $type_mapping[$id] : 'Type ID: ' . $id;
                }
              }

                $length_range = $this->calculateSkiLengthRange(
                    (int)$form_data['height'],
                    (int)$form_data['weight'],
                    $form_data['skill_level'],
                    $form_data['style_preference']
                );

                // Generate URL filter
                $filter_url = $this->generateFilterUrl($type_ids, $length_range);
            }
        }

        $this->context->smarty->assign([
            'form_data' => $form_data,
            'skill_levels' => ['beginner', 'intermediate', 'advanced'],
            'style_preferences' => ['piste', 'all_mountain', 'race', 'freestyle'],
            'result_types' => $type_ids ?? [],
            'result_names' => $result_names,
            'length_range' => $length_range ?? null,
            'filter_url' => $filter_url ?? null,
            'show_result' => Tools::isSubmit('submit_ski_recommender') && empty($this->errors),
            'tooltips' => $tooltips,
            'errors' => $this->errors ?? []
        ]);

        $this->setTemplate('module:skirecommender/views/templates/front/form.tpl');
    }

    private function validateForm($data)
    {
        $this->errors = [];

        if ($data['weight'] < 25 || $data['weight'] > 200) {
            $this->errors[] = $this->module->l('Weight must be between 25 and 200 kg', 'form');
        }

        if ($data['height'] < 100 || $data['height'] > 240) {
            $this->errors[] = $this->module->l('Height must be between 100 and 240 cm', 'form');
        }

        if ($data['age'] < 5 || $data['age'] > 100) {
            $this->errors[] = $this->module->l('Age must be between 5 and 100 years', 'form');
        }

        return empty($this->errors);
    }

    private function determineSkiType($gender, $weight, $height, $age, $style_preference)
    {
        // Check for Junior first
        if ($age < 14 || $height < 140 || $weight < 40) {
            return [2031]; // Junior ski type ID
        }
        // Handle Lady Race case
        if ($gender === 'female' && $style_preference === 'race') {
            return [1431]; // Lady Race ID
        }

        // Get ski type IDs based on style preference
        return isset($this->equivalentTypes[$style_preference]) 
            ? $this->equivalentTypes[$style_preference] 
            : $this->equivalentTypes['piste']; // Default to piste
    }

    private function calculateSkiLengthRange($height, $weight, $skill_level, $style_preference)
    {
        // Base calculation
        $base_length = $height - 10;
        
        // Skill level adjustments
        $skill_adjustments = [
            'beginner' => -10,
            'intermediate' => 0,
            'advanced' => 5
        ];
        
        // Style adjustments
        $style_adjustments = [
            'freestyle' => -5,
            'race' => 5,
            'piste' => 0,
            'all_mountain' => 0
        ];

        // Calculate the range
        $min_length = $base_length + ($skill_adjustments[$skill_level] ?? 0) - 5;
        $max_length = $base_length + ($skill_adjustments[$skill_level] ?? 0) + 5;

        // Apply style adjustments
        if (isset($style_adjustments[$style_preference])) {
            $min_length += $style_adjustments[$style_preference];
            $max_length += $style_adjustments[$style_preference];
        }

        // Weight adjustments
        if ($weight > ($height - 100)) {
            $min_length += 5;
            $max_length += 5;
        } elseif ($weight < ($height - 120)) {
            $min_length -= 5;
            $max_length -= 5;
        }

        // Ensure lengths are within bounds
        $min_length = max(70, min(210, $min_length));
        $max_length = max(70, min(210, $max_length));

        return [
            'min' => (int)$min_length,
            'max' => (int)$max_length
        ];
    }

    private function generateFilterUrl($type_ids, $length_range)
    {
        $base_url = $this->context->link->getCategoryLink(59);
        $filter_parts = [];
        
        // Add length range
        if (isset($length_range['min']) && isset($length_range['max'])) {
            $lengths = [];
            for ($i = $length_range['min']; $i <= $length_range['max']; $i++) {
                $lengths[] = $i;
            }
            $filter_parts[] = 'Długość+narty-' . implode('-', $lengths);
        }
        
        $type_mapping = [
            // Piste types
            1911 => 'Piste',
            1435 => 'On Piste',
            1790 => 'Piste\\-Sport Performance',
            1793 => 'Piste\\-High Performance',
            1421 => 'Piste\\-Performance',
            
            // Race types
            1433 => 'Race',
            1553 => 'Race\\-Performance', 
            1610 => 'Race\\-On Piste',
            1789 => 'Race\\-Sport Performance',
            1792 => 'Race\\-High Performance',
            1431 => 'Lady Race',
            
            // All Mountain types
            1430 => 'All Mountain',
            1817 => 'All Mountain, Piste',
            2030 => 'Allround',
            
            // Freestyle type
            1744 => 'Freestyle',
            
            // Junior
            2031 => 'Junior'
        ];
        
        if (!empty($type_ids)) {
            $types = [];
            foreach ($type_ids as $id) {
                if (isset($type_mapping[$id])) {
                    $type = str_replace(' ', '+', $type_mapping[$id]);
                    // Special handling for comma
                    $type = str_replace(',', '%2C', $type);
                    $types[] = $type;
                }
            }
            if (!empty($types)) {
                $filter_parts[] = 'Typ+narty-' . implode('-', $types);
            }
        }
        
        $query = implode('%2F', $filter_parts);
        
        return $base_url . '?q=' . $query;
    }
}