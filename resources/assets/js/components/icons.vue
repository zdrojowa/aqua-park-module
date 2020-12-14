<template>
    <div>
        <slot></slot>

        <b-nav align="right">
            <b-nav-item>
                <media-selector
                    extensions="svg"
                    @media-selected="add"
                ></media-selector>
            </b-nav-item>
            <b-nav-item>
                <b-button type="bytton" variant="primary" @click="save">Zapisz</b-button>
            </b-nav-item>
        </b-nav>

        <div class="row item-conteiner">
            <draggable class="list-group" ghost-class="ghost" :list="icons" handle=".handle">
                <div class="list-group-item" v-for="(url, index) in icons" :key="index">
                    <div class="item gallery-item">
                        <b-icon-arrows-move class="handle"></b-icon-arrows-move>
                        <div class="thumbnail-img">
                            <media-selector
                                extensions="svg"
                                @media-selected="change(index, $event)"
                            ></media-selector>
                            <img :src="url" class="img-thumbnail">
                        </div>
                        <div>
                            <button type="button" aria-label="Close" class="close" @click="remove(index)">×</button>
                        </div>
                    </div>
                </div>
            </draggable>
        </div>
    </div>
</template>

<script>

    export default {
        name: 'icons',
        props: {
            id: {
                required: true,
                type: String
            }
        },

        data() {
            return {
                icons: []
            };
        },

        created() {
            this.getIcons()
        },

        methods: {

            getIcons() {
                axios.get('/api/aqua-parks-prices?id=' + this.id)
                .then(res => {
                    if (typeof res.data.icons == 'undefined') {
                        this.icons = []
                    } else {
                        this.icons = res.data.icons
                    }
                }).catch(err => {
                    console.log(err)
                })
            },

            remove(index) {
                this.icons.splice(index, 1)
            },

            add(args) {
                this.icons.push(args.url)
            },

            change(index, $event) {
                this.icons[index] = $event.url
            },

            save() {
                let formData = new FormData()
                formData.append('_method', 'PUT')
                formData.append('icons', JSON.stringify(this.icons))

                axios.post('/dashboard/aqua-parks/prices/' + this.id, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                }).then(res => {
                    this.$bvToast.toast('Ikonki zaktualizowane', {
                        title: `Ikonki`,
                        variant: 'success',
                        solid: true
                    })
                }).catch(err => {
                    this.$bvToast.toast(err, {
                        title: `Błąd`,
                        variant: 'danger',
                        solid: true
                    })
                })
            }
        }
    }
</script>
