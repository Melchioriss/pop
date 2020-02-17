<template>
    <div class="comment">
        <div class="comment__user-img-block">
            <img
                :src="user.avatar"
                :alt="user.profileName"
                class="comment__user-img"
            />
        </div>
        <div class="comment__body">
            <div class="comment__head-line">
                <div class="comment__user-name">{{user.profileName}}</div>
                <div
                    :title="$getExactDatetime(comment.createdAt)"
                    class="comment__date"
                >{{$getRelativeDate(comment.createdAt)}}</div>
            </div>
            <div>{{comment.text}}</div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "CommentItem",
        props: {
            comment: {
                type: Object,
                default: () => {
                    return {
                        user: '',
                        createdAt: '',
                        text: ''
                    }
                }
            }
        },
        data() {
            return {};
        },
        computed: {
            user: function () {
                return this.$store.getters.getUser(this.comment.user);
            }
        },
        methods: {}
    }
</script>

<style lang="less">
    @import "../assets/_colors";

    .comment{
        display: flex;
        padding-bottom: 10px;
        border-bottom: 1px solid @color-dark-orange;
        margin-bottom: 10px;

        &__user-img-block{
            width: 40px;
            flex-shrink: 0;
            margin-right: 10px;
        }

        &__user-img{
            width: 100%;
        }

        &__body{
            flex-grow: 1;
        }

        &__head-line{
            display: flex;
            align-items: baseline;
            margin-bottom: 6px;
        }

        &__user-name{
            font-weight: bold;
            font-size: 14px;
            margin-right: 20px;
        }

        &__date{
            color: @color-cobalt;
            font-size: 13px;
        }
    }

</style>