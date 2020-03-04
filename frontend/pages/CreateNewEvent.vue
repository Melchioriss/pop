<template>
    <div class="event-form">
        <form
            @submit.prevent="submitForm"
            method="post"
        >
            <h1 class="title" v-if="isNew">Create New Event</h1>
            <h1 class="title" v-else>Event Edit</h1>

            <loading-indicator v-if="isLoading"></loading-indicator>

            <div v-else class="form">

                <div class="form__block">
                    <label for="event-name" class="form__label">Title</label>
                    <input
                        v-model="event.name"
                        type="text"
                        id="event-name"
                        class="input"
                        required
                    />
                </div>

                <div class="form__block">
                    <label for="event-descr" class="form__label">
                        Description
                        <span class="form__note">
                            (<router-link :to="{name: 'text_formatting'}" target="_blank">Markdown</router-link> supported)
                        </span>
                    </label>
                    <textarea
                        v-model="event.description"
                        id="event-descr"
                        class="input input--textarea"
                        rows="6"
                    ></textarea>
                </div>

                <div class="form__block form__block--container">

                    <div class="form__half-block">
                        <label for="event-start-date" class="form__label">Starting Date</label>
                        <input
                            v-model="event.startingOn"
                            type="date"
                            id="event-start-date"
                            class="input"
                            required
                        />
                    </div>

                    <div class="form__half-block">
                        <label for="event-end-date" class="form__label">Ending Date</label>
                        <input
                            v-model="event.endingOn"
                            type="date"
                            id="event-end-date"
                            class="input"
                            required
                        />
                    </div>
                </div>

                <div
                    v-if="isNew"
                    class="form__block"
                >
                    <label for="event-group" class="form__label">Group</label>
                    <select
                        id="event-group"
                        class="input"
                    >
                        <option
                            v-for="group in groups"
                            :value="group.id"
                        >{{group.name}}</option>
                    </select>
                </div>

                <error-box v-if="errors">{{errors}}</error-box>

                <button type="submit" class="button">{{isNew ? 'Create' : 'Update'}}</button>

            </div>

        </form>
    </div>
</template>

<script>
    import uuid from 'uuid';
    import ErrorBox from "../components/ErrorBox";
    import LoadingIndicator from "../components/LoadingIndicator";

    export default {
        name: "CreateNewEvent",
        components: {LoadingIndicator, ErrorBox},
        props: {},
        data() {
            return {
                isLoading: false,
                errors: '',
                event: {}
            };
        },
        computed: {
            eventUuid: function () {
                return this.$route.params.eventUuid;
            },

            isNew: function () {
                return !this.eventUuid;
            },

            groups: function () {
                return this.$store.state.groups;
            }
        },
        methods: {

            submitForm () {
                this.errors = '';

                let actionName = this.eventUuid ? 'updateEvent' : 'createEvent';

                this.$store.dispatch(actionName, this.event)
                    .then(() => {
                        this.$router.push({
                            name: 'event',
                            params: {
                                eventUuid: this.event.uuid
                            }
                        })
                    })
                    .catch(e => this.errors = e);
            }
        },
        created() {

            if (this.eventUuid)
            {
                this.isLoading = true;

                this.$store.dispatch('loadEvent', this.eventUuid)
                    .then(() => {
                        let storeEvent = this.$store.state.events[this.eventUuid];
                        this.event = Object.assign( {
                            startingOn: this.$getHtmlFormat(storeEvent.activePeriod.startDate),
                            endingOn: this.$getHtmlFormat(storeEvent.activePeriod.endDate)
                        }, storeEvent );
                    })
                    .catch(e => this.errors = e)
                    .finally(() => this.isLoading = false);

            }
            else
            {
                this.event = {
                    uuid: uuid.v4(),
                    name: '',
                    description: '',
                    group: this.groups[0] ? this.groups[0].id : '',
                    startingOn: '',
                    endingOn: ''
                };
            }
        }
    }
</script>

<style lang="less">
    @import "../assets/_colors";

</style>
