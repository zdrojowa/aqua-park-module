<template>
    <div>
        <slot></slot>

        <b-nav align="right">
            <b-nav-item>
                <b-button type="button" variant="primary" @click="save">Zapisz</b-button>
            </b-nav-item>
        </b-nav>

        <nested v-model="list"/>
    </div>
</template>

<script>

    export default {
        name: 'list',
        props: {
            name: {
                type: String,
                required: true
            },
            aquaPark: {
                type: String,
                required: false
            },
            price: {
                type: String,
                required: false
            },
        },

        data() {
            return {
                list: []
            };
        },

        created() {
            this.getList();
        },

        methods: {

            getList() {
                let url = '/api/' + this.name
                if (this.aquaPark) {
                    url += '?aquaPark=' + this.aquaPark
                }
                if (this.price) {
                    url += '?price=' + this.price
                }
                axios.get(url)
                .then(res => {
                    if (typeof res.data == 'undefined') {
                        this.list = [];
                    } else {
                        this.list = res.data;
                    }
                }).catch(err => {
                    console.log(err)
                })
            },

            save: function() {
                let formData = new FormData();
                formData.append('_method', 'POST');
                formData.append('list', JSON.stringify(this.list));

                axios.post('/dashboard/' + this.name + '/saveOrder', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                }).then(res => {
                    this.$bvToast.toast('Sortowanie zaktualizowane', {
                        title: `Sortowanie`,
                        variant: 'success',
                        solid: true
                    })
                }).catch(err => {
                    this.$bvToast.toast(err, {
                        title: `Błąd`,
                        variant: 'danger',
                        solid: true
                    })
                });
            }
        }
    }
</script>
