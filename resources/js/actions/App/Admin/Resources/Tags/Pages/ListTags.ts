import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \App\Admin\Resources\Tags\Pages\ListTags::__invoke
* @see app/Admin/Resources/Tags/Pages/ListTags.php:7
* @route '/admin/{tenant}/tags'
*/
const ListTags = (args: { tenant: string | number | { uuid: string | number } } | [tenant: string | number | { uuid: string | number } ] | string | number | { uuid: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListTags.url(args, options),
    method: 'get',
})

ListTags.definition = {
    methods: ["get","head"],
    url: '/admin/{tenant}/tags',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Admin\Resources\Tags\Pages\ListTags::__invoke
* @see app/Admin/Resources/Tags/Pages/ListTags.php:7
* @route '/admin/{tenant}/tags'
*/
ListTags.url = (args: { tenant: string | number | { uuid: string | number } } | [tenant: string | number | { uuid: string | number } ] | string | number | { uuid: string | number }, options?: RouteQueryOptions) => {
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

    return ListTags.definition.url
            .replace('{tenant}', parsedArgs.tenant.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Admin\Resources\Tags\Pages\ListTags::__invoke
* @see app/Admin/Resources/Tags/Pages/ListTags.php:7
* @route '/admin/{tenant}/tags'
*/
ListTags.get = (args: { tenant: string | number | { uuid: string | number } } | [tenant: string | number | { uuid: string | number } ] | string | number | { uuid: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListTags.url(args, options),
    method: 'get',
})

/**
* @see \App\Admin\Resources\Tags\Pages\ListTags::__invoke
* @see app/Admin/Resources/Tags/Pages/ListTags.php:7
* @route '/admin/{tenant}/tags'
*/
ListTags.head = (args: { tenant: string | number | { uuid: string | number } } | [tenant: string | number | { uuid: string | number } ] | string | number | { uuid: string | number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: ListTags.url(args, options),
    method: 'head',
})

export default ListTags