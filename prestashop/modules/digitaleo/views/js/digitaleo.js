/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to a commercial license from DIGITALEO SAS
 * Use, copy, modification or distribution of this source file without written
 * license agreement from the DIGITALEO SAS is strictly forbidden.
 *
 *  @author		Digitaleo
 *  @copyright 	2016 Digitaleo
 *  @license 	All Rights Reserved
 */
var url_global = "index.php?tab=AdminDigitaleo&token="+token_tab;
var target_toggle;
var offset_templates = 0;

$(document).ready(function()
{
	$('.dgo_btn_mode').click(function()
	{
		$('.dgo_btn_mode').removeClass('active');
		$(this).addClass('active');
		$('#'+$(this).attr('data-type')).val($(this).attr('data-auto'));

		if ($(this).attr('data-type') == "notification_type")
		{
			if ($(this).attr('data-auto') == "admin")
			{
				$(".dgo_display_admin_contacts").show();
			} else {
				$(".dgo_display_admin_contacts").hide();
			}
		}
	});

	$(".digitaleo button[data-toggle='dropdown']").click(function(event)
	{
		if (target_toggle != $(this).attr("data-target"))
			$(".digitaleo #"+target_toggle).hide();
		
		target_toggle = $(this).attr("data-target");
		$(".digitaleo #"+target_toggle).toggle();
		event.stopPropagation();
	});

	$(window).click(function() {
		$(".digitaleo #"+target_toggle).hide();
	});

	$('.dgo_switch').click(function()
	{
		var type = $(this).attr("data-type");
		var id = $(this).attr("data-id");
		var val = $(this).attr("data-value");

		if (!$(this).hasClass("active"))
		{
			$.ajax({
				type: 'POST',
				url: url_global + "&ajax=ajax_active_"+type,
				async: true,
				cache: false,
				dataType : "html",
				data: 'id='+id+'&active='+val,
				success: function(html)
				{
					$(".digitaleo a[data-id="+id+"]").removeClass("active");
					$(".digitaleo a[data-id="+id+"][data-value="+val+"]").addClass("active");
				}
			});
		}
	});

	$('select[name="notification_event"]').click(function(){
		if ($(this).val() == 'hook_cart_abandonment') {
			$('.delay_cart_abandonment').show();
		} else {
			$('.delay_cart_abandonment').hide();
		}
	});

	$('.sms_content').on('input propertychange', function()
	{
		smsCountChars();
		fillSms();
	});

	$('.dgo_sms_select_field').on('change', function()
	{
		$(".sms_content").val($(".sms_content").val()+$(this).val());
		smsCountChars();
		fillSms();
		$('.dgo_sms_select_field').prop('selectedIndex',0);
		$(".sms_content").focus();
	});

	if ($(".datepicker").length > 0)
		$(".datepicker").datepicker({
			prevText: '',
			nextText: '',
			dateFormat: 'yy-mm-dd'
		});

	if ($(".datetimepicker").length > 0)
		$('.datetimepicker').datetimepicker({
			prevText: '',
			nextText: '',
			dateFormat: 'dd/mm/yy',
			// Define a custom regional settings in order to use PrestaShop translation tools
			currentText: 'Maintenant',
			closeText: 'Valider',
			ampm: false,
			amNames: ['AM', 'A'],
			pmNames: ['PM', 'P'],
			timeFormat: 'hh:mm tt',
			timeSuffix: '',
			timeOnlyTitle: 'Choisir l\'heure',
			timeText: 'Heure',
			hourText: 'Heure',
			minuteText: 'Minute',
		});

	// Initialisation du champs SMS
	fillSms();

	// Youtube
	$('#player_vid').on('click', function(ev)
	{
		$("#player_vid").hide();
		$("#video").show();
		$("#video")[0].src += "&autoplay=1";
		ev.preventDefault();
	});

	$('.video_link').click(function(){
		$($(this).attr('href')).fadeIn(300);
	});
	$('#videos').find('div').click(function(){
		$(this).fadeOut();
	});
	$('#dropdown').find('i').click(function(){
		$('#dropdown').find('.content').fadeToggle(200);
	});

});

