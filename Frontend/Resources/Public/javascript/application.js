/**
 * Set timeout and if timout reached click a submit button
 * @param seconds
 * @param submitElement
 */
function timedSubmitInit(seconds,submitElement) {
    var timedSubmitCountdown = seconds;
    var timedSubmitClickElement = submitElement;
    var timedSubmitClickElementVal = submitElement.val();
    setInterval(function() {
        timedSubmitCountdown--;
        if(timedSubmitCountdown == -1) {
            timedSubmitClickElement.click();
        }
        else if(timedSubmitCountdown >= 0){
            timedSubmitClickElement.val(timedSubmitClickElementVal+' ('+timedSubmitCountdown+')');
        }
    },1000);
}

/**
 * Increment answere number (quick and dirty)
 * @param element
 */
function incAnswereNumber(element) {
	v = $(element).val();
    if(v=='a)') {
        element.val('b)');
    }
    if(v=='b)') {
        element.val('c)');
    }
    if(v=='c)') {
        element.val('d)');
    }
    if(v=='d)') {
        element.val('e)');
    }
    if(v=='e)') {
        element.val('f)');
    }
    if(v=='f)') {
        element.val('g)');
    }
    if(v=='g)') {
        element.val('h)');
    }
    //...
}

/**
 * Remove new lines from text
 * @param element
 */
function formatText(element) {
    element.val(element.val().replace(/\r?\n|\r/g,' '));
}

/**
 * display confirm modal
 * @param msg
 * @param labelMsg
 * @param closeMsg
 */
function modalConfirm(msg, locationHref, labelMsg, okMsg, closeMsg) {
	// Defaults
 	labelMsg = typeof labelMsg == 'undefined' ? 'Sicher?' : labelMsg;
    okMsg = typeof okMsg == 'undefined' ? 'Fortfahren' : okMsg;
    closeMsg = typeof closeMsg == 'undefined' ? 'Abbrechen' : closeMsg;

    // Do not try to display multiple modals
    if($('#modal').length) {
        $('#modal').remove();
    }

    // Generate modal
    modalTemplate = '' +
		'<div class="modal fade d-print-none" id="modal" tabindex="-1" role="dialog" aria-labelledby="jsModalLabel" aria-hidden="true">' +
			'<div class="modal-dialog">' +
				'<div class="modal-content">' +
					'<div class="modal-header">' +
						'<h4 class="modal-title" id="jsModalLabel">'+labelMsg+'</h4>' +
				        '<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">'+closeMsg+'</span></button>' +
					'</div>' +
					'<div class="modal-body">' +
						'' + msg +
					'</div>' +
					'<div class="modal-footer">' +
						'<button type="button" class="btn btn-danger" onclick="location.href=\''+locationHref+'\';">'+okMsg+'</button>' +
						'<button type="button" class="btn btn-default" data-dismiss="modal">'+closeMsg+'</button>' +
					'</div>' +
				'</div>' +
			'</div>' +
		'</div>';

    // Append modal to body and show it
    $('body').append(modalTemplate);
    $('#modal').modal({keyboard: true, focus:true, show:true});
}

/**
 * display alert modal
 * @param msg
 * @param labelMsg
 * @param closeMsg
 */
function modalAlert(msg, labelMsg, closeMsg) {
	// Defaults
	labelMsg = typeof labelMsg == 'undefined' ? 'Hinweis' : labelMsg;
    closeMsg = typeof closeMsg == 'undefined' ? 'Schließen' : closeMsg;

    // Do not try to display multiple modals
    if($('#modal').length) {
        $('#modal').remove();
    }

    // Generate modal
    modalTemplate = '' +
		'<div class="modal fade d-print-none" id="modal" tabindex="-1" role="dialog" aria-labelledby="jsModalLabel" aria-hidden="true">' +
			'<div class="modal-dialog">' +
				'<div class="modal-content">' +
					'<div class="modal-header">' +
						'<h4 class="modal-title" id="jsModalLabel">'+labelMsg+'</h4>' +
				        '<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">'+closeMsg+'</span></button>' +
					'</div>' +
					'<div class="modal-body">' +
						'' + msg +
					'</div>' +
					'<div class="modal-footer">' +
						'<button type="button" class="btn btn-default" data-dismiss="modal">'+closeMsg+'</button>' +
					'</div>' +
				'</div>' +
			'</div>' +
		'</div>';

    // Append modal to body and show it
    $('body').append(modalTemplate);
    $('#modal').modal({keyboard: true, focus:true, show:true});
}

