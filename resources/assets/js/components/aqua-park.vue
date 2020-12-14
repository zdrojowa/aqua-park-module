<template>
    <div>
        <slot></slot>

        <b-nav align="right">
            <b-nav-item>
                <b-button type="button" variant="primary" @click="save">Zapisz</b-button>
            </b-nav-item>
        </b-nav>

        <div class="row">

            <div class="col-md-6">
                <div class="form-group">
                    <label>Nazwa</label>
                    <b-form-input type="text" v-model.lazy="name" :state="name.length > 0"></b-form-input>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label>Miasto</label>
                    <multiselect v-model.lazy="city" :options="cities" track-by="_id" label="name" placeholder="Wybierz miasto"></multiselect>
                </div>
            </div>
        </div>

        <div class="row">

            <div class="col-md-4">
                <div class="form-group">
                    <b-form-group label="Logo">
                        <media-selector extensions="svg" @media-selected="selectLogo"></media-selector>
                    </b-form-group>

                    <b-img v-if="logo" thumbnail fluid :src="logo"></b-img>

                    <b-button v-if="logo" type="button" variant="danger" @click="removeLogo">Usuń</b-button>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <b-form-group label="Zdjęcie obiektu">
                        <media-selector extensions="png,jpg,jpeg" @media-selected="selectPhoto"></media-selector>
                    </b-form-group>

                    <b-img v-if="photo" thumbnail fluid :src="photo"></b-img>

                    <b-button v-if="photo" type="button" variant="danger" @click="removePhoto">Usuń</b-button>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <b-form-group label="Marker">
                        <media-selector extensions="png" @media-selected="selectMarker"></media-selector>
                    </b-form-group>

                    <b-img v-if="marker" thumbnail fluid :src="marker"></b-img>

                    <b-button v-if="marker" type="button" variant="danger" @click="removeMarker">Usuń</b-button>
                </div>
            </div>
        </div>

        <div class="row">

            <div class="col-md-6">
                <div class="form-group">
                    <label>E-mail</label>
                    <b-form-input type="email" v-model.lazy="mail"></b-form-input>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label>Telefon</label>
                    <b-form-input type="text" v-model.lazy="phone"></b-form-input>
                </div>
            </div>
        </div>

        <div class="row">

            <div class="col-md-12">
                <div class="form-group">
                    <label>Adres</label>
                    <b-form-input type="text" v-model.lazy="address"></b-form-input>
                </div>
            </div>
        </div>

        <div class="row">

            <div class="col-md-6">
                <div class="form-group">
                    <label>Latitude</label>
                    <b-form-input type="number" v-model.lazy="coordinates.latitude"></b-form-input>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label>Longitude</label>
                    <b-form-input type="number" v-model.lazy="coordinates.longitude"></b-form-input>
                </div>
            </div>
        </div>

        <div class="row">

            <div class="col-md-6">
                <div class="form-group">
                    <label>Facebook</label>
                    <input type="text" class="form-control" name="facebook" placeholder="Wpisz link do facebook" v-model.lazy="facebook">
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label>Instagram</label>
                    <input type="text" class="form-control" name="instagram" placeholder="Wpisz link do Instagram" v-model.lazy="instagram">
                </div>
            </div>
        </div>

        <div v-for="lang in langs" class="row mt-3">
            <b-input-group :prepend="'Dni otwarcia (' + lang.name + ')'" class="col-md-12">
                <b-form-input v-model.lazy="work_days[lang.key]"></b-form-input>
            </b-input-group>
        </div>
        <div class="row mt-3">
            <b-input-group prepend="Godziny otwarcia" class="col-sm-12">
                <b-form-input v-model.lazy="work_hours"></b-form-input>
            </b-input-group>
        </div>

        <div v-for="lang in langs">
            <div class="row mt-3">
                <b-input-group :prepend="'Sezon niski (' + lang.name + ')'" class="col-md-12">
                    <b-form-input v-model.lazy="season_low[lang.key]"></b-form-input>
                </b-input-group>
            </div>
            <div class="row mt-3">
                <b-input-group :prepend="'Sezon wysoki (' + lang.name + ')'" class="col-md-12">
                    <b-form-input v-model.lazy="season_high[lang.key]"></b-form-input>
                </b-input-group>
            </div>
        </div>
    </div>
</template>

