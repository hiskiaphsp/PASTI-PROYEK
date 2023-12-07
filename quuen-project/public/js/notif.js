$(document).ready(function () {
    counter_notif(localStorage.getItem("route_counter_notif"));
    load_notif(localStorage.getItem("route_notification")); // Call the function initially to load notifications
    setInterval(function () {
        counter_notif(localStorage.getItem("route_counter_notif"));
        load_notif(localStorage.getItem("route_notification")); // Call the function periodically to update notifications
    }, 5000); // Adjust the interval (in milliseconds) as needed
});

$("#notification").on("mouseenter", function () {
    markRead(localStorage.getItem("route_notification_read"));
});

function tombol_notif() {
    counter_notif(localStorage.getItem("route_counter_notif"));
    markRead(localStorage.getItem("route_notification_read"));
}

function counter_notif(url) {
    $.ajax({
        type: "GET",
        url: url,
        dataType: "json",
        success: function (response) {
            if (response.total > 0) {
                $("#top-notification-number").html(response.total);
                $("#jmlh-notif").html(response.total);
                console.log(response.total);
            } else {
                $("#top-notification-number").html(0);
                $("#jmlh-notif").html(0);
                console.log(response.total);
            }
        },
    });
}

function load_notif(url) {
    // let data = "view="+ view + "&load_keranjang=";
    $.ajax({
        type: "GET",
        url: url,
        dataType: "json",
        success: function (response) {
            $("#notification_items").html(response.notifications);
            $("#top-notification-number").html(response.total ?? 0);
        },
    });
}

function markRead(url) {
    // let data = "view="+ view + "&load_keranjang=";
    $.ajax({
        type: "GET",
        url: url,
        dataType: "json",
        success: function (response) {
            $("#notification_items").html(response.notifications);
            $("#top-notification-number").html(response.total ?? 0);
        },
    });
}

function deleteNotification(notificationId) {
    $.ajax({
        url: "notification/" + notificationId + "/delete",
        type: "DELETE",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (response) {
            // Berhasil menghapus notifikasi
            console.log(response);
            // Refresh halaman setelah notifikasi dihapus
            location.reload();
        },
        error: function (xhr, status, error) {
            // Error saat menghapus notifikasi
            console.log(xhr.responseText);
        },
    });
}
