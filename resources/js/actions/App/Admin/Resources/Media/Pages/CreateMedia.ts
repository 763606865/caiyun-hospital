import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \App\Admin\Resources\Media\Pages\CreateMedia::__invoke
* @see app/Admin/Resources/Media/Pages/CreateMedia.php:7
* @route '/admin/{tenant}/media/create'
*/
const CreateMedia = (args: { tenant: string | number | { uuid: string | number } } | [tenant: string | number | { uuid: string | number } ] | string | number | { uuid: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: CreateMedia.url(args, options),
    method: 'get',
})

CreateMedia.definition = {
    methods: ["get","head"],
    url: '/admin/{tenant}/media/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Admin\Resources\Media\Pages\CreateMedia::__invoke
* @see app/Admin/Resources/Media/Pages/CreateMedia.php:7
* @route '/admin/{tenant}/media/create'
*/
CreateMedia.url = (args: { tenant: string | number | { uuid: string | number } } | [tenant: string | number | { uuid: string | number } ] | string | number | { uuid: string | number }, options?: RouteQueryOptions) => {
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

    return CreateMedia.definition.url
            .replace('{tenant}', parsedArgs.tenant.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Admin\Resources\Media\Pages\CreateMedia::__invoke
* @see app/Admin/Resources/Media/Pages/CreateMedia.php:7
* @route '/admin/{tenant}/media/create'
*/
CreateMedia.get = (args: { tenant: string | number | { uuid: string | number } } | [tenant: string | number | { uuid: string | number } ] | string | number | { uuid: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: CreateMedia.url(args, options),
    method: 'get',
})

/**
* @see \App\Admin\Resources\Media\Pages\CreateMedia::__invoke
* @see app/Admin/Resources/Media/Pages/CreateMedia.php:7
* @route '/admin/{tenant}/media/create'
*/
CreateMedia.head = (args: { tenant: string | number | { uuid: string | number } } | [tenant: string | number | { uuid: string | number } ] | string | number | { uuid: string | number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: CreateMedia.url(args, options),
    method: 'head',
})

/**
* @see \App\Admin\Resources\Media\Pages\CreateMedia::__invoke
* @see app/Admin/Resources/Media/Pages/CreateMedia.php:7
* @route '/admin/{tenant}/media/create'
*/
const CreateMediaForm = (args: { tenant: string | number | { uuid: string | number } } | [tenant: string | number | { uuid: string | number } ] | string | number | { uuid: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: CreateMedia.url(args, options),
    method: 'get',
})

/**
* @see \App\Admin\Resources\Media\Pages\CreateMedia::__invoke
* @see app/Admin/Resources/Media/Pages/CreateMedia.php:7
* @route '/admin/{tenant}/media/create'
*/
CreateMediaForm.get = (args: { tenant: string | number | { uuid: string | number } } | [tenant: string | number | { uuid: string | number } ] | string | number | { uuid: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: CreateMedia.url(args, options),
    method: 'get',
})

/**
* @see \App\Admin\Resources\Media\Pages\CreateMedia::__invoke
* @see app/Admin/Resources/Media/Pages/CreateMedia.php:7
* @route '/admin/{tenant}/media/create'
*/
CreateMediaForm.head = (args: { tenant: string | number | { uuid: string | number } } | [tenant: string | number | { uuid: string | number } ] | string | number | { uuid: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: CreateMedia.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

CreateMedia.form = CreateMediaForm

export default CreateMedia