import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \App\Admin\Resources\Media\Pages\ListMedia::__invoke
* @see app/Admin/Resources/Media/Pages/ListMedia.php:7
* @route '/admin/{tenant}/media'
*/
const ListMedia = (args: { tenant: string | number | { uuid: string | number } } | [tenant: string | number | { uuid: string | number } ] | string | number | { uuid: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListMedia.url(args, options),
    method: 'get',
})

ListMedia.definition = {
    methods: ["get","head"],
    url: '/admin/{tenant}/media',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Admin\Resources\Media\Pages\ListMedia::__invoke
* @see app/Admin/Resources/Media/Pages/ListMedia.php:7
* @route '/admin/{tenant}/media'
*/
ListMedia.url = (args: { tenant: string | number | { uuid: string | number } } | [tenant: string | number | { uuid: string | number } ] | string | number | { uuid: string | number }, options?: RouteQueryOptions) => {
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

    return ListMedia.definition.url
            .replace('{tenant}', parsedArgs.tenant.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Admin\Resources\Media\Pages\ListMedia::__invoke
* @see app/Admin/Resources/Media/Pages/ListMedia.php:7
* @route '/admin/{tenant}/media'
*/
ListMedia.get = (args: { tenant: string | number | { uuid: string | number } } | [tenant: string | number | { uuid: string | number } ] | string | number | { uuid: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListMedia.url(args, options),
    method: 'get',
})

/**
* @see \App\Admin\Resources\Media\Pages\ListMedia::__invoke
* @see app/Admin/Resources/Media/Pages/ListMedia.php:7
* @route '/admin/{tenant}/media'
*/
ListMedia.head = (args: { tenant: string | number | { uuid: string | number } } | [tenant: string | number | { uuid: string | number } ] | string | number | { uuid: string | number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: ListMedia.url(args, options),
    method: 'head',
})

/**
* @see \App\Admin\Resources\Media\Pages\ListMedia::__invoke
* @see app/Admin/Resources/Media/Pages/ListMedia.php:7
* @route '/admin/{tenant}/media'
*/
const ListMediaForm = (args: { tenant: string | number | { uuid: string | number } } | [tenant: string | number | { uuid: string | number } ] | string | number | { uuid: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ListMedia.url(args, options),
    method: 'get',
})

/**
* @see \App\Admin\Resources\Media\Pages\ListMedia::__invoke
* @see app/Admin/Resources/Media/Pages/ListMedia.php:7
* @route '/admin/{tenant}/media'
*/
ListMediaForm.get = (args: { tenant: string | number | { uuid: string | number } } | [tenant: string | number | { uuid: string | number } ] | string | number | { uuid: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ListMedia.url(args, options),
    method: 'get',
})

/**
* @see \App\Admin\Resources\Media\Pages\ListMedia::__invoke
* @see app/Admin/Resources/Media/Pages/ListMedia.php:7
* @route '/admin/{tenant}/media'
*/
ListMediaForm.head = (args: { tenant: string | number | { uuid: string | number } } | [tenant: string | number | { uuid: string | number } ] | string | number | { uuid: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ListMedia.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

ListMedia.form = ListMediaForm

export default ListMedia