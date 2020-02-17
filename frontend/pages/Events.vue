<template>
    <div class="events-list">

        <h1 class="title">List of All Events</h1>

        <router-link
            v-if="loggedUserIsAdmin"
            :to="{name: 'create_event'}"
            class="button events-list__button"
        >Create New Event</router-link>

        <loading-indicator v-if="isLoading" />
        <template v-else>
            <event-item
                v-for="event in events"
                :key="'event_'+event.uuid"
                :event="event"
            />
        </template>


    </div>
</template>

<script>
    import {mapGetters, mapState} from 'vuex';
    import EventItem from "../components/EventItem";
    import LoadingIndicator from "../components/LoadingIndicator";
    export default {
        name: "Events",
        components: {LoadingIndicator, EventItem},
        data: function () {
            return {
                isLoading: false
            };
        },
        computed: {
            ...mapGetters([
                'loggedUserIsAdmin'
            ]),

            ...mapState([
                'events'
            ])
        },
        methods: {

        },
        created() {
            this.isLoading = true;
            this.$store.dispatch('loadEvents')
                .finally(() => this.isLoading = false);
        }

    }
</script>

<style lang="less">
    @import "../assets/_colors";

    .events-list{

        &__button{
            margin-bottom: 20px;
        }
    }
</style>