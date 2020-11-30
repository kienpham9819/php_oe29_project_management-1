<template>
    <div>
        <div class="fixed">
            <div>
                <a class="bg-dark pointer text-white font-weight-bold text-decoration-none pl-3 pr-3 pt-2 pb-2" 
                    @click="toggle()">
                    <i class="fas fa-bell"></i>
                    <i v-if="notifications.length" class="fas fa-circle text-danger"></i>
                    ({{ notifications.length }})
                </a>
            </div>
            <div class="border-dark bg-white rounded mt-3 overflow-auto notification-box"
                v-if="show_box && notifications.length != 0">
                <div class="border pl-3 pr-3 pt-1 pb-1"
                    v-for="(notification,index) in notifications" :title="notification.created_at">
                    <div class="row">
                        <a class="col text-decoration-none" :href="notification.url">
                            {{ notification.message }}
                        </a>
                        <div class="col-2">
                            <a @click="seen(index)">
                                <i class="far fa-eye text-secondary pointer"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    export default {

        props: {
            channel: {required: true},
            notification_url: {required: true},
        },

        created() 
        {
            axios.get(this.notification_url)
                .then(response => {
                    this.notifications = response.data;
                });

            Echo.private(`notify.${this.channel}`)
                .listen('NotifyEvent', (e) => {
                    this.notifications = e.data.notifications;
                });
        },


        updated() {
            this.$nextTick(() => this.scrollToEnd());
        },

        beforeDestroy () {
            Echo.leave(`notify.${this.channel}`)
        },

        data() {

            return {
                show_box: false,
                notifications: [],
                unread: 0,
            }
        },

        computed: {
            //
        },

        methods: {
            seen(index)
            {
                axios.patch(this.notification_url + '/' + this.notifications[index].id)
                    .then(response => {
                        if (response.data) {
                            this.notifications.splice(index, 1);
                        }
                    });
            },

            scrollToEnd ()
            {
                //
            },

            toggle()
            {
                this.show_box = !this.show_box;
            },
        }

    }
</script>
