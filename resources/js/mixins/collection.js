export default {
    methods: {
        /*data() {
            return {
                items: []
            }
        },*/

        add(reply) {
            this.items.push(reply);

            this.$emit('added');
        },

        remove(index) {
            this.items.splice(index, 1);

            this.$emit('removed');
        }
    }
}