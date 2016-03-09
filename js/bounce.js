$(document).ready(function() {
	$(".children").hide();
});
	
/* Function name: showChildren
 * Parameters: number - the master ticket number
 * Description: Toggles the visibility of children tickets
 */
function showChildren(number) {
	$("#p" + number).toggle();
	if($("#p" + number).is(':visible')) {
		$("#expand" + number).text("-");
	} else {
		$("#expand" + number).text("+");
	}
}

function expandAll() {
	if ($("#toggleButton").text() == "Expand All") {
		$(".children").show();
		$("#toggleButton").text("Collapse All");
		$(".expand").text('-');
	} else {
		$(".children").hide();
		$("#toggleButton").text("Expand All");
		$(".expand").text('+');
	}
}

function scrollTop() {
	$('body').scrollTop(0);
}