<script>

    export default {
        name: 'aqua-park',
        props : ['_id'],

        data() {
            return {
                cities: [],
                langs: [],
                id: 0,
                name: '',
                city: {},
                logo: '',
                photo: '',
                marker: '',
                phone: '',
                mail: '',
                address: '',
                coordinates: {latitude: '', longitude: ''},
                facebook: '',
                instagram: '',
                work_days: {},
                work_hours: '',
                season_low: {},
                season_high: {}
            };
        },

        created() {
            this.getCities();
        },

        computed: {

            url() {
                return this.id ? ('/dashboard/aqua-parks/' + this.id) : '/dashboard/aqua-parks/store'
            }
        },

        methods: {

            selectLogo: function(url) {
                this.logo = url
            },

            removeLogo: function() {
                this.logo = ''
            },

            selectPhoto: function(url) {
                this.photo = url
            },

            removePhoto: function() {
                this.photo = ''
            },

            selectMarker: function(url) {
                this.marker = url
            },

            removeMarker: function() {
                this.marker = ''
            },

            getCities: function() {
                axios.get('/api/cities')
                .then(res => {
                    this.cities = res.data
                    this.getLangs()
                }).catch(err => {
                    console.log(err)
                })
            },

            getLangs: function() {
                axios.get('/dashboard/languages/get')
                    .then(res => {
                        this.langs = res.data
                        this.getAquaPark()
                    }).catch(err => {
                    console.log(err)
                })
            },

            getItem: function(arr, key, val) {

                let item = val

                arr.forEach(it => {
                    if (it[key] === val) {
                        item = it
                    }
                })

                return item
            },

            getAquaPark: function() {
                if (this._id) {
                    axios.get('/api/aqua-parks?id=' + this._id)
                    .then(res => {
                        this.id          = res.data._id
                        this.name        = res.data.name
                        this.logo        = res.data.logo
                        this.photo       = res.data.photo
                        this.marker      = res.data.marker
                        this.phone       = res.data.phone
                        this.mail        = res.data.mail
                        this.address     = res.data.address
                        this.coordinates = res.data.coordinates
                        this.facebook    = res.data.facebook
                        this.instagram   = res.data.instagram

                        this.city = this.getItem(this.cities, '_id', res.data.city)

                        this.work_hours = res.data.work_hours
                        if (res.data.work_days != null) {
                            this.work_days = res.data.work_days
                        }
                        if (res.data.season_low != null) {
                            this.season_low = res.data.season_low
                        }
                        if (res.data.season_high != null) {
                            this.season_high = res.data.season_high
                        }
                        this.checkLangs()

                    }).catch(err => {
                        console.log(err)
                        this.checkLangs()
                    })
                }
            },

            checkLangs: function() {
                this.langs.forEach(lang => {
                    if (!(lang.key in this.work_days)) {
                        this.work_days[lang.key] = ''
                    }
                    if (!(lang.key in this.season_low)) {
                        this.season_low[lang.key] = ''
                    }
                    if (!(lang.key in this.season_high)) {
                        this.season_high[lang.key] = ''
                    }
                })
            },

            validate: function(e) {
                return !!this.name

            },

            save: function(e) {
                e.preventDefault()

                if (this.validate) {
                    let formData = new FormData()
                    formData.append('_method', this.id ? 'PUT' : 'POST')
                    formData.append('name', this.name)
                    formData.append('city', this.city._id)
                    formData.append('logo', this.logo)
                    formData.append('photo', this.photo)
                    formData.append('marker', this.marker)
                    formData.append('phone', this.phone)
                    formData.append('mail', this.mail)
                    formData.append('address', this.address)
                    formData.append('coordinates[latitude]', this.coordinates.latitude)
                    formData.append('coordinates[longitude]', this.coordinates.longitude)
                    formData.append('facebook', this.facebook)
                    formData.append('instagram', this.instagram)
                    formData.append('work_days', JSON.stringify(this.work_days))
                    formData.append('work_hours', this.work_hours)
                    formData.append('season_low', JSON.stringify(this.season_low))
                    formData.append('season_high', JSON.stringify(this.season_high))

                    axios.post(this.url, formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    })
                    .then(res => {
                        window.location = res.data.redirect
                    }).catch(err => {
                        console.log(err)
                    });
                } else {
                    return false
                }
            }
        },

        watch: {
            name() {
                this.validate()
            }
        }
    }
</script>
