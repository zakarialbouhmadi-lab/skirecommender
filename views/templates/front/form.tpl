{extends file='page.tpl'}

{block name='page_content'}

<div class="ski-recommender-container">
    <div class="ski-recommender-form">
        <div class="form-header">
            <h1>{l s='Find Your Perfect Ski' mod='skirecommender'}</h1>
            <p class="subtitle">{l s='Complete the form below to get personalized ski recommendations' mod='skirecommender'}</p>
            
            {if !empty($errors)}
                <div class="alert alert-danger">
                    <ul>
                        {foreach $errors as $error}
                            <li>{$error}</li>
                        {/foreach}
                    </ul>
                </div>
            {/if}
        </div>
        
   {if $show_result}
    <div class="recommendation-result">
        <h2>{l s='Your Perfect Ski Match' mod='skirecommender'}</h2>
        <div class="result-card">
            <div class="result-details">
                {assign var="first_type" value=reset($result_names)}

               
                 <div class="ski-type-info">
    <p class="recommended-type">
        {l s='Ski type:' mod='skirecommender'}
        <strong>
            {if !empty($result_names)}
                {assign var="first_type" value=reset($result_names)}
                {$first_type}
            {else}
                {l s='Standard' mod='skirecommender'}
            {/if}
        </strong>
    </p>
</div>



                {if isset($length_range)}
                    <p class="length">
                        {l s='Ski length:' mod='skirecommender'} 
                        <strong>{$length_range.min} - {$length_range.max} cm</strong>
                    </p>
                {/if}
                
                {if !empty($filter_url)}
                    <div class="see-products-btn-wrapper">
                        <a href="{$filter_url|escape:'html':'UTF-8'}" class="btn btn-primary see-products-btn">
                            {l s='See products' mod='skirecommender'}
                            <i class="material-icons">arrow_forward</i>
                        </a>
                    </div>
                {/if}
            </div>
        </div>
    </div>
{/if}

        <form action="" method="post" class="recommendation-form">
            <div class="form-row">
                <div class="form-group">
                    <label data-tooltip="{$tooltips.gender}">
                        {l s='Gender' mod='skirecommender'}
                        <span class="tooltip-icon">?</span>
                    </label>
                    <div class="select-wrapper">
                        <select name="gender" class="form-control">
                            <option value="male" {if $form_data.gender == 'male'}selected{/if}>
                                {l s='Male' mod='skirecommender'}
                            </option>
                            <option value="female" {if $form_data.gender == 'female'}selected{/if}>
                                {l s='Female' mod='skirecommender'}
                            </option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label data-tooltip="{$tooltips.age}">
                        {l s='Age' mod='skirecommender'}
                        <span class="tooltip-icon">?</span>
                    </label>
                    <input type="number" name="age" class="form-control" value="{$form_data.age}" required min="5" max="100">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label data-tooltip="{$tooltips.weight}">
                        {l s='Weight (kg)' mod='skirecommender'}
                        <span class="tooltip-icon">?</span>
                    </label>
                    <input type="number" name="weight" class="form-control" value="{$form_data.weight}" required min="25" max="200">
                </div>

                <div class="form-group">
                    <label data-tooltip="{$tooltips.height}">
                        {l s='Height (cm)' mod='skirecommender'}
                        <span class="tooltip-icon">?</span>
                    </label>
                    <input type="number" name="height" class="form-control" value="{$form_data.height}" required min="100" max="240">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label data-tooltip="{$tooltips.skill_level.title}">
                        {l s='Skill Level' mod='skirecommender'}
                        <span class="tooltip-icon">?</span>
                    </label>
                    <div class="select-wrapper">
                        <select name="skill_level" class="form-control">
                            {foreach from=$skill_levels item=level}
                                <option value="{$level}" 
                                        {if $form_data.skill_level == $level}selected{/if}
                                        data-tooltip="{$tooltips.skill_level[$level]}">
                                    {l s=$level|capitalize mod='skirecommender'}
                                </option>
                            {/foreach}
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label data-tooltip="{$tooltips.style_preference.title}">
                        {l s='Style Preference' mod='skirecommender'}
                        <span class="tooltip-icon">?</span>
                    </label>
                    <div class="select-wrapper">
                        <select name="style_preference" class="form-control">
                            {foreach from=$style_preferences item=style}
                                <option value="{$style}" 
                                        {if $form_data.style_preference == $style}selected{/if}
                                        data-tooltip="{$tooltips.style_preference[$style]}">
                                    {l s=$style|replace:'_':' '|capitalize mod='skirecommender'}
                                </option>
                            {/foreach}
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group submit-group">
                <button type="submit" name="submit_ski_recommender" class="btn btn-primary btn-large">
                    {l s='Get Your Ski Recommendation' mod='skirecommender'}
                </button>
            </div>
        </form>
    </div>
</div>
{/block}