function launch_sync(id_sync)
{
	var total_sync = parseInt($('.total_sync').html());
	$.ajax({
		type: 'POST',
		url: url_global + "&ajax=ajax_do_sync",
		async: true,
		cache: false,
		dataType : "json",
		data: 'id_sync='+id_sync,
		success: function(data)
		{
            if (data.error) {
                $('.error').html(data.error).show();
            } else if (!data.end) {
				$('.nb_current_sync_'+id_sync).html(data.nb_sync);
				pourc = parseInt(data.nb_sync)*100/total_sync;

				$('.dgo_progress').css("width", (pourc*3)+"px");
				launch_sync(id_sync);
			} else {
				// Affichage des boutons
				$('.dgo_img_loader').attr("src", "/modules/digitaleo/views/img/valid.png")
				$('.btn_end_sync').show();
			}
		}
	});
}

function launch_sync_list(id_sync, id_list)
{
	$('#sync_refresh_btn'+id_sync).hide();
	$('#sync_refresh_count'+id_sync).show();
	$.ajax({
		type: 'POST',
		url: url_global + "&ajax=ajax_do_sync",
		async: true,
		cache: false,
		dataType : "json",
		data: 'id_sync='+id_sync+'&id_list='+id_list,
		success: function(data)
		{
            if (data.error) {
                $('.sync_refresh_btn').show();
                $('.sync_refresh_count').hide();
                alert(data.error);
            } else if (!data.end) {
                $('.nb_current_sync_'+id_sync).html(data.nb_sync);

                launch_sync_list(id_sync, id_list);
            } else {
                $('#sync_refresh_count'+id_sync+' img').attr("src", "/modules/digitaleo/views/img/valid.png");
                $('#sync_refresh_btn'+id_sync).hide();
            }
		}
	});
}

function confirm_delete(url)
{
	if (confirm(l_confirm_delete))
	{
		location.href = url;
	}
}

function choose_filter()
{
	var choice = $('#segment_select_filter').val();

	if (choice != "")
	{
		$.ajax({
			type: 'GET',
			url: url_global + "&ajax=ajax_get_segment_filter",
			async: true,
			cache: false,
			dataType : "html",
			data: 'choice='+choice,
			success: function(html)
			{
				$('.segment_filter_choose').append(html);
				$("#segment_select_filter option[value='"+choice+"']").attr("disabled", "disabled");
				$("#segment_select_filter option:first").attr('selected','selected');

				if ($(".datepicker").length > 0)
					$(".datepicker").datepicker({
						prevText: '',
						nextText: '',
						dateFormat: 'yy-mm-dd'
					});
			}
		});
	}
}

function remove_filter(type_filter)
{
	if (confirm(texte_delete_segment))
	{
		$("#filter_"+type_filter).remove();
		$("#segment_select_filter option[value='"+type_filter+"']").prop("disabled", false);
	}
}

function sms_test()
{
	var sms_test_number = $('#sms_test_number').val();

	$(".btn_sms").hide();
	$(".loader_sms").attr("src", "/modules/digitaleo/views/img/ajax-loader.gif").show();

	// TODO : Faire un pattern de test ?
	if (sms_test_number.length > 8)
	{
		$.ajax({
			type: 'POST',
			url: url_global + "&ajax=ajax_sms_test",
			async: true,
			cache: false,
			dataType : "json",
			data: 'sms_test_number='+sms_test_number,
			success: function(data)
			{
                if (data.error) {
                    $('.display_error').show();
                    $(".loader_sms").attr("src", "/modules/digitaleo/views/img/ajax-loader.gif").hide();
                    $('.error').html(data.error).show();
                } else {
					$('.display_error').hide();
                    $(".loader_sms").attr("src", "/modules/digitaleo/views/img/valid.png");
                }
                $(".btn_sms").show();
			}
		});
	}
}

