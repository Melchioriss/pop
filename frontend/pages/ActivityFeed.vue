<template>
    <div>
        <h1 class="title">Activity Feed</h1>

        <loading-indicator v-if="isLoading" />

        <div v-else
            class="activity"
        >
            <template
                v-for="(logs, date) in logsByDate"
            >
                <div class="activity__date">{{date}}</div>
                <activity-item
                    v-for="log in logs"
                    :key="log.uuid"
                    :log="log"
                />
            </template>

        </div>

    </div>
</template>

<script>
    import {mapState, mapGetters} from 'vuex';
    import LoadingIndicator from "../components/LoadingIndicator";
    import ActivityItem from "../components/ActivityItem";
    export default {
        name: "ActivityFeed",
        components: {ActivityItem, LoadingIndicator},
        props: {},
        data() {
            return {
                isLoading: false,
                pageNum: 1
            };
        },
        computed: {
            ...mapGetters({
                logsByDate: 'getLogsByDate'
            })
        },
        methods: {},
        created() {
            this.isLoading = true;
            this.pageNum = parseInt(this.$route.query.page) || 1;
            this.$store.dispatch('loadLogs', this.pageNum)
                .finally(() => this.isLoading = false);
        }
    }
</script>

<style lang="less">
    @import "../assets/_colors";

    .activity{

        &__date{
            color: @color-cobalt;
            font-weight: bold;
            margin: 20px 0;
            display: flex;
            align-items: center;

            &:after{
                content: '';
                display: block;
                background: @color-cobalt;
                height: 3px;
                flex-grow: 1;
                margin-left: 12px;
            }
        }

    }

</style>