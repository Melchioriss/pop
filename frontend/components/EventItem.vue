<template>
    <div class="event-item">
        <router-link :to="{name: 'event', params: {eventUuid: event.uuid}}" class="event-item__name">{{event.name}}</router-link>
        <div
            v-if="event.description"
            v-html="$getMarkedContent(event.description)"
            class="event-item__description text"
        ></div>
        <div class="event-item__dates">
            <span>{{$getExactDate(event.activePeriod.startDate)}} &mdash; {{$getExactDate(event.activePeriod.endDate)}}</span>
        </div>
        

    </div>
</template>

<script>
    export default {
        name: "EventItem",
        props: {
            event: {
                type: Object,
                default: () => {
                    return {
                        uuid: '',
                        name: '',
                        description: '',
                        activePeriod: {
                            startDate: '',
                            endDate: ''
                        },
                        groupId: ''
                    };
                }
            }
        },
        computed: {}
    }
</script>

<style lang="less">
    @import "../assets/_colors";

    .event-item{
        border: 2px solid @color-cobalt;
        padding: 14px;
        margin-bottom: 20px;

        .dark-mode &{
            color: @color-cobalt-light;
        }

        &__name{
            font-size: 18px;
            font-weight: bold;
            color: @color-dark-orange;
            display: inline-block;
            text-decoration: none;
            margin-bottom: 6px;
        }

        &__description{
            font-size: 13px;
            color: @color-cobalt;
            margin-bottom: 10px;

            .dark-mode &{
                color: @color-light-text;
            }
        }

        &__dates{
            font-size: 14px;
            margin-bottom: 6px;

            .dark-mode &{
                color: @color-cobalt-light;
            }
        }
    }

</style>