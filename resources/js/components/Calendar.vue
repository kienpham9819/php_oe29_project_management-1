<template>
    <div>
        <div class="container text-center">
            <div class="p-3">
                <div id="header mb-3">
                    <a class="badge badge-pill badge-primary p-2" @click="prevMonth()">
                        <i class="fa fa-fw fa-chevron-left "></i>
                    </a>
                    <span class="font-weight-bold text-uppercase">
                        {{ currentYearMonth }}
                    </span>
                    <a class="badge badge-pill badge-primary p-2" @click="nextMonth()">
                        <i class="fa fa-fw fa-chevron-right "></i>
                    </a>
                </div>
            </div>
            <div>
                <div class="row w-100 pl-3">
                    <strong class="date-frame p-2" v-for="day in weekday">
                        {{ day }}
                    </strong>
                </div>
                <div class="row w-100 pl-3">
                    <div class="date-frame p-2" v-for="blank in firstDayOfMonth">
                        &nbsp;
                    </div>
                    <div class="date-frame p-2" v-for="date in daysInMonth">
                        <span v-if="date == due_d && (month + 1) == due_m && year == due_y" class="border rounded-pill p-1 border-danger text-danger font-weight-bold">
                            {{ date }}
                        </span>
                        <span v-else-if="date == current.getDate()" class="border rounded-pill p-1 border-primary text-primary font-weight-bold">
                            {{ date }}
                        </span>
                        <span v-else>
                            {{ date }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    export default {

        props: {
            locale: {default: "en"},
            due_y: {default: 0},
            due_m: {default: 0},
            due_d: {default: 0},
        },

        data() {

            return {
                today: new Date(),
                current: new Date(),
                weekday: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
            }
        },

        computed: {
            currentYearMonth: function()
            {
                return Intl.DateTimeFormat(this.locale, { year: 'numeric', month: 'long' })
                .format(this.current);
            },
            year: function()
            {
                return this.current.getFullYear();
            },
            month: function()
            {
                return this.current.getMonth();
            },
            day: function()
            {
                return this.current.getDate();
            },
            daysInMonth: function()
            {
                let daysInMonth = new Date(this.year, this.month + 1, 0).getDate();
                return daysInMonth;
            },
            firstDayOfMonth: function()
            {
                let firstDayOfMonth = new Date(this.year, this.month).getDay();
                return firstDayOfMonth;
            },
        },

        methods: {
            nextMonth() {
                let nextMonth = this.month + 1;
                if (this.year == this.today.getFullYear() && nextMonth == this.today.getMonth()) {
                    this.current = new Date(this.year, nextMonth, this.today.getDate());
                } else {
                    this.current = new Date(this.year, nextMonth, 1);
                }
            },
            prevMonth() {
                let prevMonth = this.month - 1;
                if (this.year == this.today.getFullYear() && prevMonth == this.today.getMonth()) {
                    this.current = new Date(this.year, prevMonth, this.today.getDate());
                } else {
                    this.current = new Date(this.year, prevMonth, 1);
                }
            }
        }

    }
</script>
