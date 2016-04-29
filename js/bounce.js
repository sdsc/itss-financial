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

function getSortedOptions() {
	var options;
	var costLength = $('input[name="sortCost"]:checked').length;
	var costSort;
	if (costLength > 0) {
		costSort = "sortBy[]=" + $('input[name="sortCost"]:checked').val();
	} else {
		costSort = "";
	}

	var newTicketLength = $('input[name="onlyNew"]:checked').length;
	var onlyNewSort;
	if (newTicketLength > 0) {
		onlyNewSort = "&sortBy[]=" + $('input[name="onlyNew"]:checked').val();
	} else {
		onlyNewSort = "";
	}

	var reasons = "";
	$('input[name="sortReason"]:checked').map(function() {
		reasons += "&reasons[]=" + this.value;
	});
	console.log(costSort);
	console.log(onlyNewSort);
	console.log(reasons);
	window.location.href = "../pages/bounce-reports.php?" + costSort + onlyNewSort + reasons;
}

$('input[data-column="closeDate"]').tooltip({ /*or use any other selector, class, ID*/
    placement: "top",
    trigger: "focus"
});