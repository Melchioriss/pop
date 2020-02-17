<template>
    <div class="comments">
        <comment-item
            v-for="comment in comments"
            :comment="comment"
        />

        <div
            v-if="canComment && isShowingReplyForm"
            class="form"
        >
            <textarea
                v-model="commentText"
                class="input input--textarea input--space-bottom"
                placeholder="Your thoughts..."
                rows="3"
            ></textarea>
            <button
                @click="addComment"
                type="button"
                class="button"
            >Reply</button>
        </div>
        <span
            v-else-if="canComment"
            @click="showReplyForm"
            class="edit-link edit-link--comments-show"
        >Show Reply Form</span>
    </div>
</template>

<script>
    import CommentItem from "./CommentItem";
    export default {
        name: "CommentsArea",
        components: {CommentItem},
        props: {
            comments: {
                type: Array,
                default: []
            },
            canComment: {
                type: Boolean,
                default: false
            }
        },
        data() {
            return {
                isShowingReplyForm: false,
                commentText: ''
            };
        },
        computed: {
            commentsCount: function () {
                return this.comments.length;
            }
        },
        watch: {
            commentsCount: function () {
                this.isShowingReplyForm = false;
                this.commentText = '';
            }
        },
        methods: {
            showReplyForm () {
                this.isShowingReplyForm = true;
            },

            addComment () {
                this.$emit('add-comment', this.commentText);
            }
        }
    }
</script>

<style lang="less">
    @import "../assets/_colors";

    .comments{
        margin-bottom: 10px;
    }

</style>