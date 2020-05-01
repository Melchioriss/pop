<template>
    <div>
        <h1 class="title">Activity Feed</h1>

        <loading-indicator v-if="isLoading" />

        <div v-else
            class="activity"
        >
            <template
                v-for="(activity, date) in activityByDate"
            >
                <div class="activity__date">{{date}}</div>
                <activity-item
                    v-for="activityItem in activity"
                    :key="'act_'+activityItem.uuid"
                    :activity="activityItem"
                />
            </template>
            <pagin-box
                v-if="pages > 1"
                class="activity__pagin"
                :currentPageNumber="currentPage"
                :maxPageNumber="pages"
                :use-route="true"
            />
        </div>

    </div>
</template>

<script>
    import {mapGetters} from 'vuex';
    import LoadingIndicator from "../components/LoadingIndicator";
    import ActivityItem from "../components/ActivityItem";
    import PaginBox from "../components/PaginBox";
    export default {
        name: "ActivityFeed",
        components: {PaginBox, ActivityItem, LoadingIndicator},
        props: {},
        data() {
            return {
                isLoading: false,
                currentPage: 1,
                pages: 1
            };
        },
        computed: {
            ...mapGetters({
                activityByDate: 'getActivityByDate'
            })
        },
        methods: {},
        created() {
            this.isLoading = true;
            this.currentPage = parseInt(this.$route.query.page) || 1;
            this.$store.dispatch('loadActivity', this.currentPage)
                .then((pagin) => {
                    this.currentPage = pagin.page;
                    this.pages = pagin.pages;
                })
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

            .dark-mode &{
                color: @color-cobalt-light;

                &:after{
                    background: @color-cobalt-light;
                }
            }
        }

    }

</style>