function reloadContent(targetId,ctrl,action,params) {
    params = typeof params == 'undefined' ? '' : params;
    action = typeof action == 'undefined' ? '' : action;
    $.ajax({
        url: '/Frontend/?ctrl='+ctrl+'&action='+action+'&'+params,
        success: function(data){
            $('#'+targetId).html(data);

            $('.modal').modal({keyboard: true, focus: true, show: true});
            updateImageModals();
            updateConfirmATags();
            updateDatepickers();
            update_selectPickers();
            showModal();
        }
    });
}

/**
 * Send form as async request
 * @param formId
 * @param confirmMsg
 */
function ajaxForm(formId, confirmMsg) {
    confirmMsg = typeof confirmMsg == 'undefined' ? 'Das hat geklappt.' : confirmMsg;

	$.ajax({
		type: 'POST',
		url: $('#'+formId).attr('action'),
		data: $('#'+formId).serialize(),
		success: function(data) {
            modalAlert(confirmMsg);
		}
	});
}

/**
 * Mark question as favourite
 * @param question_id
 * @param confirmMsg
 */
function addFavourite(question_id, confirmMsg) {
    confirmMsg = typeof confirmMsg == 'undefined' ? 'Die Frage wurde als Favorit gespeichert.' : confirmMsg;

    $.ajax({
        type: 'GET',
        url: '/Frontend/?ctrl=Favourite&action=Add&question_id='+question_id,
        success: function(data) {
            modalAlert(confirmMsg);
        }
    });
}

/**
 * Remove question as favourite
 * @param question_id
 * @param confirmMsg
 */
function removeFavourite(question_id, confirmMsg) {
    confirmMsg = typeof confirmMsg == 'undefined' ? 'Die Frage wurde als Favorit entfernt.' : confirmMsg;

    $.ajax({
        type: 'GET',
        url: '/Frontend/?ctrl=Favourite&action=Remove&question_id='+question_id,
        success: function(data) {
            modalAlert(confirmMsg);
        }
    });
}

/**
 * Add comment and add html to result target
 * @param formId
 * @param resulttargetId
 */
function addComment(formId, resulttargetId) {

    $.ajax({
        type: 'POST',
        url: $('#'+formId).attr('action'),
        data: $('#'+formId).serialize(),
        success: function(data) {
            $('#'+resulttargetId).append(data);
        }
    });
}

/**
 * Call a frontend page
 * @param a (ctrl)
 * @param d (action)
 * @param p (parameters)
 */
function callAction(a, d, p) {
	if(p) {
        location.href = '/Frontend/?ctrl=' + a + '&action=' + d + '&' + p;
    }
    else {
        location.href = '/Frontend/?ctrl=' + a + '&action=' + d;
	}
}

/**
 * Set videos to 16 by 9 format and resize to fit parent element
 */
function resizeVideoPlayers() {
    $(".video-js").each(function (i, element) {
        playerWidth = Math.round($(element).parent().width()) - 2; // sub 2 px because border
        playerHeight = Math.round(playerWidth / 1.777777777777778);
        $(element).width(playerWidth + 'px');
        $(element).height(playerHeight + 'px');
    });
}

/**
 * update image modals opened by lightbox link
 */
