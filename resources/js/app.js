/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('calendar', require('./components/Calendar.vue').default);
Vue.component('task-input', require('./components/TaskInput.vue').default);
Vue.component('comment', require('./components/Comment.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
});

$(document).ready(function () {
    $("#search_user").on("keyup", function () {
        var value = $(this).val().toLowerCase();
        $(".dropdown-menu li").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });

    $("#search_lecturer").on("keyup", function () {
        var value = $(this).val().toLowerCase();
        $("#listLecturer li").filter(function () {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
    $('.noti').delay(2000).slideUp();

    // var pusher = new Pusher(process.env.MIX_PUSHER_APP_KEY, {
    //     encrypted: true,
    //     cluster: process.env.MIX_PUSHER_APP_CLUSTER
    // });
    // var channel = pusher.subscribe('NotificationEvent');

    // channel.bind('warning-message', function(data) {
    //     var user_id = $('#userId').val();
    //     var newNotificationHtml = "";
    //     if (user_id == data.user_id) {
    //         var newNotificationHtml = `<a class="dropdown-item p-2 d-block" href="#">
    //         <span>${data.tasklistName}</span><br></a>`;
    //         $('.menu-notification').prepend(newNotificationHtml);
    //         $('#none-content').remove();
    //     }
    // });

    // Echo.channel('warning-message')
    //     .listen('NotificationEvent', (e) => {
    //         alert(e.data.tasklistName);
    //     });


    Echo.private('warning-message')
        .listen('NotificationEvent', (e) => {
            var user_id = $('#userId').val();
            var qt = parseInt($('#qt').text());
            if (user_id == e.data.user_id) {
                qt+=1;
                var newNotificationHtml = `<a class="dropdown-item p-2 d-block" href="#">
                <span>${e.data.tasklistName}</span><br></a>`;
                $('#none-content').remove();
                $('.menu-notification').prepend(newNotificationHtml);
                $('#qt').text(qt);
            }
       });


});
