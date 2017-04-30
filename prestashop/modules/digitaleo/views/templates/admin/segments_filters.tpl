{*
* NOTICE OF LICENSE
*
* This source file is subject to a commercial license from DIGITALEO SAS
* Use, copy, modification or distribution of this source file without written
* license agreement from the DIGITALEO SAS is strictly forbidden.
*
*  @author		Digitaleo
*  @copyright 	2016 Digitaleo
*  @license 	All Rights Reserved
*}
{if $segment_countries || (isset($smarty.get.choice) && $smarty.get.choice == "countries")}
<p class="dgo_liste_segment" id="filter_countries">
	{l s='Countries' mod='digitaleo'} : 
	<select multiple="multiple" name="segment_countries[]">
		{foreach from=$countries item=country}
		<option value="{$country.id_country|intval}"{if !empty($segment_countries) && in_array($country.id_country, $segment_countries)} selected="selected"{/if}>{$country.name|escape:'htmlall':'UTF-8'}</option>
		{/foreach}
	</select>
	<a href="javascript:;" onclick="remove_filter('countries')"><i class="material-icons">delete</i></a>
</p>
{/if}
{if $segment_age_from || $segment_age_to || (isset($smarty.get.choice) && $smarty.get.choice == "age")}
<p class="dgo_liste_segment" id="filter_age">
	{l s='Age' mod='digitaleo'} : 
	{l s='From2' mod='digitaleo'} <input type="text" name="segment_age_from" size="3" value="{$segment_age_from|intval}" /> {l s='To2' mod='digitaleo'} <input type="text" name="segment_age_to" size="3" value="{$segment_age_to|intval}" /> {l s='years old' mod='digitaleo'}
	<a href="javascript:;" onclick="remove_filter('age')"><i class="material-icons">delete</i></a>
</p>
{/if}
{if ($segment_registration_from && $segment_registration_from != '0000-00-00') || ($segment_registration_to && $segment_registration_to != '0000-00-00') || (isset($smarty.get.choice) && $smarty.get.choice == "registration")}
<p class="dgo_liste_segment" id="filter_registration">
	{l s='Registration date' mod='digitaleo'} : 
	{l s='From' mod='digitaleo'} <input type="text" class="datepicker" name="segment_registration_from" value="{$segment_registration_from|escape:'htmlall':'UTF-8'}" /> {l s='To' mod='digitaleo'} <input type="text"  class="datepicker"  name="segment_registration_to" value="{$segment_registration_to|escape:'htmlall':'UTF-8'}" />
	<a href="javascript:;" onclick="remove_filter('registration')"><i class="material-icons">delete</i></a>
</p>
{/if}
{if ($segment_newsletter !== false && $segment_newsletter != 2) || (isset($smarty.get.choice) && $smarty.get.choice == "newsletter")}
<p class="dgo_liste_segment" id="filter_newsletter">
	{l s='Newsletter registered' mod='digitaleo'} : 
	<input type="radio" name="segment_newsletter" value="1" id="radio_newsl_1"{if $segment_newsletter == 1} checked="checked"{/if} /> <label for="radio_newsl_1">{l s='Yes' mod='digitaleo'}</label>
	<input type="radio" name="segment_newsletter" value="0" id="radio_newsl_0"{if $segment_newsletter == 0} checked="checked"{/if} /> <label for="radio_newsl_0">{l s='No' mod='digitaleo'}</label>
	<a href="javascript:;" onclick="remove_filter('newsletter')"><i class="material-icons">delete</i></a>
</p>
{/if}
{if ($segment_optin !== false && $segment_optin != 2) || (isset($smarty.get.choice) && $smarty.get.choice == "optin")}
<p class="dgo_liste_segment" id="filter_optin">
	{l s='Optin registered' mod='digitaleo'} : 
	<input type="radio" name="segment_optin" value="1" id="radio_optin_1"{if $segment_optin == 1} checked="checked"{/if} /> <label for="radio_optin_1">{l s='Yes' mod='digitaleo'}</label>
	<input type="radio" name="segment_optin" value="0" id="radio_optin_0"{if $segment_optin == 0} checked="checked"{/if} /> <label for="radio_optin_0">{l s='No' mod='digitaleo'}</label>
	<a href="javascript:;" onclick="remove_filter('optin')"><i class="material-icons">delete</i></a>
</p>
{/if}
{if $segment_genre || (isset($smarty.get.choice) && $smarty.get.choice == "genre")}
<p class="dgo_liste_segment" id="filter_genre">
	{l s='Genre' mod='digitaleo'} : 
	<input type="radio" name="segment_genre" value="H" id="radio_genre_H"{if $segment_genre == 'H'} checked="checked"{/if} /> <label for="radio_genre_H">{l s='Man' mod='digitaleo'}</label>
	<input type="radio" name="segment_genre" value="F" id="radio_genre_F"{if $segment_genre == 'F'} checked="checked"{/if} /> <label for="radio_genre_F">{l s='Women' mod='digitaleo'}</label>
	<input type="radio" name="segment_genre" value="" id="radio_genre_A" /> <label for="radio_genre_A">{l s='All' mod='digitaleo'}</label>
	<a href="javascript:;" onclick="remove_filter('genre')"><i class="material-icons">delete</i></a>
</p>
{/if}
{if $segment_orders_from !== false || $segment_orders_to !== false || (isset($smarty.get.choice) && $smarty.get.choice == "orders")}
<p class="dgo_liste_segment" id="filter_orders">
	{l s='Number of Orders' mod='digitaleo'} : 
	{l s='From2' mod='digitaleo'} <input type="text" size="3" name="segment_orders_from" value="{$segment_orders_from|intval}" /> {l s='To2' mod='digitaleo'} <input type="text" size="3" name="segment_orders_to" value="{$segment_orders_to|intval}" />
	<a href="javascript:;" onclick="remove_filter('orders')"><i class="material-icons">delete</i></a>
</p>
{/if}
{if $segment_groups || (isset($smarty.get.choice) && $smarty.get.choice == "groups")}
<p class="dgo_liste_segment" id="filter_groups">
	{l s='Customer groups' mod='digitaleo'} : 
	<select multiple="multiple" name="segment_groups[]">
		<option value="">{l s='All' mod='digitaleo'}</option>
		{foreach from=$groups item=group}
		<option value="{$group.id_group|intval}"{if !empty($segment_groups) && in_array($group.id_group, $segment_groups)} selected="selected"{/if}>{$group.name|escape:'htmlall':'UTF-8'}</option>
		{/foreach}
	</select>
	<a href="javascript:;" onclick="remove_filter('groups')"><i class="material-icons">delete</i></a>
</p>
{/if}