function updateImageModals() {
	$('.lightbox').on("click", function() {
		$('#imagepreview').attr('src', this);
		$('#imagemodal').addClass('modal fade');
		$('#imagemodal').modal('show');
		return false;
	});
}

/**
 * Ask on confirmation for links with class 'confirm'
 */
function updateConfirmATags() {
	$('.confirm').each(function (i, element) {
		$(element).on('click', function () {
            modalConfirm($(element).attr('confirmation-message'), $(element).attr('href'));
			return false;
		});
	});
}

/**
 * Activate datepicker fields
 */
function updateDatepickers() {
    $('.datepicker-ddmmjjj').each(function(){
        $(this).datepicker({
            format: 'dd.mm.yyyy',
            todayHighlight: true,
            language: 'de'
        }).on('changeDate',function (e) {
            if($(e.target).attr('update-on-change')) {
                $('#'+$(e.target).attr('update-on-change')).datepicker('update', $(e.target).val());
            }
        });
    });
}

/**
 * Active select picker fields
 */
function update_selectPickers() {
    $('.selectpicker').selectpicker(getSelectpickerConfig());
}

/**
 * Show modal
 */
function showModal() {
    $('.modal').modal({keyboard: true, focus: true, show: true});
}

/**
 * download data in a file of a type
 * @param string data
 * @param string filename
 * @param string type
 */
function getApiKeyFile(data, filename, type) {
	var file = new Blob([data], {type: type});
	if (window.navigator.msSaveOrOpenBlob) // IE10+
		window.navigator.msSaveOrOpenBlob(file, filename);
	else { // Others
		var a = document.createElement("a"),
			url = URL.createObjectURL(file);
		a.href = url;
		a.download = filename;
		document.body.appendChild(a);
		a.click();
		setTimeout(function() {
			document.body.removeChild(a);
			window.URL.revokeObjectURL(url);
		}, 0);
	}
}

/**
 * get datatables language strings
 */
function getDatatablesLanguageStr() {
	return {
		sEmptyTable: "Keine Daten in der Tabelle vorhanden",
		sInfo: "_START_ bis _END_ von _TOTAL_ Einträgen",
		sInfoEmpty: "0 bis 0 von 0 Einträgen",
		sInfoFiltered: "(gefiltert von _MAX_ Einträgen)",
		sInfoPostFix: "",
		sInfoThousands: ".",
		sLengthMenu: "_MENU_ Einträge anzeigen",
		sLoadingRecords: "Wird geladen...",
		sProcessing: "Bitte warten...",
		sSearch: "Suchen",
		sZeroRecords: "Keine Einträge vorhanden.",
		oPaginate: {
			sFirst: "Erste",
			sPrevious: "Zurück",
			sNext: "Nächste",
			sLast: "Letzte"
		},
		oAria: {
			sSortAscending: ": aktivieren, um Spalte aufsteigend zu sortieren",
			sSortDescending: ": aktivieren, um Spalte absteigend zu sortieren"
		},
		select: {
			rows: {
				_: '%d Zeilen ausgewählt',
				0: 'Zum Auswählen auf eine Zeile klicken',
				1: '1 Zeile ausgewählt'
			}
		}
	};
}

/**
 * get tiny mce config
 */
function getTinyMCEConfig() {
	return {
        selector: 'textarea.mce',
        plugins: 'link lists paste',
        toolbar: 'undo redo paste | bold italic underline strikethrough | bullist numlist outdent indent | alignleft aligncenter alignright alignjustify | link | removeformat ',
        menubar: false,
        statusbar: false,
        language: 'de',
        language_url: '/Frontend/Resources/Public/javascript/tinymce/langs/de.js',
        relative_urls : false,
        remove_script_host : false,
        document_base_url : "//waffensachkunde-trainer.de/"
    };
}

/**
 * get selectpicker config
 */
function getSelectpickerConfig() {
	return {
        style: 'btn-default',
        size: 10
    };
}
