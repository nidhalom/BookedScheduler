<div class="form-group {$class}">
    {if $readonly}
        <label class="customAttribute readonly" for="{$attributeId}">{$attribute->Label()}</label>
        <span class="attributeValue {$class}">{if $attribute->Value() == "1"}{translate key='True'}{else}{translate key='False'}{/if}</span>
    {elseif $searchmode}
        <label class="customAttribute search" for="{$attributeId}">{$attribute->Label()}</label>
        <select id="{$attributeId}" name="{$attributeName}" class="customAttribute form-control {$inputClass}">
            <option value="">--</option>
            <option value="0" {if $attribute->Value() == "0"}selected="selected"{/if}>{translate key=No}</option>
            <option value="1" {if $attribute->Value() == "1"}selected="selected"{/if}>{translate key=Yes}</option>
        </select>
    {else}
        <div class="checkbox">
            <input type="checkbox" value="1" id="{$attributeId}" name="{$attributeName}"
                   {if $attribute->Value() == "1"}checked="checked"{/if} class="{$inputClass}"/>
            <label class="customAttribute standard" for="{$attributeId}">{$attribute->Label()}
                {if $attribute->Required() && !$searchmode}
                    <i class="glyphicon glyphicon-asterisk"></i>
                {/if}
            </label>
        </div>
    {/if}
</div>
