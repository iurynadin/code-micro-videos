// exact p evitar colizão de rotas
import { RouteProps } from "react-router-dom";
import Dashboard from "../pages/Dashboard";
import CategoryList from "../pages/category/PageList";
import CategoryCreate from "../pages/category/PageForm";
import CastMemberList from "../pages/cast-member/PageList";
import CastMemberCreate from "../pages/cast-member/PageForm";
import GenreList from "../pages/genre/PageList";
import GenreCreate from "../pages/genre/PageForm";

export interface MyRouteProps extends RouteProps{
    name: string;
    label: string;
}

const routes: MyRouteProps[] = [
    {
        name: 'dashboard',
        label: 'Dashboard',
        path: '/',
        component: Dashboard,
        exact: true,
    },
    {
        name: 'categories.list',
        label: 'Categorias',
        path: '/categories',
        component: CategoryList,
        exact: true,
    },
    {
        name: 'categories.create',
        label: 'Criar Categorias',
        path: '/categories/create',
        component: CategoryCreate,
        exact: true,
    },
    {
        name: 'cast_members.list',
        label: 'Membros do elenco',
        path: '/cast-members',
        component: CastMemberList,
        exact: true,
    },
    {
        name: 'cast_members.create',
        label: 'Criar membros de elencos',
        path: '/cast-members/create',
        component: CastMemberCreate,
        exact: true,
    },
    {
        name: 'genres.list',
        label: 'Gêneros',
        path: '/genres',
        component: GenreList,
        exact: true,
    },
    {
        name: 'genres.create',
        label: 'Criar gêneros',
        path: '/genres/create',
        component: GenreCreate,
        exact: true,
    }
];

export default routes;
