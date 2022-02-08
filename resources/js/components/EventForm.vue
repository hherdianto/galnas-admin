<template>
    <form method="POST" action="{{ route('events.store') }}">
    <div class="form-group row">
        <label>Nama</label>
        <md-input class="form-control" type="text" placeholder="{{ __('Nama') }}" name="event_name" required autofocus></md-input>
    </div>

    <div class="form-group row">
        <label>Tipe</label>
        <select class="form-control" name="event_type_id">
            @foreach($eventTypes as $eventType)
            <option value="{{ $eventType->id }}">{{ $eventType->type }}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group row">
        <label>Lokasi</label>
        <select class="form-control" name="event_type_id">
            @foreach($locations as /** @var \App\Models\Location $location */$location)
            <option value="{{ $location->id }}">{{ $location->location_name }}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group row">
        <label>Tgl Mulai</label>
        <input type="date" class="form-control" name="date_start" required/>
    </div>

    <div class="form-group row">
        <label>Tgl Berakhir</label>
        <input type="date" class="form-control" name="date_end" required/>
    </div>

    <div class="form-group row">
        <label>Open Booking</label>
        <input type="date" class="form-control" name="open_booking_at" required/>
    </div>

    <div class="form-group row">
        <label>URL</label>
        <input type="url" class="form-control" name="url"/>
    </div>

    <div class="form-group row">
        <label>Deskripsi</label>
        <textarea class="form-control" id="textarea-input" name="notes" rows="9"
                  placeholder="{{ __('Deskripsi..') }}"></textarea>
    </div>

    <button class="btn btn-block btn-success" type="button">{{ __('Add') }}</button>
    <a href="{{ route('events') }}" class="btn btn-block btn-primary">{{ __('Return') }}</a>
