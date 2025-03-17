$(document).ready(function () {
    $("#add-passenger").click(function () {
        var passengerForm = $(".passenger").first().clone();

        passengerForm.find("input").val("");
        passengerForm.find("select").prop("selectedIndex", 0);

        $("#passenger-section").append(passengerForm);

        newTotalPrice();
    });

    function newTotalPrice() {
        var ticketPrice = parseFloat($("#flight-price").data("price"));
        var numPassenger = $(".passenger").length;
        var totalPrice = ticketPrice * numPassenger;

        $("#total-price").text("Total: $" + totalPrice.toFixed(2));
    }

    newTotalPrice();
});
