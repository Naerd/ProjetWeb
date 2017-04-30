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

{* ATTENTION ! Pas d'espaces blanc volontairement ! Car positionnement en inline block *}
<div class="step step_first {if $step_active == 1}step_active{elseif $step_active > 1}step_passed{/if}"></div><div class="step_line {if $step_active > 1}line_active{/if}"></div><div class="step {if $step_active == 2}step_active{elseif $step_active > 2}step_passed{/if}"></div><div class="step_line {if $step_active > 2}line_active{/if}"></div><div class="step {if $step_active == 3}step_active{elseif $step_active > 3}step_passed{/if}"></div>{if $type_steps != "segment"}<div class="step_line {if $step_active > 3}line_active{/if}"></div><div class="step {if $step_active == 4}step_active{elseif $step_active > 4}step_passed{/if}"></div>{/if}{if $type_steps != "segment" && $type_steps != "sync"}<div class="step_line {if $step_active > 4}line_active{/if}"></div><div class="step {if $step_active == 5}step_active{elseif $step_active > 5}step_passed{/if}"></div><div class="step_line {if $step_active > 5}line_active{/if}"></div><div class="step {if $step_active == 6}step_active{elseif $step_active > 6}step_passed{/if}"></div>{if $type_steps == "campaign"}<div class="step_line {if $step_active > 6}line_active{/if}"></div><div class="step {if $step_active == 7}step_active{elseif $step_active > 7}step_passed{/if}"></div>{/if}{/if}