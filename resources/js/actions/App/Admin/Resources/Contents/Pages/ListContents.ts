import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \App\Admin\Resources\Contents\Pages\ListContents::__invoke
* @see app/Admin/Resources/Contents/Pages/ListContents.php:7
* @route '/admin/{tenant}/contents'
*/
const ListContents = (args: { tenant: string | number | { uuid: string | number } } | [tenant: string | number | { uuid: string | number } ] | string | number | { uuid: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListContents.url(args, options),
    method: 'get',
})

ListContents.definition = {
    methods: ["get","head"],
    url: '/admin/{tenant}/contents',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Admin\Resources\Contents\Pages\ListContents::__invoke
* @see app/Admin/Resources/Contents/Pages/ListContents.php:7
* @route '/admin/{tenant}/contents'
*/
ListContents.url = (args: { tenant: string | number | { uuid: string | number } } | [tenant: string | number | { uuid: string | number } ] | string | number | { uuid: string | number }, options?: RouteQueryOptions) => {
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

    return ListContents.definition.url
            .replace('{tenant}', parsedArgs.tenant.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Admin\Resources\Contents\Pages\ListContents::__invoke
* @see app/Admin/Resources/Contents/Pages/ListContents.php:7
* @route '/admin/{tenant}/contents'
*/
ListContents.get = (args: { tenant: string | number | { uuid: string | number } } | [tenant: string | number | { uuid: string | number } ] | string | number | { uuid: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListContents.url(args, options),
    method: 'get',
})

/**
* @see \App\Admin\Resources\Contents\Pages\ListContents::__invoke
* @see app/Admin/Resources/Contents/Pages/ListContents.php:7
* @route '/admin/{tenant}/contents'
*/
ListContents.head = (args: { tenant: string | number | { uuid: string | number } } | [tenant: string | number | { uuid: string | number } ] | string | number | { uuid: string | number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: ListContents.url(args, options),
    method: 'head',
})

/**
* @see \App\Admin\Resources\Contents\Pages\ListContents::__invoke
* @see app/Admin/Resources/Contents/Pages/ListContents.php:7
* @route '/admin/{tenant}/contents'
*/
const ListContentsForm = (args: { tenant: string | number | { uuid: string | number } } | [tenant: string | number | { uuid: string | number } ] | string | number | { uuid: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ListContents.url(args, options),
    method: 'get',
})

/**
* @see \App\Admin\Resources\Contents\Pages\ListContents::__invoke
* @see app/Admin/Resources/Contents/Pages/ListContents.php:7
* @route '/admin/{tenant}/contents'
*/
ListContentsForm.get = (args: { tenant: string | number | { uuid: string | number } } | [tenant: string | number | { uuid: string | number } ] | string | number | { uuid: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ListContents.url(args, options),
    method: 'get',
})

/**
* @see \App\Admin\Resources\Contents\Pages\ListContents::__invoke
* @see app/Admin/Resources/Contents/Pages/ListContents.php:7
* @route '/admin/{tenant}/contents'
*/
ListContentsForm.head = (args: { tenant: string | number | { uuid: string | number } } | [tenant: string | number | { uuid: string | number } ] | string | number | { uuid: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ListContents.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

ListContents.form = ListContentsForm

export default ListContents