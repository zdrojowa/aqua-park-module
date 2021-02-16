<template>
    <div>
        <slot></slot>

        <b-nav align="right">
            <b-nav-item>
                <media-selector
                    extensions="jpg,jpeg,png"
                    @media-selected="add"
                ></media-selector>
            </b-nav-item>
            <b-nav-item>
                <b-button type="bytton" variant="primary" @click="save">Zapisz</b-button>
            </b-nav-item>
        </b-nav>

        <div class="row item-conteiner">
            <draggable class="list-group" ghost-class="ghost" :list="map" handle=".handle">
                <div class="list-group-item" v-for="(element, index) in map" :key="index + element.url">
                    <div class="item gallery-item">
                        <b-icon-arrows-move class="handle"></b-icon-arrows-move>
                        <div class="thumbnail-img">
                            <media-selector
                                extensions="jpg,jpeg,png"
                                @media-selected="change(index, $event)"
                            ></media-selector>
                            <img :src="element.url" class="img-thumbnail">
                        </div>
                        <div class="thumbnail-img">
                            <media-selector
                                extensions="jpg,jpeg,png"
                                @media-selected="changeDescriptionUrl(index, $event)"
                            ></media-selector>
                            <img v-if="element.description_url" :src="element.description_url" class="img-thumbnail">
                            <b-btn v-if="element.description_url" variant="danger" @click="removeDescriptionUrl(index)">
                                Usuń
                            </b-btn>
                        </div>
                        <div class="thumbnail-img">
                            <media-selector
                                extensions="jpg,jpeg,png"
                                @media-selected="changeMoileUrl(index, $event)"
                            ></media-selector>
                            <img v-if="element.mobile_url" :src="element.mobile_url" class="img-thumbnail">
                            <b-btn v-if="element.mobile_url" variant="danger" @click="removeMobileUrl(index)">
                                Usuń
                            </b-btn>
                        </div>
                        <div class="gallery-form px-3">
                            <b-input-group prepend="Tytuł" class="mt-3">
                                <b-form-input v-model.lazy="element.title"></b-form-input>
                            </b-input-group>
                            <b-input-group prepend="Opis" class="mt-3">
                                <b-form-textarea v-model.lazy="element.description" rows="3"></b-form-textarea>
                            </b-input-group>
                            <b-form-group label="Link" class="mt-3">
                                <div class="row">
                                    <b-input-group prepend="Text" class="mt-3 col-sm-6">
                                        <b-form-input v-model.lazy="element.link.text"></b-form-input>
                                    </b-input-group>
                                    <b-input-group prepend="Url" class="mt-3 col-sm-6">
                                        <b-form-input v-model.lazy="element.link.url"></b-form-input>
                                    </b-input-group>
                                </div>
                            </b-form-group>
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
        name: 'map-gallery',
        props: {
            id: {
                required: true,
                type: String
            }
        },

        data() {
            return {
                element: {id: '', url: '', description_url: '', title: '', description: '', link: {text: '', url: ''}},
                map: []
            };
        },

        created() {
            this.getMap()
        },

        methods: {

            getMap() {
                axios.get('/api/aqua-parks?id=' + this.id)
                .then(res => {
                    if (typeof res.data.map == 'undefined') {
                        this.map = []
                    } else {
                        this.map = res.data.map
                    }
                }).catch(err => {
                    console.log(err)
                })
            },

            remove(index) {
                this.map.splice(index, 1)
            },

            removeDescriptionUrl(index) {
                this.map[index].description_url = ''
            },

            removeMobileUrl(index) {
                this.map[index].mobile_url = ''
            },

            add(args) {
                this.map.push({
                    id: args._id,
                    url: args.url,
                    description_url: '',
                    mobile_url: '',
                    title: args.title,
                    description: args.alt,
                    link: {text: '', url: ''}
                })
            },

            change(index, $event) {
                this.map[index].url = $event.url
                this.map[index].id = $event._id
            },

            changeDescriptionUrl(index, $event) {
                this.map[index].description_url = $event.url
            },

            changeMoileUrl(index, $event) {
                this.map[index].mobile_url = $event.url
            },

            save() {
                let formData = new FormData()
                formData.append('_method', 'PUT')
                formData.append('map', JSON.stringify(this.map))

                axios.post('/dashboard/aqua-parks/' + this.id, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                }).then(res => {
                    this.$bvToast.toast('Mapa zaktualizowana', {
                        title: `Mapa`,
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
