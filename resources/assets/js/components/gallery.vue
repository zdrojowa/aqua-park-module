<template>
    <div>
        <slot></slot>

        <b-nav align="right">
            <b-nav-item>
                <media-selector
                    extensions="jpg,jpeg,png,3gp,3g2,asf,wmv,avi,divx,evo,f4v,flv,mp4,mpg,mpeg"
                    @media-selected="add"
                ></media-selector>
            </b-nav-item>
            <b-nav-item>
                <b-button type="bytton" variant="primary" @click="save">Zapisz</b-button>
            </b-nav-item>
        </b-nav>

        <div class="row item-conteiner">
            <draggable class="list-group" ghost-class="ghost" :list="gallery" handle=".handle">
                <div class="list-group-item" v-for="(element, index) in gallery" :key="index + element.url">
                    <div class="item gallery-item">
                        <b-icon-arrows-move class="handle"></b-icon-arrows-move>
                        <div class="thumbnail-img">
                            <media-selector
                                extensions="jpg,jpeg,png,3gp,3g2,asf,wmv,avi,divx,evo,f4v,flv,mp4,mpg,mpeg"
                                @media-selected="change(index, $event)"
                            ></media-selector>
                            <img v-if="element.is_image" :src="element.url" class="img-thumbnail">
                            <a v-else href="element.url" target="_blank">
                                <i class="mdi mdi-file-video-outline"></i>
                            </a>
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
        name: 'gallery',
        props: {
            id: {
                required: true,
                type: String
            }
        },

        data() {
            return {
                element: {id: '', url: '', title: '', description: '', is_image: false, link: {text: '', url: ''}},
                gallery: []
            };
        },

        created() {
            this.getGallery()
        },

        methods: {

            getGallery() {
                axios.get('/api/aqua-parks?id=' + this.id)
                .then(res => {
                    if (typeof res.data.gallery == 'undefined') {
                        this.gallery = []
                    } else {
                        this.gallery = res.data.gallery
                    }
                }).catch(err => {
                    console.log(err)
                })
            },

            remove(index) {
                this.gallery.splice(index, 1)
            },

            add(args) {
                axios.get('/api/media/' + args._id)
                .then(res => {
                    this.gallery.push({
                        id: args._id,
                        url: args.url,
                        title: args.title,
                        description: args.alt,
                        is_image: ['jpg', 'jpeg', 'png'].includes(res.data.extension),
                        link: {text: '', url: ''}
                    })
                }).catch(err => {
                    console.log(err)
                })
            },

            change(index, $event) {
                axios.get('/api/media/' + $event._id)
                .then(res => {
                    this.gallery[index].url = $event.url
                    this.gallery[index].id = $event._id
                    this.gallery[index].is_image = ['jpg', 'jpeg', 'png'].includes(res.data.extension)
                }).catch(err => {
                    console.log(err)
                })
            },

            save() {
                let formData = new FormData()
                formData.append('_method', 'PUT')
                formData.append('gallery', JSON.stringify(this.gallery))

                axios.post('/dashboard/aqua-parks/' + this.id, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                }).then(res => {
                    this.$bvToast.toast('Galeria zaktualizowana', {
                        title: `Galeria`,
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
