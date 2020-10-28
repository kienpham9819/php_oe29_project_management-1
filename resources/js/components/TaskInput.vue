<template>
    <div>
        <table class="table table-hover table-bordered">
            <tbody>
                <slot>
                </slot>
                <tr v-for="(task,index) in tasks">
                    <td>
                        <div class="row">
                            <div class="col-md-10">
                                <input v-if="task.status != null" type='checkbox' disabled>
                                <input v-else type='checkbox' :checked="task.is_completed" @click="toggle(index)">
                                <a data-toggle="modal" :data-target="'#info' + task.id">
                                    {{ task.name }}
                                </a>
                                <span v-if="task.status != null" class="text-secondary">
                                    <i>
                                        ({{ task.status }})
                                    </i>
                                </span>
                            </div>
                            <div class="col-md" v-if="can_delete">
                                <a class="btn btn-sm btn-danger" @click="deleteTask(index)">
                                    <i class="far fa-trash-alt"></i>
                                </a>
                            </div>
                            <div class="col-md" data-toggle="modal" data-target="#attachment" @click="setAttatchmentTask(index)">
                                <a class="btn btn-sm btn-secondary">
                                    <i class="fas fa-paperclip"></i>
                                </a>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr v-if="can_create">
                    <td>
                        <div class="container text-center">
                            <div>
                            </div>
                            <div class="row">
                                <input type="text" class="form-control col-md-11 mr-2"
                                    :class="{ 'is-invalid' : error }"
                                    v-model="task_name"
                                    @keyup.13="addTask()">
                                <a class="btn btn-primary col-md" @click="addTask()">
                                    <i class="far fa-calendar-plus"></i>
                                </a>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr v-if="can_create">
                    <td>
                        <form method="post" :action="path">
                            <input type="hidden" name="_token" :value="this.token">
                            <div v-for="(task, index) in tasks">
                                <input v-if="task.status != null"
                                    type="hidden"
                                    :name="'tasks[' + index + '][name]'"
                                    :value="task.name">
                                <input v-if="task.status != null"
                                    type="hidden"
                                    :name="'tasks[' + index + '][task_list_id]'"
                                    :value="task_list_id">
                                <input v-if="task.status != null"
                                    type="hidden"
                                    :name="'tasks[' + index + '][is_completed]'"
                                    value="0">
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm">
                                {{ trans('general.save') }}
                            </button>
                        </form>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="modal fade" id="attachment" tabindex="-1" role="dialog" aria-labelledby="attachment" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Add attachment on "{{ attachment_task.name }}"</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="post"
                            :action="att_path"
                            enctype="multipart/form-data">
                            <input type="hidden" name="_token" :value="this.token">
                            <input type="hidden" name="task_id" :value="this.attachment_task.id">
                            <input type="file"
                                name="urls[]"
                                class='form-control' multiple>
                            <button type="submit" class="btn btn-primary mt-1">
                                {{ trans('general.save') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    export default {

        mounted() {
            axios.get(this.render)
                .then(response => {
                    this.tasks = response.data;
                })
            this.translations = JSON.parse(this.translation);
        },

        props: {
            translation: {required: true},
            render: {required : true},
            path: {required : true},
            token: {required : true},
            task_list_id: {required : true},
            att_path: {required : true},
            can_create: {default : false},
            can_delete: {default : false},
        },

        data() {
            return {
                translations : {},
                tasks : [],
                task_name : '',
                error : false,
                attachment_task : {
                    id : 0,
                    name : '',
                },
            }
        },

        computed : {
        },

        methods: {

            trans(key, replace = {}) {
                let translation = key.split('.').reduce((t, i) => t[i] || null, this.translations);

                for (var placeholder in replace) {
                    translation = translation.replace(`:${placeholder}`, replace[placeholder]);
                }

                return translation;
            },

            addTask()
            {
                if (this.task_name == '') {
                    this.error = true;
                } else {
                    this.error = false;
                    this.tasks.push({
                        'id': 0,
                        'name' : this.task_name,
                        'is_completed' : false,
                        'status': 'Not saved',
                    });
                    this.task_name = '';
                }
            },

            deleteTask(index)
            {
                if (this.tasks[index].status == null) {
                    if (confirm("Are you sure you want to delete?")) {
                        axios.delete('/tasks/' + this.tasks[index].id)
                            .then(response => {
                                this.tasks.splice(index, 1);
                            });
                    }
                } else {
                    this.tasks.splice(index, 1);
                }
            },

            toggle(index)
            {
                if (this.tasks[index].status == null) {
                    axios.patch('/tasks/' + this.tasks[index].id + '/toggle')
                        .then(response => {
                            console.log(response.data);
                            this.tasks[index].is_completed = !this.tasks[index].is_completed;
                        });
                }
            },

            setAttatchmentTask(index)
            {
                this.attachment_task = this.tasks[index];
            },
        }

    }
</script>
