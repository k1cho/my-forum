<template>
    <div :id="'reply-'+id" class="card">
        <div class="card-header">
            <div class="level">
                <h5 class="flex">
                    <a :href="'/profiles/'+data.owner.name" v-text="data.owner.name"></a> said <span v-text="ago"></span>
                </h5>
                    <div v-if="signedIn">
                        <favorite :reply="data"></favorite>
                    </div>
            </div>
        </div>
        <div class="card-body">
            <div v-if="editing">
                <form @submit="update">
                    <div class="form-group">
                        <textarea class="form-control" cols="10" rows="5" v-model="body" required></textarea>
                    </div>
                    <button class="btn btn-sm btn-primary">Update</button>
                    <button class="btn btn-sm btn-link" @click="editing = false" type="button">Cancel</button>
                </form>
            </div>
            <span v-else v-html="body"></span>
        </div>

        <div v-if="canUpdate" class="card-footer level">
            <button class="btn btn-sm mr-1" @click="editing = true">Edit</button>
            <button class="btn btn-sm btn-danger mr-1" @click="destroy">Delete</button>
        </div>

    </div>
</template>

<script>
    import Favorite from './Favorite.vue';
    import moment from 'moment';

    export default {
        props: ['data'],

        components: { Favorite },

        computed: {
            ago() {
                return moment(this.data.created_at).fromNow() + '...';
            },

            signedIn() {
                return window.App.signedIn;
            },

            canUpdate() {
                return this.authorize(user => this.data.user_id == user.id);
                //return this.data.user_id == window.App.auth_user.id;
            }
        },
        
        data() {
            return {
                editing: false,
                id: this.data.id,
                body: this.data.body
            };
        },

        methods: {
            update() {
                axios.patch(
                    '/replies/' + this.data.id, {
                        body: this.body
                    })
                    .catch(error => {
                        flash(error.response.data, 'danger');
                    });

                this.editing = false;

                flash('Updated');
            },

            destroy() 
            {
                axios.delete('/replies/' + this.data.id);

                this.$emit('deleted', this.data.id);
            }
        }
    }
</script>