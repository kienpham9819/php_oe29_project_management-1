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

    $("#k").change(function () {
        var k = $(this).val();
        $.get("showChart/"+k, function (data) {
            $("#view_data").text(data);
            var data = $.parseJSON(data);
            var labels = [];
            var result = [];
            for (var i in data) {
                labels.push(data[i].task_list_id);
                result.push(data[i].total);
            }


            Chart.defaults.global.defaultFontColor = '#000000';
            Chart.defaults.global.defaultFontFamily = 'Arial';
            Chart.defaults.global.defaultFontSize = 15;
            var lineChart = $('#line-chart');
            var myChart = new Chart(lineChart, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Completed task',
                            data: result,
                            backgroundColor: 'rgba(0, 128, 128, 0.3)',
                            borderColor: 'rgba(0, 128, 128, 0.7)',
                            borderWidth: 1
                        },

                    ]
                },
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero:true
                            }
                        }]
                    },
                }
            });
        });
    });
});