</form>
    <div class="Form">
        <form novalidate @submit.prevent="validateReservation">
            <div class="md-layout mt-2 mx-2 md-gutter">
                <div class="md-layout-item md-size-100 my-2">
                    <md-card class="md-elevation-12">
                        <md-card-content>
                            <div class="md-layout md-gutter">
                                <div class="md-layout-item md-small-size-100">
                                    <md-field>
                                        <label for="time_slot">Schedule</label>
                                        <md-select name="time_slot" id="time_slot" v-model="form.scheduleId" md-dense
                                                   :disabled="sending">
                                            <md-option v-for="schedule in schedules" :value="schedule.id"
                                                       :key="schedule.id">
                                                {{ `${schedule.name}, ${schedule.time_slot}, ${schedule.slot} available`
                                                }}
                                            </md-option>
                                        </md-select>
                                    </md-field>
                                </div>
                            </div>
                        </md-card-content>
                    </md-card>
                </div>

                <div class="md-layout-item md-size-100 my-2">
                    <md-card class="md-elevation-12">
                        <md-card-header>
                            <div class="md-title">Profile</div>
                        </md-card-header>

                        <md-card-content>
                            <div class="md-layout md-gutter">
                                <div class="md-layout-item md-size-100">
                                    <md-field :class="getValidationClass('full_name')">
                                        <label for="name">Name</label>
                                        <md-input name="name" id="name" autocomplete="given-name" required
                                                  v-model="form.full_name" :disabled="sending"/>
                                        <span class="md-error"
                                              v-if="!$v.form.full_name.required">Name is required</span>
                                        <span class="md-error"
                                              v-else-if="!$v.form.full_name.minLength">Invalid name</span>
                                    </md-field>
                                </div>

                                <div class="md-layout-item md-size-100">
                                    <md-field :class="getValidationClass('email')">
                                        <label for="email">Email</label>
                                        <md-input type="email" name="email" id="email" autocomplete="email" required
                                                  v-model="form.email" :disabled="sending"/>
                                        <span class="md-error" v-if="!$v.form.email.required">Email is required</span>
                                        <span class="md-error" v-else-if="!$v.form.email.email">Invalid email</span>
                                    </md-field>
                                </div>

                                <div class="md-layout-item md-size-100">
                                    <md-field :class="getValidationClass('phone')">
                                        <label for="phone">Phone</label>
                                        <md-input type="tel" name="phone" id="phone" autocomplete="given-phone" required
                                                  v-model="form.phone" :disabled="sending"/>
                                        <span class="md-error" v-if="!$v.form.phone.required">Phone is required</span>
                                        <span class="md-error"
                                              v-else-if="!$v.form.phone.minLength">Invalid phone number</span>
                                    </md-field>
                                </div>

                                <div class="md-layout-item md-size-100">
                                    <md-field :class="getValidationClass('gender')">
                                        <md-radio v-model="form.gender" :value="1" :disabled="sending">Male</md-radio>
                                        <md-radio v-model="form.gender" :value="2" :disabled="sending">Female</md-radio>
                                        <span class="md-error" v-if="!$v.form.gender.required">Gender is required</span>
                                    </md-field>
                                </div>

                                <div class="md-layout-item md-size-100">
                                    <md-field>
                                        <md-radio v-model="form.indonesian" :value="1" :disabled="sending">WNI
                                        </md-radio>
                                        <md-radio v-model="form.indonesian" :value="2" :disabled="sending">WNA
                                        </md-radio>
                                    </md-field>
                                </div>

                                <div class="md-layout-item md-size-100">
                                    <md-field :class="getValidationClass('age')">
                                        <label for="age">Age</label>
                                        <md-select name="age" id="age" v-model="form.age" md-dense required
                                                   :disabled="sending">
                                            <md-option value="13-17">13-17</md-option>
                                            <md-option value="18-24">18-24</md-option>
                                            <md-option value="25-34">25-34</md-option>
                                            <md-option value="35-44">35-44</md-option>
                                            <md-option value="45-54">45-54</md-option>
                                            <md-option value="55-64">55-64</md-option>
                                            <md-option value="65+">65+</md-option>
                                        </md-select>
                                        <span class="md-error" v-if="!$v.form.age.required">Age is required</span>
                                    </md-field>
                                </div>
                            </div>
                        </md-card-content>

                        <md-card-content>
                            <div class="md-subhead" v-if="form.members.length">
                                Group Members {{ form.members.length }} / {{ maxGroupSize - 1 }}
                            </div>

                            <div class="md-layout-item md-size-100"
                                 v-for="(member, index) in form.members" :key="index">
                                <div class="outer">
                                    <md-field>
                                        <label :for="`memberName${index}`">Name</label>
                                        <md-input :name="`memberName${index}`" :id="`memberName${index}`"
                                                  :autocomplete="`memberName${index}`"
                                                  v-model="form.members[index].name" :disabled="sending" required/>
                                    </md-field>
                                    <md-button class="md-icon-button" @click="form.members.splice(index, 1)">
                                        <md-icon>close</md-icon>
                                    </md-button>
                                </div>
                            </div>

                            <div class="md-layout-item md-size-100"
                                 v-if="(!form.members.length || form.members.slice(-1)[0].name.length > 3) && form.members.length < maxGroupSize - 1">
                                <md-button class="md-fab md-mini md-primary"
                                           @click="form.members.push({name: ''})">
                                    <md-icon>add</md-icon>
                                </md-button>
                            </div>
                        </md-card-content>

                        <md-progress-bar md-mode="indeterminate" v-if="sending"/>

                        <md-card-actions>
                            <md-button type="submit" class="md-primary md-raised" :disabled="sending">Reserve</md-button>
                        </md-card-actions>
                    </md-card>
                </div>

                <md-snackbar md-position="center" :md-duration=5000 :md-active.sync="showSnackbar" md-persistent>
                    <span>{{ snackBarMessage }}</span>
                    <md-button class="md-primary" @click="showSnackbar = false; snackBarMessage = null">OK</md-button>
                </md-snackbar>

                <div class="md-layout-item md-size-100 my-2" v-if="errorResponse">
                    <md-card class="md-elevation-12">
                        <md-card-header>
                            <md-card-header-text>Error</md-card-header-text>
                        </md-card-header>

                        <md-card-content>
                            {{ errorResponse }}
                        </md-card-content>

                        <md-card-actions>
                            <md-button class="md-primary" @click="errorResponse = null">OK</md-button>
                        </md-card-actions>
                    </md-card>
                </div>
            </div>
        </form>
        <md-dialog-confirm
            :md-active.sync="showDialogSuccess"
            :md-content="`Reservation Success! Your reservation code is ${this.code}`"
            :md-close-on-esc="false"
            :md-click-outside-to-close="false"
            md-cancel-text=""
            md-confirm-text="OK"
            @md-confirm="onConfirm"/>
    </div>
