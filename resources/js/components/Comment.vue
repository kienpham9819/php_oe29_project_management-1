<template>
    <div>
        <div v-if="!this.deleted">
            <div v-if="this.comment.user_id == user_id">
                <slot>
                </slot>
                <br>
                <input 
                    name="comment" 
                    class="form-control mr-2 mb-1"
                    :class="{ 'is-invalid' : this.error }"
                    v-if="edit" 
                    type="text" 
                    v-model="comment.content" 
                    autofocus>
                
                <label v-else 
                    v-text="comment.content" 
                    class="mr-2" 
                    @click="toggle()" 
                    :class="{ 'bg-white border p-2' : this.hover }" 
                    @mouseover="hover = true" 
                    @mouseleave="hover = false" 
                    title="Edit" 
                    data-toggle="tooltip" 
                    data-placement="right">
                </label>

                <a  v-if="edit" class="btn btn-sm btn-primary text-white" @click="toggle()">
                    <i class="fas fa-edit"></i>
                </a>
                <a  class="btn btn-sm btn-danger" @click="deleteComment()">
                    <i class="fas fa-times"></i>
                </a>
            </div>
            <div v-else>
                <slot>
                </slot>
                <br>
                <label
                    v-text="comment.content" 
                    class="mr-2">
                </label>
            </div>
        </div>
    </div>
</template>

<script>
    export default {

        mounted() {
            axios.get(this.render)
                .then(response => {
                    this.comment = response.data;
                    console.log(response.data);
                });
        },

        data: function() {
            return {
                error: false,
                hover: false,
                edit: false,
                comment: '',
                deleted: false,
            }
        },

        methods: {
            deleteComment()
            {
                axios.delete(this.delete_path)
                    .then(response => {
                        if (response.data) {
                            this.deleted = true;
                        }
                    });
            },

            toggle()
            {
                this.hover = false;
                if (this.edit == true) {
                    if (this.comment.content == '' || this.comment.content == null) {
                        this.error = true;
                    }
                    else {
                        axios.patch(this.update_path, {
                            content: this.comment.content,
                        })
                            .then(response => {
                                if (response.data) {
                                    this.error = false;
                                    this.edit = false;
                                } else {
                                    this.error = true;
                                }
                            });
                    }
                }
                else {
                    this.edit = !this.edit;
                }
            },
        },

        props: {
            id : { required : true },
            render : { required : true },
            update_path : { required : true },
            delete_path: { required: true },
            user_id : { required : true },
        }
    }
</script>