function email_test()
{
	var email_test = $('#email_test').val();

	$(".btn_email").hide();
	$(".loader_email").attr("src", "/modules/digitaleo/views/img/ajax-loader.gif").show();

	// TODO : Faire un pattern de test ?
	if (email_test.length > 8)
	{
		$.ajax({
			type: 'POST',
			url: url_global + "&ajax=ajax_email_test",
			async: true,
			cache: false,
			dataType : "json",
			data: 'email_test='+email_test,
			success: function(data)
			{
                if (data.error) {
                    $('.display_error').show();
                    $(".loader_email").attr("src", "/modules/digitaleo/views/img/ajax-loader.gif").hide();
                    $('.error').html(data.error).show();
                } else {
					$('.display_error').hide();
                    $(".loader_email").attr("src", "/modules/digitaleo/views/img/valid.png");
                }
                $(".btn_email").show();
			}
		});
	}
}

function smsCountChars()
{
	var tmpText = $(".sms_content").val();

	//operation to clean special char
	tmpText = tmpText.replace(/<span class="invalidchar">/g, '');
	tmpText = tmpText.replace(/<\/span>/g, '');

	// Transformation caracteres doubles par '12' pour les compter
	tmpText = tmpText.replace(/€|\f|\^|\~|\\|\||\[|\]|\{|\}/g, '12');

	// Si TPOA on supprime le retour a la ligne de la preview
	tmpText = tmpText.replace(/<br \/>/g, '\n');

	var nbChars = tmpText.length;

	$(".sms_nb_chars").html(nbChars);

	var nbSms = 1;
	if (nbChars > 160)
		nbSms = Math.floor(1 + (nbChars/160));

	$(".sms_nb_sms").html(nbSms);
}

function fillSms()
{
	var texte = $(".sms_content").val();

	if (texte != undefined && texte.length > 0)
	{
		texte = smsReplaceChars(texte);

		$(".sms_content").html(texte);
		$(".sms").show();
	}

}

function smsReplaceChars(str)
{
	// REMPLACEMENT TABs ET RCs...
	return str.replace(/\t/g, ' ')
	// suppression des espaces multiples
	.replace(/ +/g, ' ')
	// SUPPRESSIONS DES BLANCS AVANT FIN DE LIGNE
	.replace(/ \n/g, '\n')
	// MODIFICATION DE CERTAINS CARACTERES
	.replace(/°/g, 'o')
	.replace(/`|’/g, '\'')
	.replace(/oe/g, 'oe')
	.replace(/…/g, '...')
	// ACCENT CIRCONFLEXE
	.replace(/â/g, 'a')
	.replace(/ê/g, 'e')
	.replace(/î/g, 'i')
	.replace(/ô/g, 'o')
	.replace(/û/g, 'u')
	.replace(/Â/g, 'A')
	.replace(/Ê/g, 'E')
	.replace(/Î/g, 'I')
	.replace(/Ô/g, 'O')
	.replace(/Û/g, 'U')
	// TREMA
	.replace(/ë/g, 'e')
	.replace(/ï/g, 'i')
	.replace(/ö/g, 'o')
	.replace(/ü/g, 'u')
	.replace(/Ë/g, 'E')
	.replace(/Ï/g, 'I')
	.replace(/Ö/g, 'O')
	.replace(/Ü/g, 'U');
}

function selectTemplate(id)
{
	$('.dgo_tpl').removeClass("active");
	$('#dgo_link_tpl'+id).addClass("active");
	$("#campaign_id_template").val(id);
	$("#notification_id_template").val(id);
}

function more_templates()
{
	offset_templates += 20;

	$('.btn_more_templates').hide();
	$('.loader_templates').show();

	$.ajax({
		type: 'POST',
		url: url_global + "&ajax=ajaxGetTemplates",
		async: true,
		cache: false,
		dataType : "html",
		data: 'offset='+offset_templates,
		success: function(html)
		{
			$('.btn_more_templates').show();
			$('.loader_templates').hide();
            $(html).appendTo('.templates_digitaleo');
		}
	});
}