<template>
    <div>
        <div v-if="signedIn">
            <div class="form-group">
                <textarea name="body" 
                            id="body" 
                            cols="30" 
                            rows="5" 
                            placeholder="Have something to say?" 
                            class="form-control" 
                            v-model="body" 
                            required></textarea>
            </div>
            <div class="form-group">
                <button class="btn btn-default" @click="addReply">Post</button>
            </div>
        </div>
        <p class="text-center" v-else>Please <a href="/login">sign in</a> to participate in this discussion</p>
    </div>
</template>
<script>
    import 'jquery.caret';
    import 'at.js';

    export default {
        data() {
            return {
                body: ''
            }
        },

        computed: {
            signedIn() {
                return window.App.signedIn;
            }
        },

        mounted() {
            $('#body').atwho({
                at: '@',
                delay: 750,
                callbacks: {
                    remoteFilter: function(query, callback) {
                        $.getJSON("/api/users", {name: query}, function(usernames) {
                            callback(usernames)
                        });
                    }
                }
            });
        },

        methods: {
            addReply() {
                axios.post(location.pathname + '/replies', {body: this.body})
                    .catch(error => {
                        flash(error.response.data, 'danger');
                    })
                    .then(response => {
                        this.body = '';

                        flash('Your reply has been posted.');

                        this.$emit('created', response.data);
                    });
            }
        }

    }
</script>