</template>

<script>
    import axios from 'axios'
    import {validationMixin} from 'vuelidate'
    import {email, minLength, required} from 'vuelidate/lib/validators'
    // import {isPhone} from "../../validators";
    // import router from "../../router";

    export default {
        name: 'VisitorForm',
        mixins: [validationMixin],
        data: () => ({
            maxGroupSize: 10,
            event: null,
            selectedDate: null,
            schedules: null,
            schedule: null,
            form: {
                scheduleId: null,
                email: null,
                full_name: null,
                phone: null,
                gender: 1,
                age: null,
                indonesian: 1,
                members: []
            },
            userSaved: false,
            sending: false,
            lastUser: null,
            showSnackbar: false,
            snackBarMessage: null,
            errorResponse: null,
            showDialogSuccess: false,
            code: null,
        }),
        validations: {
            form: {
                email: {
                    required,
                    email
                },
                full_name: {
                    required,
                    minLength: minLength(3)
                },
                phone: {
                    required,
                    minLength: minLength(6),
                    // isPhone
                },
                gender: {
                    required
                },
                age: {
                    required
                },
                scheduleId: {
                    required
                }
            }
        },
        methods: {
            getValidationClass(fieldName) {
                const field = this.$v.form[fieldName]

                if (field) {
                    return {
                        'md-invalid': field.$invalid && field.$dirty
                    }
                }
            },
            clearForm() {
                this.$v.$reset()
                this.form.email = null
                this.form.full_name = null
                this.form.phone = null
                this.form.gender = 1
                this.form.age = null
                this.form.indonesian = 1

                this.code = null
                this.errorResponse = null
            },
            saveReservation() {
                this.sending = true

                axios.post('/api/reservation', {
                    ...this.form
                })
                    .then(response => {
                        this.code = response.data.code
                        this.showDialogSuccess = true

                        this.lastUser = `${this.form.full_name}`
                        this.userSaved = true
                        // this.clearForm()
                    })
                    .catch(e => {
                        this.errorResponse = e.response.data.message
                        this.snackBarMessage = e.message
                        this.showSnackbar = true
                    })
                    .finally(() => {
                        this.sending = false
                    })
            },
            validateReservation() {
                this.$v.$touch()

                if (!this.$v.$invalid) {
                    this.saveReservation()
                }
            },
            onConfirm() {
                // router.push('/calendar')
            },
            // removeMember(index) {
            //     for (var i = 0; i < this.form.members.length; i++)
            //         if (i === index)
            // },
        },
        props: {},
        mounted() {
            axios.get('/api/schedule/' + this.$route.query.schedule_id)
                .then(response => {
                    this.event = response.data.event
                    this.schedule = response.data.schedule
                    this.schedules = response.data.schedules
                    this.form.scheduleId = response.data.schedule.id
                })
        },
        computed: {},
        watch: {},
    };
</script>

<style lang="scss" scoped>
    .outer {
        display: flex;
        align-items: baseline;
    }

    .full {
        flex: 1;
    }

    .md-card {
        background-color: #fff;
        color: revert;
    }
</style>
