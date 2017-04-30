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
{if $start!=$stop && $total_results > 0}
	<ul class="dgo_pagination">
		{if $p != 1}
			{assign var='p_previous' value=$p-1}
			<li id="pagination_previous" class="pagination_previous">
				<a href="{$lien_pagination|escape:'htmlall':'UTF-8'}&num_page={$p_previous|intval}" rel="prev">
					<i class="material-icons">keyboard_arrow_left</i>
				</a>
			</li>
		{else}
			<li id="pagination_previous" class="disabled pagination_previous">
				<span>
					<i class="material-icons">keyboard_arrow_left</i>
				</span>
			</li>
		{/if}
		{if $start==3}
			<li>
				<a href="{$lien_pagination|escape:'htmlall':'UTF-8'}&num_page=1">
					<span>1</span>
				</a>
			</li>
			<li>
				<a href="{$lien_pagination|escape:'htmlall':'UTF-8'}&num_page=2">
					<span>2</span>
				</a>
			</li>
		{/if}
		{if $start==2}
			<li>
				<a href="{$lien_pagination|escape:'htmlall':'UTF-8'}&num_page=1">
					<span>1</span>
				</a>
			</li>
		{/if}
		{if $start>3}
			<li>
				<a href="{$lien_pagination|escape:'htmlall':'UTF-8'}&num_page=1">
					<span>1</span>
				</a>
			</li>
			<li class="truncate">
				<span>
					<span>...</span>
				</span>
			</li>
		{/if}
		{section name=pagination start=$start loop=$stop+1 step=1}
			{if $p == $smarty.section.pagination.index}
				<li class="active current">
					<span>
						<span>{$p|intval}</span>
					</span>
				</li>
			{else}
				<li>
					<a href="{$lien_pagination|escape:'htmlall':'UTF-8'}&num_page={$smarty.section.pagination.index|intval}">
						<span>{$smarty.section.pagination.index|intval}</span>
					</a>
				</li>
			{/if}
		{/section}
		{if $pages_nb>$stop+2}
			<li class="truncate">
				<span>
					<span>...</span>
				</span>
			</li>
			<li>
				<a href="{$lien_pagination|escape:'htmlall':'UTF-8'}&num_page={$pages_nb|intval}">
					<span>{$pages_nb|intval}</span>
				</a>
			</li>
		{/if}
		{if $pages_nb==$stop+1}
			<li>
				<a href="{$lien_pagination|escape:'htmlall':'UTF-8'}&num_page={$pages_nb|intval}">
					<span>{$pages_nb|intval}</span>
				</a>
			</li>
		{/if}
		{if $pages_nb==$stop+2}
			<li>
				<a href="{$lien_pagination|escape:'htmlall':'UTF-8'}&num_page={($pages_nb-1)|intval}">
					<span>{$pages_nb-1|intval}</span>
				</a>
			</li>
			<li>
				<a href="{$lien_pagination|escape:'htmlall':'UTF-8'}&num_page={$pages_nb|intval}">
					<span>{$pages_nb|intval}</span>
				</a>
			</li>
		{/if}
		{if $pages_nb > 1 AND $p != $pages_nb}
			{assign var='p_next' value=$p+1}
			<li id="pagination_next" class="pagination_next">
				<a href="{$lien_pagination|escape:'htmlall':'UTF-8'}&num_page={$p_next|intval}" rel="next">
					<i class="material-icons">keyboard_arrow_right</i>
				</a>
			</li>
		{else}
			<li id="pagination_next" class="disabled pagination_next">
				<span>
					<i class="material-icons">keyboard_arrow_right</i>
				</span>
			</li>
		{/if}
	</ul>
{/if}