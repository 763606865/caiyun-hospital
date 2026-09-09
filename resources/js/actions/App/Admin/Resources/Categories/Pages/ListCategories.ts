import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \App\Admin\Resources\Categories\Pages\ListCategories::__invoke
* @see app/Admin/Resources/Categories/Pages/ListCategories.php:7
* @route '/admin/{tenant}/categories'
*/
const ListCategories = (args: { tenant: string | number | { uuid: string | number } } | [tenant: string | number | { uuid: string | number } ] | string | number | { uuid: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListCategories.url(args, options),
    method: 'get',
})

ListCategories.definition = {
    methods: ["get","head"],
    url: '/admin/{tenant}/categories',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Admin\Resources\Categories\Pages\ListCategories::__invoke
* @see app/Admin/Resources/Categories/Pages/ListCategories.php:7
* @route '/admin/{tenant}/categories'
*/
ListCategories.url = (args: { tenant: string | number | { uuid: string | number } } | [tenant: string | number | { uuid: string | number } ] | string | number | { uuid: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { tenant: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'uuid' in args) {
        args = { tenant: args.uuid }
    }

    if (Array.isArray(args)) {
        args = {
            tenant: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        tenant: typeof args.tenant === 'object'
        ? args.tenant.uuid
        : args.tenant,
    }

    return ListCategories.definition.url
            .replace('{tenant}', parsedArgs.tenant.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Admin\Resources\Categories\Pages\ListCategories::__invoke
* @see app/Admin/Resources/Categories/Pages/ListCategories.php:7
* @route '/admin/{tenant}/categories'
*/
ListCategories.get = (args: { tenant: string | number | { uuid: string | number } } | [tenant: string | number | { uuid: string | number } ] | string | number | { uuid: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListCategories.url(args, options),
    method: 'get',
})

/**
* @see \App\Admin\Resources\Categories\Pages\ListCategories::__invoke
* @see app/Admin/Resources/Categories/Pages/ListCategories.php:7
* @route '/admin/{tenant}/categories'
*/
ListCategories.head = (args: { tenant: string | number | { uuid: string | number } } | [tenant: string | number | { uuid: string | number } ] | string | number | { uuid: string | number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: ListCategories.url(args, options),
    method: 'head',
})

export default ListCategories