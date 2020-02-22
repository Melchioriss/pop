import Vue from 'vue'
import Router from 'vue-router'

Vue.use(Router);


const IndexPage = () => import( /* webpackChunkName: "index_page" */ './pages/Index.vue');
const MembersPage = () => import( /* webpackChunkName: "members_page" */ './pages/Members.vue');
const EventsPage = () => import( /* webpackChunkName: "events_page" */ './pages/Events.vue');
const NewEventPage = () => import( /* webpackChunkName: "new_event_page" */ './pages/CreateNewEvent.vue');
const EventPage = () => import( /* webpackChunkName: "event_detail" */ './pages/Event.vue');
const EventLeaderboardPage = () => import( /* webpackChunkName: "event_leaderboard" */ './pages/EventLeaderboard.vue');
const ActivityPage = () => import( /* webpackChunkName: "activity_feed" */ './pages/ActivityFeed.vue');
const TextFormattingPage = () => import( /* webpackChunkName: "text_formatting" */ './pages/TextFormatting.vue');
const AdminToolsPage = () => import( /* webpackChunkName: "admin_tools" */ './pages/AdminTools.vue');
const NotFoundPage = () => import( /* webpackChunkName: "not_found" */ './pages/NotFound.vue');


const appRouter = new Router({
    mode: 'history',
    routes: [
        {
            name: 'index',
            path: '/',
            component: IndexPage,
            meta: {title: 'Play or Pay | Index Page'}
        },
        {
            name: 'members',
            path: '/members',
            component: MembersPage,
            meta: {title: 'Play or Pay | Member List'}
        },
        {
            name: 'events',
            path: '/events',
            component: EventsPage,
            meta: {title: 'Play or Pay | Event List'}
        },
        {
            name: 'create_event',
            path: '/create-event',
            component: NewEventPage,
            meta: {title: 'Play or Pay | New Event Creation'}
        },
        {
            name: 'edit_event',
            path: '/edit-event/:eventUuid',
            component: NewEventPage,
            meta: {title: 'Play or Pay | Event Edit'}
        },
        {
            name: 'event',
            path: '/events/:eventUuid',
            component: EventPage,
            meta: {title: 'Play or Pay | Event'}
        },
        {
            name: 'event_leaderboard',
            path: '/events/:eventUuid/leaderboard',
            component: EventLeaderboardPage,
            meta: {title: 'Play or Pay | Event Leaderboard'}
        },
        {
            name: 'activity_feed',
            path: '/activity',
            component: ActivityPage,
            meta: {title: 'Play or Pay | Activity Feed'}
        },
        {
            name: 'text_formatting',
            path: '/text-formatting',
            component: TextFormattingPage,
            meta: {title: 'Play or Pay | Text Formatting Help'}
        },
        {
            name: 'admin_tools',
            path: '/admin',
            component: AdminToolsPage,
            meta: {title: 'Play or Pay | Admin Tools'}
        },
        {
            name: '404',
            path: '/404',
            alias: '*',
            component: NotFoundPage,
            meta: {title: 'Play or Pay | Page Not Found'}
        }
    ],
    scrollBehavior: function(to, from, savedPosition){

        if (savedPosition) {
            return savedPosition;
        } else {
            return { x: 0, y: 0 };
        }
    }
});


appRouter.beforeEach((to, from, next) => {
    if (to.meta.title !== undefined)
        document.title = to.meta.title;
    else
        document.title = 'Play or Pay';

    next();
});

export default appRouter;